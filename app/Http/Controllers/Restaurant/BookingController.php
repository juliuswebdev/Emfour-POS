<?php

namespace App\Http\Controllers\Restaurant;

use App\BusinessLocation;
use App\Business;
use App\Contact;
use App\CustomerGroup;
use App\Product;
use App\Variation;
use App\Transaction;
use App\Restaurant\Booking;
use App\Restaurant\BookingDetail;
use App\User;
use App\Utils\RestaurantUtil;
use App\Utils\ContactUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Str;
use Mail;
use Crypt;

class BookingController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $restUtil;

    protected $contactUtil;

    public function __construct(Util $commonUtil, RestaurantUtil $restUtil, ContactUtil $contactUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
        $this->contactUtil = $contactUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('crud_all_bookings') && ! auth()->user()->can('crud_own_bookings')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $user_id = request()->has('user_id') ? request()->user_id : null;
        if (! auth()->user()->hasPermissionTo('crud_all_bookings') && ! $this->restUtil->is_admin(auth()->user(), $business_id)) {
            $user_id = request()->session()->get('user.id');
        }
        if (request()->ajax()) {
            $filters = [
                'start_date' => request()->start,
                'end_date' => request()->end,
                'user_id' => $user_id,
                'location_id' => ! empty(request()->location_id) ? request()->location_id : null,
                'business_id' => $business_id,
            ];

            $events = $this->restUtil->getBookingsForCalendar($filters);

            return $events;
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        $customers = Contact::customersDropdown($business_id, false);

        $correspondents = User::forDropdown($business_id, false);

        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business = Business::find($business_id);
      

        return view('restaurant.booking.index', compact('business', 'business_locations', 'customers', 'correspondents', 'types', 'customer_groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('crud_all_bookings') && ! auth()->user()->can('crud_own_bookings')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if ($request->ajax()) {
                $business_id = request()->session()->get('user.business_id');
                $user_id = request()->session()->get('user.id');
                
                $input = $request->input();
                $booking_start = $this->commonUtil->uf_date($input['booking_start'], true);
                $booking_end = $this->commonUtil->uf_date($input['booking_end'], true);
                $date_range = [$booking_start, $booking_end];

                //Check if booking is available for the required input
                $query = Booking::where('business_id', $business_id)
                                ->where('location_id', $input['location_id'])
                                ->where('contact_id', $input['contact_id'])
                                ->where(function ($q) use ($date_range) {
                                    $q->whereBetween('booking_start', $date_range)
                                    ->orWhereBetween('booking_end', $date_range);
                                });

                if (isset($input['res_table_id'])) {
                    $query->where('table_id', $input['res_table_id']);
                }

                $existing_booking = $query->first();
                if (empty($existing_booking)) {
                    $input['business_id'] = $business_id;
                    $input['created_by'] = $user_id;
                    $input['booking_start'] = $booking_start;
                    $input['booking_end'] = $booking_end;
                    $booking = Booking::createBooking($input);

                    $business_location = BusinessLocation::find($input['location_id']);
                    $business_location_id = $business_location->location_id ?? 'NA';

                    $booking_detail_input['booking_id'] = $booking->id;
                    $booking_detail_input['product_id'] = '';
                    $booking_detail_input['ref_no'] = $business_location_id .'-'. $booking->id;
                    $booking_detail_input['time'] = date('h:m a');
                    $booking_detail_input['full_name'] = '';
                    $booking_detail_input['phone'] = '';
                    $booking_details = BookingDetail::create($booking_detail_input);

                    
                    // $contact = [];
                    // $contact['']

                    $output = ['success' => 1,
                        'msg' => trans('lang_v1.added_success'),
                    ];

                    //Send notification to customer
                    if (isset($input['send_notification']) && $input['send_notification'] == 1) {
                        $output['send_notification'] = 1;
                        $output['notification_url'] = action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], ['transaction_id' => $booking->id, 'template_for' => 'new_booking']);
                    }
                } else {
                    $time_range = $this->commonUtil->format_date($existing_booking->booking_start, true).' ~ '.
                                    $this->commonUtil->format_date($existing_booking->booking_end, true);

                    $output = ['success' => 0,
                        'msg' => trans(
                            'restaurant.booking_not_available',
                            ['customer_name' => $existing_booking->customer->name,
                                'booking_time_range' => $time_range, ]
                        ),
                    ];
                }
            } else {
                exit(__('messages.something_went_wrong'));
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $booking = Booking::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(['table', 'customer', 'correspondent', 'waiter', 'location', 'booking_details'])
                                ->first();
           
            $services = Product::whereIn('id', explode(',', $booking->booking_details->product_id))->select('name')->get();
            if (! empty($booking)) {
                $booking_start = $this->commonUtil->format_date($booking->booking_start, true);
                $booking_end = $this->commonUtil->format_date($booking->booking_end, true);

                $services = Variation::whereIn('id', explode(',', $booking->booking_details->product_id))->with(['product'])->get();
                $services_text = '';
                foreach($services as $service) {
                    $variation_name = '';
                    if($service->name != 'DUMMY') {
                        $variation_name = '['.$service->name.']';
                    }
                    $services_text .= $service->product->name.' '.$variation_name.',';  
                }
                $services = substr($services_text, 0, -2);
                $booking_statuses = [
                    'waiting' => __('lang_v1.waiting'),
                    'booked' => __('restaurant.booked'),
                    'checkin' => __('restaurant.checkin'),
                    'checkout' => __('restaurant.checkout'),
                    'completed' => __('restaurant.completed'),
                    'cancelled' => __('restaurant.cancelled'),
                ];

                $business_locations = BusinessLocation::forDropdown($business_id);
                $customers = CustomerGroup::forDropdown($business_id);
                $correspondents = User::forDropdown($business_id, false);

                return view('restaurant.booking.show', compact('business_locations', 'correspondents', 'customers', 'services', 'booking', 'booking_start', 'booking_end', 'booking_statuses'));
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('crud_all_bookings') && ! auth()->user()->can('crud_own_bookings')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = $request->session()->get('user.business_id');
            $booking = Booking::where('business_id', $business_id)
                                ->with(['booking_details', 'customer'])
                                ->find($id);

            if($request->booking_status == 'cancelled' && $booking->booking_status == 'completed') {

                $output = ['success' => 0,
                            'msg' => __('lang_v1.unable_cancel'),
                ];

            } else {

                if (! empty($booking)) {
                    $booking->booking_status = $request->booking_status;
                    $booking->save();
                }
                $output = ['success' => 1,
                    'msg' => trans('lang_v1.updated_success'),
                    'booking' => $booking
                ];

            }

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('crud_all_bookings') && ! auth()->user()->can('crud_own_bookings')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = request()->session()->get('user.business_id');
            $booking = Booking::where('business_id', $business_id)
                                ->where('id', $id)
                                ->delete();
            $booking_details = BookingDetail::where('booking_id', $id)
                ->delete();
            $output = ['success' => 1,
                'msg' => trans('lang_v1.deleted_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Retrieves todays bookings
     *
     * @param  \App\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function getTodaysBookings()
    {
        if (! auth()->user()->can('crud_all_bookings') && ! auth()->user()->can('crud_own_bookings')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $today = \Carbon::now()->format('Y-m-d');
            $query = Booking::where('business_id', $business_id)
                        //->where('booking_status', 'booked')
                        ->whereDate('booking_start', $today)
                        ->whereIn('booking_status', ['waiting', 'booked', 'checkin', 'checkout', 'cancelled', 'completed'])
                        ->with(['table', 'customer', 'correspondent', 'waiter', 'location', 'booking_details']);

            if (! empty(request()->location_id)) {
                $query->where('location_id', request()->location_id);
            }

            if (! auth()->user()->hasPermissionTo('crud_all_bookings') && ! $this->commonUtil->is_admin(auth()->user(), $business_id)) {
                $query->where(function ($query) use ($user_id) {
                    $query->where('created_by', $user_id)
                        ->orWhere('correspondent_id', $user_id)
                        ->orWhere('waiter_id', $user_id);
                });

                //$query->where('created_by', $user_id);
            }

            $query->orderBy('created_at', 'desc');

            return Datatables::of($query)
                ->editColumn('table', function ($row) {
                    return ! empty($row->table->name) ? $row->table->name : '--';
                })
                ->editColumn('customer', function ($row) {
                    return ! empty($row->customer->name) ? $row->customer->name : '--';
                })
                ->editColumn('correspondent', function ($row) {
                    return ! empty($row->correspondent->user_full_name) ? $row->correspondent->user_full_name : '--';
                })
                ->editColumn('waiter', function ($row) {
                    return ! empty($row->waiter->user_full_name) ? $row->waiter->user_full_name : '--';
                })
                ->editColumn('location', function ($row) {
                    return ! empty($row->location->name) ? $row->location->name : '--';
                })
                ->editColumn('booking_start', function ($row) {
                    return $this->commonUtil->format_date($row->booking_start, true);
                })
                ->editColumn('booking_end', function ($row) {
                    return $this->commonUtil->format_date($row->booking_end, true);
                })
                ->addColumn('ref_no', function($row) {
                    return $row->booking_details->ref_no;
                })
                ->addColumn('time', function ($row) {
                    return $row->booking_details->time ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    if($row->booking_status  == 'waiting') {
                        $type = 'bg-light-blue';
                        $text = __("lang_v1.waiting");
                    } else if ($row->booking_status  == 'booked') {
                        $type = 'bg-blue';
                        $text = __('restaurant.booked');
                    } else if ($row->booking_status  == 'checkin') {
                        $type = 'bg-yellow';
                        $text = __('restaurant.checkin');
                    } else if ($row->booking_status  == 'checkout') {
                        $type = 'bg-gray';
                        $text = __('restaurant.checkout');
                    }  else if ($row->booking_status  == 'completed') {
                        $type = 'bg-green';
                        $text = __('restaurant.completed');
                    } else if ($row->booking_status  == 'cancelled') {
                        $type = 'bg-black';
                        $text = __('restaurant.cancelled');
                    } else {
                        $type = 'bg-default';
                        $text = 'N/A';
                    }
                    return '
                        <div class="external-event '.$type.' text-center" style="position: relative;">
                        <small>'.$text.'</small>
                        </div>
                    ';
                })
                ->addColumn('actions', function ($row) {
                    return '
                        <a href="'.action([\App\Http\Controllers\Restaurant\BookingController::class, 'show'], [$row->id]).'" data-href="'.action([\App\Http\Controllers\Restaurant\BookingController::class, 'show'], [$row->id]).'" class="text-center btn-modal" style="position: relative;" data-container=".view_modal">
                        <i class="fa fas fa-edit"></i>
                        </a>
                    '; 
                })
                ->rawColumns(['status', 'actions'])
                ->removeColumn('id')
                ->make(true);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBookings($slug)
    {
        if (! auth()->user()->can('crud_all_bookings') && ! auth()->user()->can('crud_own_bookings')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            //$business_id = request()->session()->get('user.business_id');
            $business = Business::where('slug', $slug)->first();

            $from = request()->from;
            $booking = Booking::where('bookings.business_id', $business->id)
                    ->leftJoin('bookings_details', 'bookings_details.booking_id', '=', 'bookings.id')
                    ->whereIn('bookings.booking_status', explode(',',$from))
                    ->where('bookings_details.ref_no', request()->search_query)
                    ->orWhere('bookings_details.phone', request()->search_query)
                    ->with(['table', 'customer', 'correspondent', 'waiter', 'location', 'booking_details'])
                    ->first();

            if($booking) {
                $services = Variation::whereIn('id', explode(',', $booking->booking_details->product_id))->with(['product'])->get();
                $services_text = '';
                foreach($services as $service) {
                    $variation_name = '';
                    if($service->name != 'DUMMY') {
                        $variation_name = '['.$service->name.']';
                    }
                    $services_text .= $service->product->name.' '.$variation_name.',';  
                }
                $services = substr($services_text, 0, -2);
                if($from == 'booked' || $from == 'booked,waiting') {
                    return view('restaurant.booking.checkin-result', compact('booking', 'services'));
                } else if( $from == 'checkin') {
                    return view('restaurant.booking.checkout-result', compact('booking', 'services'));
                } else {
                    return 'No Result Found!';
                }
            } else {
                return 'No Result Found!';
            }
            
        }
    }

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPublicBooking($slug)
    {
     
        $business = Business::where('slug', $slug)->with(['currency'])->first();
        if (!$business) {
            abort(403, 'Unauthorized action.');
        }

        $business_info = BusinessLocation::where('business_id', $business->id)->first();

        $business_locations = BusinessLocation::where('business_id', $business->id)->get();

        $correspondents = User::forDropdown($business->id, false);

        $services = Product::where('business_id', $business->id)->where('enable_stock', 0)->where('type', '<>', 'modifier')->with(['variations'])->get();

        return view('restaurant.booking.public', compact('business', 'business_info', 'business_locations', 'correspondents', 'services'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postPublicBooking(Request $request)
    {
        // try {
            $input = $request->input();
            $business_location = BusinessLocation::find($input['location_id']);
            $business_id = $business_location->business_id ?? 0;
            $business = Business::find($business_id);

            $business_location_id = $business_location->location_id ?? 'NA';
            
            $user = User::where('business_id', $business_id)->first();
            $user_id = $user->id ?? 0;

            $booking_start = $input['booking_time'];
            $booking_end = $input['booking_time'];

            $date_temp = strtotime($booking_start);
            $time = date('H:i:s', $date_temp);
            
            $time_details = 'Start: '. $booking_start. ' , End: ' .$booking_end. ', @ '.$time;
            $full_name = $input['first_name']. ' ' .$input['last_name'];

            //$booking_start = $this->commonUtil->uf_date($input['booking_start'], true);
            //$booking_end = $this->commonUtil->uf_date($input['booking_end'], true);
            //$time = $input['time'];
            //$time_details = 'Start: '. $booking_start. ' , End: ' .$booking_end. ', @ '.$time;

            $full_name = $input['first_name']. ' ' .$input['last_name'];
            $contact = Contact::where('email', $input['email'])->first();
            if(!$contact) {
                $contact_input['business_id'] = $business_id;
                $contact_input['type'] = 'customer';
                $contact_input['name'] = $full_name;
                $contact_input['first_name'] = $input['first_name'];
                $contact_input['last_name'] = $input['last_name'];
                $contact_input['email'] = $input['email'];
                $contact_input['mobile'] = $input['phone'];
                $contact_input['created_by'] = $user_id;   
                $contact = $this->contactUtil->createNewContact($contact_input);    
                $contact = $contact['data'];         
            }

            $input['contact_id'] = $contact->id;
            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;
            $input['booking_time'] = $booking_time;
            //$input['booking_start'] = $booking_start;
            //$input['booking_end'] = $booking_end;
            $input['correspondent'] = $input['staff'];
            $input['booking_note'] = $input['booking_note'];
            $input['booking_status'] = 'waiting';
            $booking = Booking::createBooking($input);

            $ref_no = $business_location_id .'-'. $booking->id;
            $slug = $business->slug;

            $booking_detail_input['booking_id'] = $booking->id;
            $booking_detail_input['product_id'] = implode(",", $input['services']);
            $booking_detail_input['ref_no'] = $ref_no;
            $booking_detail_input['time'] = $time;
            $booking_detail_input['full_name'] = $full_name;
            $booking_detail_input['phone'] = $input['phone'];
            $booking_detail_input['email'] = $input['email'];
            $booking_details = BookingDetail::create($booking_detail_input);
            

            $services = Variation::whereIn('id', explode(',', $booking_details->product_id))->with(['product'])->get();
            $services_text = '';
            foreach($services as $service) {
                $variation_name = '';
                if($service->name != 'DUMMY') {
                    $variation_name = '['.$service->name.']';
                }
                $services_text .= $service->product->name.' '.$variation_name.',';  
            }

            $booking_id = Crypt::encrypt($booking->id);

            $business_name = $business->name;
            
            $from = $business->email_settings['mail_from_address'];
            $to = $input['email'];
            $subject = $ref_no." - ".$business_name." - Booking";
            $content = '<p>Dear '.$full_name.',</p>
            <p>Your booking is confirmed</p>
            <p>Ref No: '.$ref_no.'</p>
            <p>Date: '.$booking_start.' to '.$booking_end.' @ '. $time .'</p>
            <p>Location: '.$business_location->name.'</p>
            <p>Services: '.substr($services_text, 0, -1).'</p>';
            $data = [ 
                'name' => $full_name,
                'to' => $to,
                'from' => $from,
                'subject' => $subject,
                'business_name' => $business_name,
                'content' => $content
            ];
            Mail::send('restaurant.booking.mail', $data, function($message) use ($data) {
                $message->to($data['to'], $data['subject']);
                $message->subject($data['subject']);
                $message->from('information@emfoursystem.com',$data['business_name']);
            });
            
            return redirect()->back()->with(
                [
                    'status' => true,
                    'booking' => $booking,
                    'booking_details' => $booking_details,
                    'time_details' => $time_details,
                    'services' => substr($services_text, 0, -1)
                ]
            );
        // } catch (\Exception $e) {
        //     return redirect()->back()->with(
        //         [
        //             'status' => false
        //         ]
        //     );
        // }

    }



    public function getPublicBookingCheckin(Request $request, $slug)
    {

        $business = Business::where('slug', $slug)->with(['currency'])->first();

        if (!$business) {
            abort(403, 'Unauthorized action.');
        }

        return view('restaurant.booking.public-checkin', compact('slug', 'business'));
    }

    public function getPublicBookingCheckinUpdate(Request $request, $slug, $booking_id)
    {
        $booking_id = Crypt::decrypt($booking_id);
        $booking = Booking::find($booking_id);      
        if (!$booking) {
            abort(403, 'Unauthorized action.');
        }
        $booking->booking_status = 'checkin';
        $booking->save();
        return redirect()->back()->with(
            [
                'status' => 'Success Checkin!',
            ]
        );
    }

    public function getLocations($id) {
        $locations = BusinessLocation::where('business_id', $id)->get();
        return response($locations, 200);
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPublicBookingAPI(Request $request)
    {
        try {
            $input = $request->input();
            $business_location = BusinessLocation::find($input['location_id']);
            $business_id = $business_location->business_id ?? 0;

            $business_location_id = $business_location->location_id ?? 'NA';

            $user = User::where('business_id', $business_id)->first();
            $user_id = $user->id ?? 0;

            $booking_start = date('Y-m-d', strtotime($input['date'])) .' '. date('H:i:s', strtotime($input['when']));
            $booking_end = date('Y-m-d', strtotime($input['date'])) .' '. date('H:i:s', strtotime($input['until']));

   
            $contact = Contact::where('mobile', $input['phone'])->first();
            if(!$contact) {
                $contact_input['business_id'] = $business_id;
                $contact_input['type'] = 'customer';
                $contact_input['name'] = $input['full_name'];
                $contact_input['mobile'] = $input['phone'];
                $contact_input['created_by'] = $user_id;   
                $contact = $this->contactUtil->createNewContact($contact_input);   
                $contact = $contact['data'];
            }
          
            $input['contact_id'] = $contact->id;
            $input['business_id'] = $business_id;
            $input['location_id'] = $input['location_id'];
            $input['created_by'] = $user_id;
            $input['booking_start'] = $booking_start;
            $input['booking_end'] = $booking_end;
            $input['booking_note'] = $input['note'];
            $input['booking_status'] = 'waiting';
            $booking = Booking::createBooking($input);


            $product_id = array(
                'wpc_booked_ids' => $input['wpc_booked_ids'],
                'wpc_booked_table_ids' => $input['wpc_booked_table_ids']
            );

            $booking_detail_input['booking_id'] = $booking->id;
            $booking_detail_input['product_id'] = json_encode($product_id);
            $booking_detail_input['ref_no'] = $input['invoice_id'].'-'.$input['location_id'];
            $booking_detail_input['full_name'] = $input['full_name'];
            $booking_detail_input['phone'] = $input['phone'];
            $booking_details = BookingDetail::create($booking_detail_input);
            
            $return = [
                    'status' => true,
                    'booking' => $booking,
                    'booking_details' => $booking_details,     
            ];
            return response($return, 200);
        } catch (\Exception $e) {
            $return = [
                    'status' => false
            ];
            return response($return, 401);
        } 
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */

    public function loadTableMapping(Request $request)
    {
        $location_id = $request->input('location_id');
        $business_location = BusinessLocation::find($location_id);
        return view('restaurant.booking.table', compact('business_location'));
    }

    public function loadTableChairSelected(Request $request) {
        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->input('location_id');
        $transactions = Transaction::where('business_id', $business_id)->where('location_id', $location_id)->where('payment_status', 'due')->select('table_chair_selected')->get();
        $arr = [];
        foreach($transactions as $item1) {
            if($item1->table_chair_selected && $item1->table_chair_selected != 'null') {
                $i = $item1->table_chair_selected;
                $arr_temp = json_decode(json_decode($i, true), true);
                foreach($arr_temp as $item2) {
                    if(!in_array($item2, $arr)) {
                        array_push($arr, $item2);
                    }
                }
            }
        }
        return response($arr, 200);
    }


    //This function is used for get occupied chair ids.
    public function getOccupiedTableChairs(Request $request) {
        $business_id = request()->session()->get('user.business_id');
        $location_id = $request->input('location_id');
        $res_table_ids = Transaction::where('res_table_id','!=', null)->where('business_id', $business_id)->where('location_id', $location_id)->where('payment_status', 'due')->pluck('res_table_id')->toArray();
        $res_table_ids = (empty($res_table_ids)) ? [] : array_map('strval', $res_table_ids);
        return response($res_table_ids, 200);
    }

}
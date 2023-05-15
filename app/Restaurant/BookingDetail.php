<?php

namespace App\Restaurant;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    //Allowed booking statuses ('waiting', 'booked', 'completed', 'cancelled')

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = 'bookings_details';
  
 
}

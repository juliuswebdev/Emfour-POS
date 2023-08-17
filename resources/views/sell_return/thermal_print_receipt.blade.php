<!-- business information here -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- <link rel="stylesheet" href="style.css"> -->
        <title>Receipt-{{$receipt_details->invoice_no}}</title>
    </head>
    <body>
        
        <div class="ticket">
            @if(empty($receipt_details->letter_head))

                @if(!empty($receipt_details->logo))
                    <div class="text-box centered">
                        <img style="max-height: 100px; width: auto;" src="{{$receipt_details->logo}}" alt="Logo">
                    </div>
                @endif
                <div class="text-box">
                    <!-- Logo -->
                    <p class="centered">
                        <!-- Header text -->
                        @if(!empty($receipt_details->header_text))
                            <span class="headings">{!! $receipt_details->header_text !!}</span>
                            <br/>
                        @endif
                        <!-- business information here -->
                        @if(!empty($receipt_details->display_name))
                            <span class="headings">
                                {{$receipt_details->display_name}}
                            </span>
                            <br/>
                        @endif
                        
                        @if(!empty($receipt_details->address))
                            {!! $receipt_details->address !!}
                            <br/>
                        @endif
    
                        @if(!empty($receipt_details->contact))
                            {!! $receipt_details->contact !!}
                        @endif
                        @if(!empty($receipt_details->contact) && !empty($receipt_details->website))
                            , 
                        @endif
                        @if(!empty($receipt_details->website))
                            {{ $receipt_details->website }}
                        @endif
                        @if(!empty($receipt_details->location_custom_fields))
                            <br>{{ $receipt_details->location_custom_fields }}
                        @endif
    
                        @if(!empty($receipt_details->sub_heading_line1))
                            {{ $receipt_details->sub_heading_line1 }}<br/>
                        @endif
                        @if(!empty($receipt_details->sub_heading_line2))
                            {{ $receipt_details->sub_heading_line2 }}<br/>
                        @endif
                        @if(!empty($receipt_details->sub_heading_line3))
                            {{ $receipt_details->sub_heading_line3 }}<br/>
                        @endif
                        @if(!empty($receipt_details->sub_heading_line4))
                            {{ $receipt_details->sub_heading_line4 }}<br/>
                        @endif		
                        @if(!empty($receipt_details->sub_heading_line5))
                            {{ $receipt_details->sub_heading_line5 }}<br/>
                        @endif
    
                        @if(!empty($receipt_details->tax_info1))
                            <br><b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
                        @endif
    
                        @if(!empty($receipt_details->tax_info2))
                            <b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
                        @endif

                    </p>
                </div>
            @else
                <div class="text-box">
                    <img style="width: 100%;margin-bottom: 10px;" src="{{$receipt_details->letter_head}}">
                </div>
            @endif
            
            <div class="centered">**************************************************</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_invoice_no')</strong></p>
				<p class="f-right">
					{{$receipt_details->invoice_no}}
				</p>
			</div>
			<div class="textbox-info">
				<p class="f-left"><strong>{!! $receipt_details->date_label !!}</strong></p>
				<p class="f-right">
					{{$receipt_details->invoice_date}}
				</p>
			</div>

            <!-- customer info -->
	        <div class="textbox-info">
	        	<p style="vertical-align: top;"><strong>
	        		{{$receipt_details->customer_label ?? ''}}
	        	</strong></p>

	        	<p>
	        		@if(!empty($receipt_details->customer_info))
	        			<div class="bw">
						{!! $receipt_details->customer_info !!}
						</div>
					@endif
	        	</p>
	        </div>
			
			@if(!empty($receipt_details->client_id_label))
				<div class="textbox-info">
					<p class="f-left"><strong>
						{{ $receipt_details->client_id_label }}
					</strong></p>
					<p class="f-right">
						{{ $receipt_details->client_id }}
					</p>
				</div>
			@endif
			
            <table style="margin-top: 10px !important" class="border-bottom width-100 table-f-12 mb-10">
                <thead class="border-bottom-dotted">
                    <tr>
                        <th class="serial_number">#</th>
                        <th class="description" width="40%">
                        	{{$receipt_details->table_product_label}}<br>
                        </th>
                       
                        @if(!empty($receipt_details->table_unit_price_label))
							<th class="text-right" width="30%">
								{{$receipt_details->table_unit_price_label}}
							</th>
						@endif
                       
                        <th class="price text-right"  width="30%">{{$receipt_details->table_subtotal_label}}</th>
                    </tr>
                </thead>
                <tbody>
                	@forelse($receipt_details->lines as $line)
	                    <tr>
	                        <td class="serial_number" style="vertical-align: top;">
	                        	{{$loop->iteration}}
	                        </td>
	                        <td class="description">
                                {{$line['name']}} {{$line['variation']}} 
                                @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif 
                                @if(!empty($line['brand'])), {{$line['brand']}} @endif
                                @if(!empty($line['sell_line_note']))({{$line['sell_line_note']}}) @endif 
                                <br>
                                <span>
                                    @lang('lang_v1.thermal_receipt_small_qty') :    
                                    {{$line['quantity']}} {{$line['units']}}
                                </span>
	                        </td>
                            <td class="text-right">
                                {{$line['unit_price_exc_tax']}}
                            </td>
                            <td class="text-right">
                                {{$line['line_total']}}
                            </td>
	                    </tr>
	                    
                    @endforeach
                    
                </tbody>
            </table>
			

            <div class="textbox-info">
                <p class="f-left sub-headings">
                    @lang('lang_v1.receipt_total_return')
                </p>
                <p class="f-right sub-headings">
                    {{$receipt_details->subtotal}}
                </p>
            </div>



            @if($receipt_details->payment_response_json != null)
            <div class="textbox-info">
                <div class="centered">**************************************************</div>
            </div>
            @php
                $payment_response_json = json_decode($receipt_details->payment_response_json, true);
                $ext_data = explode(',', $payment_response_json['response']['ExtData']);
                $ex_amount = $ext_data[0];
                $ex_x_amount = explode('=', $ex_amount);
                $total_amount = end($ex_x_amount);
                
                $ex_account_number = $ext_data[7];
                $ex_x_account_number  = explode('=', $ex_account_number);
                $account_number  = end($ex_x_account_number);

                $ex_cart = $ext_data[2];
                $ex_x_cart = explode('=', $ex_cart);
                $card_issue_by = end($ex_x_cart);
            @endphp

            <div class="textbox-info">
				<p class="f-left"><strong>{{ $card_issue_by }} Card</strong></p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_account_number')</strong></p>
				<p class="f-right">
					************{{ $account_number }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_sequence_number')</strong></p>
				<p class="f-right">
					{{ $payment_response_json['response']['SN'] }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_termial_id')</strong></p>
				<p class="f-right">
					{{ $payment_response_json['response']['RegisterId'] }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_total_amount')</strong></p>
				<p class="f-right">
				{{ $total_amount }}
				</p>
			</div>

            <div class="textbox-info">
				<p class="f-left"><strong>@lang('lang_v1.receipt_response_code')</strong></p>
				<p class="f-right">
					{!! str_replace('%20',' ',$payment_response_json['response']['RespMSG']) !!}
				</p>
			</div>
            @endif
            <br>
            <div class="width-100">&nbsp;</div>
            @if(empty($receipt_details->hide_price) && !empty($receipt_details->tax_summary_label) )
	            <!-- tax -->
	            @if(!empty($receipt_details->taxes))
	            	<table class="border-bottom width-100 table-f-12">
	            		<tr>
	            			<th colspan="2" class="text-center">{{$receipt_details->tax_summary_label}}</th>
	            		</tr>
	            		@foreach($receipt_details->taxes as $key => $val)
	            			<tr>
	            				<td class="left">{{$key}}</td>
	            				<td class="right">{{$val}}</td>
	            			</tr>
	            		@endforeach
	            	</table>
	            @endif
            @endif

            @if(!empty($receipt_details->additional_notes))
	            <p class="centered">
	            	{!! nl2br($receipt_details->additional_notes) !!}
	            </p>
            @endif

            {{-- Barcode --}}
			@if($receipt_details->show_barcode)
				<br/>
				<img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
			@endif
			
			@if(!empty($receipt_details->footer_text))
				<p class="centered">
					{!! $receipt_details->footer_text !!}
				</p>
			@endif
            <div class="centered">**************************************************</div>
            <div>
                <p class="centered">
					@lang('lang_v1.receipt_thank_you_msg') {{$receipt_details->display_name}}
				</p>
            </div>
			<div class="centered">**************************************************</div>
        	



        </div>


    </body>
</html>

<style type="text/css">
.f-8 {
	font-size: 8px !important;
}
body {
	color: #000000;
}

@media print {
	* {
    	font-size: 12px;
    	font-family: 'Times New Roman';
    	word-break: break-all;
        text-transform: uppercase;
	}
	.f-8 {
		font-size: 8px !important;
	}
.headings{
	font-size: 16px;
	font-weight: 700;
	text-transform: uppercase;
	white-space: nowrap;
}

.sub-headings{
	font-size: 15px !important;
	font-weight: 700 !important;
}

.border-top{
    border-top: 1px solid #242424;
}
.border-bottom{
	border-bottom: 1px solid #242424;
}

.border-bottom-dotted{
	border-bottom: 1px dotted darkgray;
}

td.serial_number, th.serial_number{
	width: 5%;
    max-width: 5%;
}

td.description,
th.description {
    width: 35%;
    max-width: 35%;
}

td.quantity,
th.quantity {
    width: 15%;
    max-width: 15%;
    word-break: break-all;
}
td.unit_price, th.unit_price{
	width: 25%;
    max-width: 25%;
    word-break: break-all;
}

td.price,
th.price {
    width: 20%;
    max-width: 20%;
    word-break: break-all;
}

.centered {
    text-align: center;
    align-content: center;
}

.ticket {
    width: 300px;
    max-width: 300px;
}

img {
    max-width: inherit;
    width: auto;
}

    .hidden-print,
    .hidden-print * {
        display: none !important;
    }
}
.table-info {
	width: 100%;
}
.table-info tr:first-child td, .table-info tr:first-child th {
	padding-top: 8px;
}
.table-info th {
	text-align: left;
}
.table-info td {
	text-align: right;
}
.logo {
	float: left;
	width:35%;
	padding: 10px;
}

.text-with-image {
	float: left;
	width:65%;
}
.text-box {
	width: 100%;
	height: auto;
}

.textbox-info {
	clear: both;
    font-size: 7px;
}
.textbox-info p {
	margin-bottom: 0px
    font-size: 7px;
}
.flex-box {
	display: flex;
	width: 100%;
}
.flex-box p {
	width: 50%;
	margin-bottom: 0px;
	white-space: nowrap;
}

.table-f-12 th, .table-f-12 td {
	font-size: 12px;
	word-break: break-word;
}

.bw {
	word-break: break-word;
}
</style>
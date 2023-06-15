
<html>
    <head>
        <title>Customer</title>
        <link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">
        @if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
            <link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
        @endif
        @yield('css')
        <!-- app css -->
        <link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
        <style>
            body {
                padding: 10px;
            }
            .fa-info-circle,
            .row_edit_product_price_model,
            #public-pos-customer thead tr th:last-child,
            #public-pos-customer tbody tr td:last-child,
            .form-control.input-sm.sub_unit,
            .input-group-btn {
                display: none;
            }
            #public-pos-customer thead tr th:nth-child(2) {
                text-align: left;
            }
            .input_number,
            .text-link {
                color: #000!important;
                pointer-events: none;
            }
            .input_number {
                border: none;
                background: transparent;
            }
            .col-left,
            .col-right {
                display: inline-block;
                vertical-align: top;
            }
            .col-left {
                width: 100px;
            }
            .col-right {
                width: calc(100% - 105px);
            }
            #total_payable {
                font-weight: 700;
                font-size: 24px;
            }
            .fade {
                display: none!important;
            }
        </style>
    </head>
    <body>
        <div class="col-left">
            @if($business->logo)
                <img src="{{ asset('uploads/business_logos') }}/{{ $business->logo }}">
            @else
                <img src="{{ asset('img') }}/default.png" alt="Color-Correction">
            @endif
        </div>
        <div class="col-right">
            <p>{{ $business->name }}</p>
            <p id="pos_clock"></p>
            <p id="pos_location"></p>
        </div>
        <table class="table table-condensed table-bordered table-striped table-responsive" id="public-pos-customer">
            <thead><tr><th class="tex-center  col-md-4 ">Product <i class="fa fa-info-circle text-info hover-q no-print " aria-hidden="true" data-container="body" data-toggle="popover" data-placement="auto bottom" data-content="Click <i>product name</i> to edit price, discount &amp; tax. <br/>Click <i>Comment Icon</i> to enter serial number / IMEI or additional note.<br/><br/>Click <i>Modifier Icon</i>(if enabled) for modifiers" data-html="true" data-trigger="hover"></i></th><th class="text-center col-md-3">Quantity</th><th class="text-center col-md-2 hide">Price inc. tax</th><th class="text-center col-md-2">Subtotal</th><th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th></tr></thead>
            <tbody>  
            </tbody>
        </table>
        <table class="table table-condensed table-bordered table-striped table-responsive">
            <tr>
                <th style="width: 200px;">Discount:&nbsp;&nbsp;</th>
                <td><span id="total_discount"></span></td>
            </tr>
            <tr>
                <th>Tax:&nbsp;&nbsp;</th>
                <td><span id="order_tax"></span></td>
            </tr>
            <tr>
                <th>Shipping:&nbsp;&nbsp;</th>
                <td><span id="shipping_charges_amount"></span></td>
            </tr>
            <tr>
                <th>Packing Charge:&nbsp;&nbsp;</th>
                <td><span id="packing_charge_text"></span></td>
            </tr>
            <tr>
                <th>Tips:&nbsp;&nbsp;</th>
                <td><span id="tips_text"></span></td>
            </tr>
            <tr>
                <th>Total:&nbsp;&nbsp;</th>
                <td><span id="total_payable"></span></td>
            </tr>
        </table>
    </body>
    <script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>
    <script>

        setInterval(function() {
            var pos_table = localStorage.getItem('pos_table');
            $('#public-pos-customer tbody').html(pos_table);

            var pos_location = localStorage.getItem('pos_location');
            $('#pos_location').text(pos_location);

            var total_payable = localStorage.getItem('total_payable');
            $('#total_payable').text(total_payable);

            var total_discount = localStorage.getItem('total_discount');
            $('#total_discount').text(total_discount);

            var order_tax = localStorage.getItem('order_tax');
            $('#order_tax').text(order_tax);

            var shipping_charges_amount = localStorage.getItem('shipping_charges_amount');
            $('#shipping_charges_amount').text(shipping_charges_amount);

            var packing_charge_text = localStorage.getItem('packing_charge_text');
            $('#packing_charge_text').text(packing_charge_text);

            var tips_text = localStorage.getItem('tips_text');
            $('#tips_text').text(tips_text);

        },1);

        currentTime();

        function currentTime() {
            let date = new Date(); 
            let hh = date.getHours();
            let mm = date.getMinutes();
            let ss = date.getSeconds();
            let session = "AM";

            if(hh == 0){
                hh = 12;
            }
            if(hh > 12){
                hh = hh - 12;
                session = "PM";
            }

            hh = (hh < 10) ? "0" + hh : hh;
            mm = (mm < 10) ? "0" + mm : mm;
            ss = (ss < 10) ? "0" + ss : ss;
                
            let time = hh + ":" + mm + ":" + ss + " " + session;

            document.getElementById("pos_clock").innerText = date.toLocaleDateString("en-US") + ' ' + time; 
            let t = setTimeout(function(){ currentTime() }, 1000);
        }

        
    </script>
             
</html>
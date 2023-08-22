
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
            #amount_tendered,
            #amount_change,
            #total_payable {
                font-weight: 700;
                font-size: 24px;
            }
            #tips_v2{
                display: block;
                width: 50%;
                padding:15px;
                margin: auto;
            }
            .fade {
                display: none!important;
            }
            .tips_v2_radio_input #tips_v2_4a {
                display: none;
            }
            .tips_v2_radio_input.active #tips_v2_4a {
                display: block;
            }
            #tips_v2 .-footer{ 
                display: none;
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
                <th><span id="gratuity_charges_label"></span>:&nbsp;&nbsp;</th>
                <td><span id="gratuity_charges"></span></td>
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
            <tr>
                <th>Amount Tendered:&nbsp;&nbsp;</th>
                <td><span id="amount_tendered"></span></td>
            </tr>
            <tr>
                <th>Change:&nbsp;&nbsp;</th>
                <td><span id="amount_change"></span></td>
            </tr>
        </table>
        
       

        <div id="tips_v2" style="display: none;">

            <div class="box-body" id="tips_v2_form">
                <h2 id="tips_v2_total_amount"></h2>
                <div class="row">
                   <div class="wrapper-of-tips-box">
                   </div>
                   <div class="col-md-12 tips_v2_radio tips_v2_radio_input ct" data-item="4">
                      <input type="radio" name="tips_v2" id="tips_v2_4" value="0" class="hidden">
                      <label for="tips_v2_4">Custom Tip Amount</label>
                      <input type="text" name="tips_v2" id="tips_v2_4a" value="0" class="form-control allow-decimal-number-only">
                   </div>
                   <div class="col-md-12 tips_v2_radio ct" attr-multiplier="0" data-item="5">
                      <input type="radio" name="tips_v2" id="tips_v2_5" value="0" class="hidden">
                      <label for="tips_v2_5">No Tip</label>
                   </div>
                </div>
             </div>

        </div>

        <footer style="display: block; position: fixed; bottom: 0; width: 100%;">
            Powered by <strong>Maxximu Software</strong>
        </footer>
    </body>
    <script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>
    <script>

        localStorage.setItem('is_visible_tips', 0);
        function render_tips_percentage_box(){
            var tips_popup_show = localStorage.getItem('tips_popup_show');
            var is_visible_tips = localStorage.getItem('is_visible_tips');
            
            if(tips_popup_show == "true" && is_visible_tips == 0){
                $('#tips_v2').css('display', 'block');
                var tips_option = localStorage.getItem('tips_options');
                tips_option = tips_option.split(',');
                var final_total = $('#total_payable').text();
                var html_box = "";
                $.each(tips_option, function( index, value ) {
                     if(value > 0){
                        var computed_raw = (final_total * value / 100).toFixed(2);
                        html_box += '<div class="col-md-4 tips_v2_radio" attr-multiplier='+value+' data-item='+index+'>';
                        html_box += '<input type="radio" name="tips_v2" id="tips_v2_'+value+'" value='+computed_raw+' class="hidden">';
                        html_box += '<label for="tips_v2_'+value+'">';
                        html_box += '<p class="percent">'+value+'%</p>';
                        html_box += '<p class="computed">'+computed_raw+'</p>';
                        html_box += '</label>';
                        html_box += '</div>'; 
                     }
                });
                $('.wrapper-of-tips-box').html(html_box);
                localStorage.setItem('is_visible_tips', 1);
            }

            if(tips_popup_show == "false"){
                $('#tips_v2').css('display', 'none');
                localStorage.setItem('is_visible_tips', 0);
            }
            
        }

        

        var reload = true;
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

            var gratuity_charges_label = localStorage.getItem('gratuity_charges_label');
            if(!gratuity_charges_label) {
                $('#gratuity_charges_label').parents('tr').hide();
            }
            $('#gratuity_charges_label').text(gratuity_charges_label);

            var gratuity_charges = localStorage.getItem('gratuity_charges');
            $('#gratuity_charges').text(gratuity_charges);

            var packing_charge_text = localStorage.getItem('packing_charge_text');
            $('#packing_charge_text').text(packing_charge_text);

            var tips_text = localStorage.getItem('tips_text');
            $('#tips_text').text(tips_text);

            var amount_tendered = localStorage.getItem('amount_tendered');
            $('#amount_tendered').text(amount_tendered);

            var amount_change = localStorage.getItem('amount_change');
            $('#amount_change').text(amount_change ?? 0);

            $('.pos_line_total').each(function(i){
                var total = $(this).val();
                var price = $(this).parents('.product_row').find('.pos_unit_price_inc_tax').val();
                var qty = total/price;
                $(this).parents('.product_row').find('.input_quantity').val(qty);
            })

            render_tips_percentage_box();
            detect_the_cashier_tips_event();

        },1500);

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


        $(document).on('input', '#tips_v2_4a', function() {
            var tips_amount = $(this).val();
            localStorage.setItem('tips_text', tips_amount);
            
            localStorage.setItem('tips_update_by', 'Customer');
            localStorage.setItem('manual_tip_input', tips_amount);
        });

        $(document).on('click', '.tips_v2_radio', function() {
            $('input[name="tips_v2"]').removeAttr('checked');
            $(this).find('input[type="radio"]').attr('checked', 'checked');

            var is_it_manual_input = $(this).find('input[id="tips_v2_4a"]').length;
            if(is_it_manual_input){
                $('input[id="tips_v2_4a"]').css('display', 'block');
            }else{
                $('input[id="tips_v2_4a"]').css('display', 'none');
            }
            
            var tips_amount = $(this).find('input').val();
            
            localStorage.setItem('tips_text', tips_amount);
            localStorage.setItem('tips_update_by', 'Customer');
            localStorage.setItem('tip_box_identifier', $(this).find('input[type="radio"]').attr('id'));
            //localStorage.setItem('manual_tip_input', '');
        })

        function detect_the_cashier_tips_event(){
            var tips_update_by =  localStorage.getItem('tips_update_by');
            if(tips_update_by == "Cashier"){
                var tip_box_identifier = localStorage.getItem('tip_box_identifier');
                $('#'+tip_box_identifier).trigger('click');

                if(tip_box_identifier == "tips_v2_4"){
                    var manual_tip_input = localStorage.getItem('manual_tip_input');
                    $('#tips_v2_4a').val(manual_tip_input);
                }
                localStorage.setItem('tips_update_by', '');
            }
        }



    </script>
             
</html>
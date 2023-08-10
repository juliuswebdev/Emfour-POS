$(document).ready(function() {


    // var barcode = '';
    // var interval;
    // document.addEventListener('keydown', function(evt) {
    //     if (evt.code === 'F12'){
    //         evt.preventDefault();
    //     }
    //     if (interval){
    //         clearInterval(interval);
    //     }
    //     if (evt.code == 'Enter') {
    //         if (barcode){
    //             var card_data = barcode;
    //             var details1 = card_data.split("^");

    //             var card_number = details1[0];
    //             card_number = card_number.substring(2);
            
    //             var names = details1[1].split("/");
    //             var first_name = names[1];
    //             var last_name = names[0];
            
    //             var details2 = details1[2].split(";");
    //             details2 = details2[1].split("=");
            
    //             var exp_date = details2[1];
    //             exp_date = exp_date.substring(0, exp_date.length - 1);
    //             exp_date = exp_date.substring(2, 4) + "/" + exp_date.substring(0,2);
    //             console.log('card_number: ' + card_number);
    //             console.log('exp_date: ' + exp_date);
    //             console.log('first_name: ' + first_name);
    //             console.log('last_name:' + last_name);

    //             var month = exp_date.split("/")[0];
    //             var year = exp_date.split("/")[1];

    //             $('#card_number').val(card_number);
    //             $('#card_holder_name').val(first_name + ' ' + last_name);
    //             $('#card_month').val(month);
    //             $('#card_year').val(year);
    //             $('#card_type').val(GetCardType(card_number));

    //             $('#card_number').hide();
    //             var input = '<input class="form-control valid" placeholder="Card Number" id="card_number" autofocus="" name="" type="text" aria-invalid="false">';
    //             $(input).insertAfter('#card_number');

    //         }
    //         barcode = '';
    //         return;
    //     }
    //     if (evt.key != 'Shift'){
    //         barcode += evt.key;
    //     }
    //     interval = setInterval(() => barcode = '', 20);
    // });


    const urlParams = new URL(window.location.href).searchParams;
    const products_id = urlParams.get('booking_product_ids');
    if(products_id) {
        products_id.split(',').forEach(function(item) {
            pos_product_row(item);
        });
    }

    $('#restaurant-search-order').submit(function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            context: this,
            method: "GET",
            url: $(this).attr("action"),
            data: data,
            success: function(result) {
                $('#restaurant-search-order_result').html(result);
                $('.btn-search').removeAttr('disabled');
            }
        });
    });

    $('.search-check').submit(function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            context: this,
            method: "POST",
            url: $(this).attr("action"),
            data: data,
            success: function(result) {
                $('#checkout_result').html(result);
                $('.btn-search').removeAttr('disabled');
            }
        });
    });


    $(document).on('submit', 'form.check_booking_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            context: this,
            method: "PUT",
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            beforeSend: function(xhr) {
                __disable_submit_button($(this).find('button[type="submit"]'));
            },
            success: function(result){
                if(result.success == true) {
                    console.log(result);
                    $('#checkout_result').html('');
                    $('input[name="search_query"]').val('');
                    $(this).find('button[type="submit"]').attr('disabled', false);
                    var booking_product_ids = $(this).find('input[name="booking_product_ids"]').val();
                    var booking_correspondent_id = $(this).find('input[name="booking_correspondent_id"]').val();
                    var booking_customer = $(this).find('input[name="booking_customer"]').val();
                    booking_product_ids.split(',').forEach(function(item) {
                        pos_product_row(item);
                    });
                    $('#correspondent_id').select2('val', booking_correspondent_id);
                    $('#customer_id').append('<option value="'+result.booking.contact_id+'">'+result.booking.booking_details.full_name+' ('+result.booking.customer.contact_id+')</option>');
                    $('#customer_id').val(booking_customer);
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });

    customer_set = false;
    //Prevent enter key function except texarea
    $('form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    //For edit pos form
    if ($('form#edit_pos_sell_form').length > 0) {
        pos_total_row();
        pos_form_obj = $('form#edit_pos_sell_form');
    } else {
        pos_form_obj = $('form#add_pos_sell_form');
    }
    if ($('form#edit_pos_sell_form').length > 0 || $('form#add_pos_sell_form').length > 0) {
        initialize_printer();
    }

    $('select#select_location_id').change(function() {
        reset_pos_form();

        var default_price_group = $(this).find(':selected').data('default_price_group')
        if (default_price_group) {
            if($("#price_group option[value='" + default_price_group + "']").length > 0) {
                $("#price_group").val(default_price_group);
                $("#price_group").change();
            }
        }

        //Set default invoice scheme for location
        if ($('#invoice_scheme_id').length) {
            let invoice_scheme_id = $(this).find(':selected').data('default_invoice_scheme_id');
            $("#invoice_scheme_id").val(invoice_scheme_id).change();
        }

        //Set default invoice layout for location
        if ($('#invoice_layout_id').length) {
            let invoice_layout_id = $(this).find(':selected').data('default_invoice_layout_id');
            $("#invoice_layout_id").val(invoice_layout_id).change();
        }
        
        //Set default price group
        if ($('#default_price_group').length) {
            var dpg = default_price_group ?
            default_price_group : 0;
            $('#default_price_group').val(dpg);
        }

        set_payment_type_dropdown();

        if ($('#types_of_service_id').length && $('#types_of_service_id').val()) {
            $('#types_of_service_id').change();
        }

        //Check register is open or not for this location
        check_location_is_open();


    });

    //get customer
    $('select#customer_id').select2({
        ajax: {
            url: '/contacts/customers',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term, // search term
                    page: params.page,
                };
            },
            processResults: function(data) {
                return {
                    results: data,
                };
            },
        },
        templateResult: function (data) { 
            var template = '';
            if (data.supplier_business_name) {
                template += data.supplier_business_name + "<br>";
            }
            template += data.text + "<br>" + LANG.mobile + ": " + data.mobile;

            if (typeof(data.total_rp) != "undefined") {
                var rp = data.total_rp ? data.total_rp : 0;
                template += "<br><i class='fa fa-gift text-success'></i> " + rp;
            }

            return  template;
        },
        minimumInputLength: 1,
        language: {
            noResults: function() {
                var name = $('#customer_id')
                    .data('select2')
                    .dropdown.$search.val();
                return (
                    '<button type="button" data-name="' +
                    name +
                    '" class="btn btn-link add_new_customer"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' +
                    __translate('add_name_as_new_customer', { name: name }) +
                    '</button>'
                );
            },
        },
        escapeMarkup: function(markup) {
            return markup;
        },
    });
    $('#customer_id').on('select2:select', function(e) {
        var data = e.params.data;
        if (data.pay_term_number) {
            $('input#pay_term_number').val(data.pay_term_number);
        } else {
            $('input#pay_term_number').val('');
        }

        if (data.pay_term_type) {
            $('#add_sell_form select[name="pay_term_type"]').val(data.pay_term_type);
            $('#edit_sell_form select[name="pay_term_type"]').val(data.pay_term_type);
        } else {
            $('#add_sell_form select[name="pay_term_type"]').val('');
            $('#edit_sell_form select[name="pay_term_type"]').val('');
        }
        
        update_shipping_address(data);
        $('#advance_balance_text').text(__currency_trans_from_en(data.balance), true);
        $('#advance_balance').val(data.balance);

        if (data.price_calculation_type == 'selling_price_group') {
            $('#price_group').val(data.selling_price_group_id);
            $('#price_group').change();
        } else {
            $('#price_group').val('');
            $('#price_group').change();
        }
        if ($('.contact_due_text').length) {
            get_contact_due(data.id);
        }
    });

    set_default_customer();

    if ($('#search_product').length) {
        //Add Product
        $('#search_product')
            .autocomplete({
                delay: 1000,
                source: function(request, response) {
                    var price_group = '';
                    var search_fields = [];
                    $('.search_fields:checked').each(function(i){
                      search_fields[i] = $(this).val();
                    });

                    if ($('#price_group').length > 0) {
                        price_group = $('#price_group').val();
                    }
                    $.getJSON(
                        '/products/list',
                        {
                            price_group: price_group,
                            location_id: $('input#location_id').val(),
                            term: request.term,
                            not_for_selling: 0,
                            search_fields: search_fields
                        },
                        response
                    );
                },
                //minLength: 2,
                minLength: 1,
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];

                        var is_overselling_allowed = false;
                        if($('input#is_overselling_allowed').length) {
                            is_overselling_allowed = true;
                        }
                        var for_so = false;
                        if ($('#sale_type').length && $('#sale_type').val() == 'sales_order') {
                            for_so = true;
                        }

                        if ((ui.item.enable_stock == 1 && ui.item.qty_available > 0) || 
                                (ui.item.enable_stock == 0) || is_overselling_allowed || for_so) {
                            $(this)
                                .data('ui-autocomplete')
                                ._trigger('select', 'autocompleteselect', ui);
                            $(this).autocomplete('close');
                        }
                    } else if (ui.content.length == 0) {
                        toastr.error(LANG.no_products_found);
                        $('input#search_product').select();
                    }
                },
                focus: function(event, ui) {
                    if (ui.item.qty_available <= 0) {
                        return false;
                    }
                },
                select: function(event, ui) {
                    var searched_term = $(this).val();
                    var is_overselling_allowed = false;
                    if($('input#is_overselling_allowed').length) {
                        is_overselling_allowed = true;
                    }
                    var for_so = false;
                    if ($('#sale_type').length && $('#sale_type').val() == 'sales_order') {
                        for_so = true;
                    }

                    var is_draft=false;
                    if($('input#status') && ($('input#status').val()=='quotation' || 
                    $('input#status').val()=='draft')) {
                        var is_draft=true;
                    }

                    if (ui.item.enable_stock != 1 || ui.item.qty_available > 0 || is_overselling_allowed || for_so || is_draft) {
                        $(this).val(null);

                        //Pre select lot number only if the searched term is same as the lot number
                        var purchase_line_id = ui.item.purchase_line_id && searched_term == ui.item.lot_number ? ui.item.purchase_line_id : null;
                        pos_product_row(ui.item.variation_id, purchase_line_id);
                    } else {
                        //alert(LANG.out_of_stock);
                    }
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
                var is_overselling_allowed = false;
                if($('input#is_overselling_allowed').length) {
                    is_overselling_allowed = true;
                }

                var for_so = false;
                if ($('#sale_type').length && $('#sale_type').val() == 'sales_order') {
                    for_so = true;
                }
                var is_draft=false;
                if($('input#status') && ($('input#status').val()=='quotation' || 
                $('input#status').val()=='draft')) {
                    var is_draft=true;
                }

            if (item.enable_stock == 1 && item.qty_available <= 0 && !is_overselling_allowed && !for_so && !is_draft) {
                var string = '<li class="ui-state-disabled">' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }
                var selling_price = item.selling_price;
                if (item.variation_group_price) {
                    selling_price = item.variation_group_price;
                }
                string +=
                    ' (' +
                    item.sub_sku +
                    ')' +
                    '<br> Price: ' +
                    selling_price +
                    ' (Out of stock) </li>';
                return $(string).appendTo(ul);
            } else {
                var string = '<div>' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }

                var selling_price = item.selling_price;
                if (item.variation_group_price) {
                    selling_price = item.variation_group_price;
                }

                string += ' (' + item.sub_sku + ')' + '<br> Price: ' + selling_price;
                if (item.enable_stock == 1) {
                    var qty_available = __currency_trans_from_en(item.qty_available, false, false, __currency_precision, true);
                    string += ' - ' + qty_available + item.unit;
                }
                string += '</div>';
                var weighing_sale_class = (item.weighing_sale) ? 'weighing_sale' : '';
                return $('<li class="'+weighing_sale_class+'" data-product-id="'+item.product_id+'">')
                    .append(string)
                    .appendTo(ul);
            }
        };
    }


    $(document).on('click', '.weighing_sale', function() {
        var product_id = $(this).attr('data-product-id');
        $('#weighing_sale_2 button').attr('data-product-id', product_id);
        $('#weighing_sale_2').modal('show');
        setTimeout(function(){
            $('#weighing_sale_2 input').focus();
        },500);
    });

    $(document).on('click', '.weight_save', function() {
        var product_id = $(this).attr('data-product-id');
        var val = $('#weighing_sale_2 input').val();
        $('#pos_table .product_row_'+product_id).find('.pos_quantity').val(val).trigger('change');
        $('#weighing_sale_2').modal('hide');
       
    });

    var tr_temp = '';
    var entered_qty_temp = 0;
    //Update line total and check for quantity not greater than max quantity
    $('table#pos_table tbody').on('change', 'input.pos_quantity', function() {
        if (sell_form_validator) {
            sell_form.valid();
        }
        if (pos_form_validator) {
            pos_form_validator.element($(this));
        }
        // var max_qty = parseFloat($(this).data('rule-max'));
        var entered_qty = __read_number($(this));
        var tr = $(this).parents('tr');
        tr_temp = tr;
        entered_qty_temp = entered_qty;

        if(entered_qty > -1) {

            update_all_change_quantity(tr, entered_qty);


        } else {
            $.ajax({
                context: this,
                method: "POST",
                url: '/user/check-has-pin',
                data: { user_id : $('#user_id_pin').val() },
                dataType: "json",
                success: function(result) {
                    if(result.success == true) {
                        $('#check_user_pin').addClass('entered_qty');
                        $('#pin_server_modal').modal('show');
                        price_override_id = $(this).attr('data-target');
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        }

    });

    $(document).on('submit', '#check_user_pin.entered_qty', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            context: this,
            method: "POST",
            url: $(this).attr("action"),
            data: data,
            dataType: "json",
            success: function(result) {
                if(result.success == true) {
                    update_all_change_quantity(tr_temp, entered_qty_temp);
                    toastr.success(result.msg);
                    $('#pin_server_modal').modal('hide');
                } else {
                    toastr.error(result.msg);
                }
                $('#check_user_pin #pin').val('');
                $('#check_user_pin button').removeAttr('disabled');
            }
        });
    });

    function update_all_change_quantity(tr, entered_qty) {
        
        var unit_price_inc_tax = tr.find('input.pos_unit_price_inc_tax').attr('value');
        //alert(unit_price_inc_tax);
        var line_total = entered_qty * unit_price_inc_tax;

        __write_number(tr.find('input.pos_line_total'), line_total, false, 2);
        tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));

        //Change modifier quantity
        tr.find('.modifier_qty_text').each( function(){
            $(this).text(__currency_trans_from_en(entered_qty, false));
        });
        tr.find('.modifiers_quantity').each( function(){
            $(this).val(entered_qty);
        });

        pos_total_row();

        adjustComboQty(tr);
    }

    //If change in unit price update price including tax and line total
    $('table#pos_table tbody').on('change', 'input.pos_unit_price', function() {
        var unit_price = __read_number($(this));
        var tr = $(this).parents('tr');

        //calculate discounted unit price
        var discounted_unit_price = calculate_discounted_unit_price(tr);

        var tax_rate = tr
            .find('select.tax_id')
            .find(':selected')
            .data('rate');
        var quantity = __read_number(tr.find('input.pos_quantity'));

        var unit_price_inc_tax = __add_percent(discounted_unit_price, tax_rate);
        var line_total = quantity * unit_price_inc_tax;

        __write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);
        __write_number(tr.find('input.pos_line_total'), line_total);
        tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));
        pos_each_row(tr);
        pos_total_row();
        round_row_to_iraqi_dinnar(tr);
    });

    //If change in tax rate then update unit price according to it.
    $('table#pos_table tbody').on('change', 'select.tax_id', function() {
        var tr = $(this).parents('tr');

        var tax_rate = tr
            .find('select.tax_id')
            .find(':selected')
            .data('rate');
        var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));

        var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
        var unit_price = get_unit_price_from_discounted_unit_price(tr, discounted_unit_price);
        __write_number(tr.find('input.pos_unit_price'), unit_price);
        pos_each_row(tr);
    });

    //If change in unit price including tax, update unit price
    $('table#pos_table tbody').on('change', 'input.pos_unit_price_inc_tax', function() {
        var unit_price_inc_tax = __read_number($(this));

        if (iraqi_selling_price_adjustment) {
            unit_price_inc_tax = round_to_iraqi_dinnar(unit_price_inc_tax);
            __write_number($(this), unit_price_inc_tax);
        }

        var tr = $(this).parents('tr');

        var tax_rate = tr
            .find('select.tax_id')
            .find(':selected')
            .data('rate');
        var quantity = __read_number(tr.find('input.pos_quantity'));

        var line_total = quantity * unit_price_inc_tax;
        var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
        var unit_price = get_unit_price_from_discounted_unit_price(tr, discounted_unit_price);

        __write_number(tr.find('input.pos_unit_price'), unit_price);
        __write_number(tr.find('input.pos_line_total'), line_total, false, 2);
        tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));

        pos_each_row(tr);
        pos_total_row();
    });

    //Change max quantity rule if lot number changes
    $('table#pos_table tbody').on('change', 'select.lot_number', function() {
        var qty_element = $(this)
            .closest('tr')
            .find('input.pos_quantity');

        var tr = $(this).closest('tr');
        var multiplier = 1;
        var unit_name = '';
        var sub_unit_length = tr.find('select.sub_unit').length;
        if (sub_unit_length > 0) {
            var select = tr.find('select.sub_unit');
            multiplier = parseFloat(select.find(':selected').data('multiplier'));
            unit_name = select.find(':selected').data('unit_name');
        }
        var allow_overselling = qty_element.data('allow-overselling');
        if ($(this).val() && !allow_overselling) {
            var lot_qty = $('option:selected', $(this)).data('qty_available');
            var max_err_msg = $('option:selected', $(this)).data('msg-max');

            if (sub_unit_length > 0) {
                lot_qty = lot_qty / multiplier;
                var lot_qty_formated = __number_f(lot_qty, false);
                max_err_msg = __translate('lot_max_qty_error', {
                    max_val: lot_qty_formated,
                    unit_name: unit_name,
                });
            }

            qty_element.attr('data-rule-max-value', lot_qty);
            qty_element.attr('data-msg-max-value', max_err_msg);

            qty_element.rules('add', {
                'max-value': lot_qty,
                messages: {
                    'max-value': max_err_msg,
                },
            });
        } else {
            var default_qty = qty_element.data('qty_available');
            var default_err_msg = qty_element.data('msg_max_default');
            if (sub_unit_length > 0) {
                default_qty = default_qty / multiplier;
                var lot_qty_formated = __number_f(default_qty, false);
                default_err_msg = __translate('pos_max_qty_error', {
                    max_val: lot_qty_formated,
                    unit_name: unit_name,
                });
            }

            qty_element.attr('data-rule-max-value', default_qty);
            qty_element.attr('data-msg-max-value', default_err_msg);

            qty_element.rules('add', {
                'max-value': default_qty,
                messages: {
                    'max-value': default_err_msg,
                },
            });
        }
        qty_element.trigger('change');
    });

    //Change in row discount type or discount amount
    $('table#pos_table tbody').on(
        'change',
        'select.row_discount_type, input.row_discount_amount',
        function() {
            var tr = $(this).parents('tr');

            //calculate discounted unit price
            var discounted_unit_price = calculate_discounted_unit_price(tr);

            var tax_rate = tr
                .find('select.tax_id')
                .find(':selected')
                .data('rate');
            var quantity = __read_number(tr.find('input.pos_quantity'));

            var unit_price_inc_tax = __add_percent(discounted_unit_price, tax_rate);
            var line_total = quantity * unit_price_inc_tax;

            __write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);
            __write_number(tr.find('input.pos_line_total'), line_total, false, 2);
            tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));
            pos_each_row(tr);
            pos_total_row();
            round_row_to_iraqi_dinnar(tr);
        }
    );

    //Remove row on click on remove row
    $('table#pos_table tbody').on('click', 'i.pos_remove_row', function() {
        $(this)
            .parents('tr')
            .remove();
        pos_total_row();
    });

    //Cancel the invoice
    $('button#pos-cancel').click(function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(confirm => {
            if (confirm) {
                reset_pos_form();
            }
        });
    });

    //Save invoice as draft
    $('button#pos-draft').click(function() {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        var is_valid = isValidPosForm();
        if (is_valid != true) {
            return;
        }

        var data = pos_form_obj.serialize();
        data = data + '&status=draft';
        var url = pos_form_obj.attr('action');

        disable_pos_form_actions();
        $.ajax({
            method: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(result) {
                enable_pos_form_actions();
                if (result.success == 1) {
                    reset_pos_form();
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    //Save invoice as Quotation
    $('button#pos-quotation').click(function() {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        var is_valid = isValidPosForm();
        if (is_valid != true) {
            return;
        }

        var data = pos_form_obj.serialize();
        data = data + '&status=quotation';
        var url = pos_form_obj.attr('action');

        disable_pos_form_actions();
        $.ajax({
            method: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(result) {
                enable_pos_form_actions();
                if (result.success == 1) {
                    reset_pos_form();
                    toastr.success(result.msg);

                    //Check if enabled or not
                    if (result.receipt.is_enabled) {
                        pos_print(result.receipt);
                    }
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    //Finalize invoice, open payment modal
    $('button#pos-finalize').click(function() {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        if ($('#reward_point_enabled').length) {
            var validate_rp = isValidatRewardPoint();
            if (!validate_rp['is_valid']) {
                toastr.error(validate_rp['msg']);
                return false;
            }
        }

        $('#modal_payment').modal('show');
    });

    $('#modal_payment').one('shown.bs.modal', function() {
        $('#modal_payment')
            .find('input')
            .filter(':visible:first')
            .focus()
            .select();
        if ($('form#edit_pos_sell_form').length == 0) {
            $(this).find('#method_0').change();
        }
    });

    //Finalize without showing payment options
    $('button.pos-express-finalize').click(function() {
        
        var total_payable = parseFloat($('#total_payable').text());
        var card_charge_percent = parseFloat($('#card_charge_percent_hidden').val()) / 100;
        var total_payable = $('#__symbol').val() +' '+ (total_payable + ( total_payable * card_charge_percent ))
        $('#card_total_payable').text( total_payable );
       
        
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        if ($('#reward_point_enabled').length) {
            var validate_rp = isValidatRewardPoint();
            if (!validate_rp['is_valid']) {
                toastr.error(validate_rp['msg']);
                return false;
            }
        }

        var pay_method = $(this).data('pay_method');

        //If pay method is credit sale submit form
        if (pay_method == 'credit_sale') {
            $('#is_credit_sale').val(1);
            pos_form_obj.submit();
            return true;
        } else {
            if ($('#is_credit_sale').length) {
                $('#is_credit_sale').val(0);
            }
        }

        //Check for remaining balance & add it in 1st payment row
        var total_payable = __read_number($('input#final_total_input'));
        var total_paying = __read_number($('input#total_paying_input'));
        if (total_payable > total_paying) {
            var bal_due = total_payable - total_paying;

            var first_row = $('#payment_rows_div')
                .find('.payment-amount')
                .first();
            var first_row_val = __read_number(first_row);
            first_row_val = first_row_val + bal_due;
            __write_number(first_row, first_row_val);
            first_row.trigger('change');
        }

        //Change payment method.
        var payment_method_dropdown = $('#payment_rows_div')
            .find('.payment_types_dropdown')
            .first();
        
            payment_method_dropdown.val(pay_method);
            payment_method_dropdown.change();
        if (pay_method == 'card') {

            var processing_text = "<span class='card-payment-popup'><div>Processing of Payment..</div><div><button id='card-payment-close' class='btn-danger'>Close</button></div></span>"
            var bg_black_fade_in = '<div class="modal-backdrop fade in">'+processing_text+'</div>';
            $('.ui-helper-hidden-accessible').after(bg_black_fade_in);
            pos_form_obj.submit();
            
            //$('div#card_details_modal').modal('show');
        } else if (pay_method == 'suspend') {
            //Change the modal title & label
           
            var send_to_kitchen_title = $(this).data('modal-title');
            if(send_to_kitchen_title != undefined){
                var send_to_kitchen_label = $(this).data('modal-placeholder');
                $('#confirmSuspendModal').find('.modal-title').text(send_to_kitchen_title);
                $('#confirmSuspendModal').find('.modal-body label:eq(0)').text(send_to_kitchen_label);
            }
            
            $('div#confirmSuspendModal').modal('show');
        } else if (pay_method == 'cash') {

            $('div#modal_cash_payment').modal('show');
            
        } else {
            pos_form_obj.submit();
        }
    });

    $('div#card_details_modal').on('shown.bs.modal', function(e) {
        $('input#card_number').focus();
    });

    $('div#confirmSuspendModal').on('shown.bs.modal', function(e) {
        $(this)
            .find('textarea')
            .focus();
    });

    //on save card details
    $('button#pos-save-card').click(function() {
        $('input#card_number_0').val($('#card_number').val());
        $('input#card_holder_name_0').val($('#card_holder_name').val());
        $('input#card_transaction_number_0').val($('#card_transaction_number').val());
        $('select#card_type_0').val($('#card_type').val());
        $('input#card_month_0').val($('#card_month').val());
        $('input#card_year_0').val($('#card_year').val());
        $('input#card_security_0').val($('#card_security').val());

        $('div#card_details_modal').modal('hide');
        pos_form_obj.submit();

        

    });

    $('button#pos-suspend').click(function() {
        $('input#send_to_kitchen').val(1);
        $('input#is_suspend').val(1);
        $('div#confirmSuspendModal').modal('hide');
        pos_form_obj.submit();
        $('input#send_to_kitchen').val(0);
        $('input#is_suspend').val(0);
    });

    //fix select2 input issue on modal
    $('#modal_payment')
        .find('.select2')
        .each(function() {
            $(this).select2({
                dropdownParent: $('#modal_payment'),
            });
        });

    $('button#add-payment-row').click(function() {
        var row_index = $('#payment_row_index').val();
        var location_id = $('input#location_id').val();
        $.ajax({
            method: 'POST',
            url: '/sells/pos/get_payment_row',
            data: { row_index: row_index, location_id: location_id },
            dataType: 'html',
            success: function(result) {
                if (result) {
                    var appended = $('#payment_rows_div').append(result);

                    var total_payable = __read_number($('input#final_total_input'));
                    var total_paying = __read_number($('input#total_paying_input'));
                    var b_due = total_payable - total_paying;
                    $(appended)
                        .find('input.payment-amount')
                        .focus();
                    $(appended)
                        .find('input.payment-amount')
                        .last()
                        .val(__currency_trans_from_en(b_due, false))
                        .change()
                        .select();
                    __select2($(appended).find('.select2'));
                    $(appended).find('#method_' + row_index).change();
                    $('#payment_row_index').val(parseInt(row_index) + 1);

                    //DropDown Sequence Update
                    var total_box = $('#payment_rows_div').find('.payment_types_dropdown').length;
                    var recent_dropdown_selected = parseInt(total_box) - 2;
                    var dropdown_all_options = $('.payment_types_dropdown:eq('+recent_dropdown_selected+')').find('option').length;
                    var recent_dropdown_option_index = $('.payment_types_dropdown:eq('+recent_dropdown_selected+')').find('option:selected').index();

                    var new_dropdown_index = parseInt(total_box) - 1;
                    var new_dropdown =  $('.payment_types_dropdown:eq('+new_dropdown_index+')');
                    new_dropdown.find('option').removeAttr('selected');
	                
                    dropdown_all_options = (parseInt(dropdown_all_options) - 1);
                    if(dropdown_all_options == recent_dropdown_option_index){
                        //Select First Option
                        var new_option_position = 0;
                    }else{
                        //Select Next Option
                        var new_option_position = parseInt(recent_dropdown_option_index) + 1;
                    }
                    new_dropdown.find('option:eq('+new_option_position+')').attr('selected', 'selected');
                    
                    //Update the dropdown vise input fields
                    var select_box = $('#payment_rows_div').find('.payment_row:eq('+new_dropdown_index+')');
                    select_box.find('.payment_details_div').removeClass('hide').addClass('hide');
                    var selected_option_value = new_dropdown.find('option:eq('+new_option_position+')').val();
                    select_box.find('.payment_details_div[data-type="'+selected_option_value+'"]').removeClass('hide');
                }
            },
        });
    });

    

    $(document).on('click', '.remove_payment_row', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                $(this)
                    .closest('.payment_row')
                    .remove();
                calculate_balance_due();
            }
        });
    });

    pos_form_validator = pos_form_obj.validate({
        submitHandler: function(form) {
            // var total_payble = __read_number($('input#final_total_input'));
            // var total_paying = __read_number($('input#total_paying_input'));
            var cnf = true;

            //Ignore if the difference is less than 0.5
            if ($('input#in_balance_due').val() >= 0.5) {
                // cnf = confirm(LANG.paid_amount_is_less_than_payable);
                // if( total_payble > total_paying ){
                // 	cnf = confirm( LANG.paid_amount_is_less_than_payable );
                // } else if(total_payble < total_paying) {
                // 	alert( LANG.paid_amount_is_more_than_payable );
                // 	cnf = false;
                // }
            }

            var total_advance_payments = 0;
            $('#payment_rows_div').find('select.payment_types_dropdown').each( function(){
                if ($(this).val() == 'advance') {
                    total_advance_payments++
                };
            });

            if (total_advance_payments > 1) {
                //alert(LANG.advance_payment_cannot_be_more_than_once);
                return false;
            }

            var is_msp_valid = true;
            //Validate minimum selling price if hidden
            $('.pos_unit_price_inc_tax').each( function(){
                if (!$(this).is(":visible") && $(this).data('rule-min-value')) {
                    var val = __read_number($(this));
                    var error_msg_td = $(this).closest('tr').find('.pos_line_total_text').closest('td');
                    if (val > $(this).data('rule-min-value')) {
                        is_msp_valid = false;
                        error_msg_td.append( '<label class="error">' + $(this).data('msg-min-value') + '</label>');
                    } else {
                        error_msg_td.find('label.error').remove();
                    }
                }
            });

            if (!is_msp_valid) {
                return false;
            }

            if (cnf) {
                
                disable_pos_form_actions();

                var data = $(form).serialize();
                data = data + '&status=final';
                var url = $(form).attr('action');
                $.ajax({
                    method: 'POST',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success == 1) {
                            
                            if (result.whatsapp_link) {
                                window.open(result.whatsapp_link);
                            }
                            $('#modal_payment, #modal_cash_payment').modal('hide');
                            // var data_authorize_net = {
                            //     'invoice_id' : result.receipt.print_title,
                            //     'amount' : result.receipt.receipt_details.subtotal_unformatted,
                            //     'card_number' : $('#card_number').val(),
                            //     'card_first_name' : $('#card_first_name').val(),
                            //     'card_last_name' : $('#card_last_name').val(),
                            //     'card_year' : $('#card_year').val(),
                            //     'card_month' : $('#card_month').val(),
                            //     'card_cvv' : $('#card_security').val()
                            // };
                           
                            // $.ajax({
                            //     method: 'POST',
                            //     url: $('#api-authorize-net').val(),
                            //     data: data_authorize_net,
                            //     dataType: 'json',
                            //     success: function(result) {
                            //         console.log(result);
                            //     }
                            // });

                            toastr.success(result.msg);

                            reset_pos_form();
                          
                            //Check if enabled or not
                            if (result.receipt.is_enabled) {
                                pos_print(result.receipt);
                            }
                            
                        } else {
                            toastr.error(result.msg);
                        }
                        
                        var modal_backdrop_length = $('.modal-backdrop').length;
                        if(modal_backdrop_length > 1){
                            console.log(modal_backdrop_length);
                            $('.modal-backdrop').remove();
                        }   
                        enable_pos_form_actions();
                    },
                });
            }
            return false;
        },
    });

    $(document).on('change', '.payment-amount', function() {
        calculate_balance_due();
    });

    //Update discount
    $('button#posEditDiscountModalUpdate').click(function() {

        //if discount amount is not valid return false
        if (!$("#discount_amount_modal").valid()) {
            return false;
        }
        //Close modal
        $('div#posEditDiscountModal').modal('hide');

        //Update values
        $('input#discount_type').val($('select#discount_type_modal').val());
        __write_number($('input#discount_amount'), __read_number($('input#discount_amount_modal')));

        if ($('#reward_point_enabled').length) {
            var reward_validation = isValidatRewardPoint();
            if (!reward_validation['is_valid']) {
                toastr.error(reward_validation['msg']);
                $('#rp_redeemed_modal').val(0);
                $('#rp_redeemed_modal').change();
            }
            updateRedeemedAmount();
        }

        pos_total_row();
    });

    //Shipping
    $('button#posShippingModalUpdate').click(function() {
        //Close modal
        $('div#posShippingModal').modal('hide');

        //update shipping details
        $('input#shipping_details').val($('#shipping_details_modal').val());

        $('input#shipping_address').val($('#shipping_address_modal').val());
        $('input#shipping_status').val($('#shipping_status_modal').val());
        $('input#delivered_to').val($('#delivered_to_modal').val());

        //Update shipping charges
        __write_number(
            $('input#shipping_charges'),
            __read_number($('input#shipping_charges_modal'))
        );

        //$('input#shipping_charges').val(__read_number($('input#shipping_charges_modal')));

        pos_total_row();
    });

    $('#posShippingModal').on('shown.bs.modal', function() {
        $('#posShippingModal')
            .find('#shipping_details_modal')
            .filter(':visible:first')
            .focus()
            .select();
    });

    $(document).on('shown.bs.modal', '.row_edit_product_price_model', function() {
        $('.row_edit_product_price_model')
            .find('input')
            .filter(':visible:first')
            .focus()
            .select();
    });

    var price_override_id = '';
    $(document).on('click', '.row_edit_product_price_modal_btn', function() {
        $.ajax({
            context: this,
            method: "POST",
            url: '/user/check-has-pin',
            data: { user_id : $('#user_id_pin').val() },
            dataType: "json",
            success: function(result) {
                if(result.success == true) {
                    $('#check_user_pin').addClass('price_override');
                    $('#pin_server_modal').modal('show');
                    // $(this).parents('.product_row').find('.is_price_override').val(1);
                    // $(this).parents('.product_row').find('.user_id_pinned').val($('#user_id_pin').val());
                    price_override_id = $(this).attr('data-target');
                    populate_textarea_override_price($(this));
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });

    function populate_textarea_override_price(tr) {
        const date = $('.curr_datetime').text();
        const original_price = $('#__symbol').val() +''+ tr.parents('.product_row').find('.original_price').val();
        const override_price = $('#__symbol').val() +''+ tr.parents('.product_row').find('.row_edit_product_price_model .pos_unit_price').val();
        const name = $('#user_name_pin').val();
        const price_override_description = `Date: ${ date }
Price Change: ${ original_price } -> ${ override_price }
Name: ${ name }`;
        tr.parents('.product_row').find('.row_edit_product_price_model textarea').val(price_override_description)
    }

    $(document).on('change', '.row_edit_product_price_model', function() {
        populate_textarea_override_price($(this));
    });


    // $(document).on('keyup', '.input_quantity', function() {
    //     $.ajax({
    //         context: this,
    //         method: "POST",
    //         url: '/user/check-has-pin',
    //         data: { user_id : $('#user_id_pin').val() },
    //         dataType: "json",
    //         success: function(result) {
    //             if(result.success == true) {

    //                 $('#pin_server_modal').modal('show');
    //                 price_override_id = tr.attr('data-target');
    //             } else {
    //                 toastr.error(result.msg);
    //             }
    //         }
    //     });
    // });
    

    $(document).on('submit', '#check_user_pin.price_override', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            context: this,
            method: "POST",
            url: $(this).attr("action"),
            data: data,
            dataType: "json",
            success: function(result) {
                if(result.success == true) {
                    toastr.success(result.msg);
                    $('#pin_server_modal').modal('hide');
                    $(price_override_id).modal('show');
                } else {
                    toastr.error(result.msg);
                }
                $('#check_user_pin #pin').val('');
                $('#check_user_pin button').removeAttr('disabled');
            }
        });
    });

    //Update Order tax
    $('button#posEditOrderTaxModalUpdate').click(function() {
        //Close modal
        $('div#posEditOrderTaxModal').modal('hide');

        var tax_obj = $('select#order_tax_modal');
        var tax_id = tax_obj.val();
        var tax_rate = tax_obj.find(':selected').data('rate');

        $('input#tax_rate_id').val(tax_id);

        __write_number($('input#tax_calculation_amount'), tax_rate);
        pos_total_row();
    });

    $(document).on('click', '.add_new_customer', function() {
        $('#customer_id').select2('close');
        var name = $(this).data('name');
        $('.contact_modal')
            .find('input#name')
            .val(name);
        $('.contact_modal')
            .find('select#contact_type')
            .val('customer')
            .closest('div.contact_type_div')
            .addClass('hide');
        $('.contact_modal').modal('show');
    });
    $('form#quick_add_contact')
        .submit(function(e) {
            e.preventDefault();
        })
        .validate({
            rules: {
                contact_id: {
                    remote: {
                        url: '/contacts/check-contacts-id',
                        type: 'post',
                        data: {
                            contact_id: function() {
                                return $('#contact_id').val();
                            },
                            hidden_id: function() {
                                if ($('#hidden_id').length) {
                                    return $('#hidden_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                contact_id: {
                    remote: LANG.contact_id_already_exists,
                },
            },
            submitHandler: function(form) {
                $.ajax({
                    method: 'POST',
                    url: base_path + '/check-mobile',
                    dataType: 'json',
                    data: {
                        contact_id: function() {
                            return $('#hidden_id').val();
                        },
                        mobile_number: function() {
                            return $('#mobile').val();
                        },
                    },
                    beforeSend: function(xhr) {
                        __disable_submit_button($(form).find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.is_mobile_exists == true) {
                            swal({
                                title: LANG.sure,
                                text: result.msg,
                                icon: 'warning',
                                buttons: true,
                                dangerMode: true,
                            }).then(willContinue => {
                                if (willContinue) {
                                    submitQuickContactForm(form);
                                } else {
                                    $('#mobile').select();
                                }
                            });
                            
                        } else {
                            submitQuickContactForm(form);
                        }
                    },
                });
            },
        });
    $('.contact_modal').on('hidden.bs.modal', function() {
        $('form#quick_add_contact')
            .find('button[type="submit"]')
            .removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
    });

    //Updates for add sell
    $('select#discount_type, input#discount_amount, input#shipping_charges, \
        input#rp_redeemed_amount').change(function() {
        pos_total_row();
    });
    $('select#tax_rate_id').change(function() {
        var tax_rate = $(this)
            .find(':selected')
            .data('rate');
        __write_number($('input#tax_calculation_amount'), tax_rate);
        pos_total_row();
    });
    //Datetime picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    //Direct sell submit
    sell_form = $('form#add_sell_form');
    if ($('form#edit_sell_form').length) {
        sell_form = $('form#edit_sell_form');
        pos_total_row();
    }
    sell_form_validator = sell_form.validate();

    $('button#submit-sell, button#save-and-print').click(function(e) {
        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        var is_msp_valid = true;
        //Validate minimum selling price if hidden
        $('.pos_unit_price_inc_tax').each( function(){
            if (!$(this).is(":visible") && $(this).data('rule-min-value')) {
                var val = __read_number($(this));
                var error_msg_td = $(this).closest('tr').find('.pos_line_total_text').closest('td');
                if (val > $(this).data('rule-min-value')) {
                    is_msp_valid = false;
                    error_msg_td.append( '<label class="error">' + $(this).data('msg-min-value') + '</label>');
                } else {
                    error_msg_td.find('label.error').remove();
                }
            }
        });

        if (!is_msp_valid) {
            return false;
        }

        if ($(this).attr('id') == 'save-and-print') {
            $('#is_save_and_print').val(1);           
        } else {
            $('#is_save_and_print').val(0);
        }

        if ($('#reward_point_enabled').length) {
            var validate_rp = isValidatRewardPoint();
            if (!validate_rp['is_valid']) {
                toastr.error(validate_rp['msg']);
                return false;
            }
        }

        if ($('.enable_cash_denomination_for_payment_methods').length) {
            var payment_row = $('.enable_cash_denomination_for_payment_methods').closest('.payment_row');
            var is_valid = true;
            var payment_type = payment_row.find('.payment_types_dropdown').val();
            var denomination_for_payment_types = JSON.parse($('.enable_cash_denomination_for_payment_methods').val());
            if (denomination_for_payment_types.includes(payment_type) && payment_row.find('.is_strict').length && payment_row.find('.is_strict').val() === '1' ) {
                var payment_amount = __read_number(payment_row.find('.payment-amount'));
                var total_denomination = payment_row.find('input.denomination_total_amount').val();
                if (payment_amount != total_denomination ) {
                    is_valid = false;
                }
            }

            if (!is_valid) {
                payment_row.find('.cash_denomination_error').removeClass('hide');
                toastr.error(payment_row.find('.cash_denomination_error').text());
                e.preventDefault();
                return false;
            } else {
                payment_row.find('.cash_denomination_error').addClass('hide');
            }
        }

        if (sell_form.valid()) {
            window.onbeforeunload = null;
            $(this).attr('disabled', true);
            sell_form.submit();
        }
    });

    //REPAIR MODULE:check if repair module field is present send data to filter product
    var is_enabled_stock = null;
    if ($("#is_enabled_stock").length) {
        is_enabled_stock = $("#is_enabled_stock").val();
    }

    var device_model_id = null;
    if ($("#repair_model_id").length) {
        device_model_id = $("#repair_model_id").val();
    }

    //Show product list.

    /*
    $('#product_category_div .cat-parent-area label').click(function() {
        var parent_id = $(this).parent().find('input').val();
        $('#product_category_div .cat-sub-area .cat-container').css({'display':'none'});
        $('#product_category_div .cat-sub-area .cat_parent_'+parent_id).parent().css({'display':'inline-block'});
    });



    $('#product_category_div label').click(function() {
        $('div#product_list_body').html('');
        $('#product_category_div .cat-sub-area label').parent().removeClass('active');
        $(this).parent().addClass('active').siblings().removeClass('active');
    });

    var product_category_select = $('input[name="category_id"]:checked').attr('value');
    var product_brand_select = $('select#product_brand').val();
    if(product_category_select == 'all' || product_brand_select == 'all') {
        product_category_select = 99999999999999;
        product_brand_select = 99999999999999;
    }
    get_product_suggestion_list(
        product_category_select,
        product_brand_select,
        $('input#location_id').val(),
        null,
        is_enabled_stock,
        device_model_id
    );

    $('input[name="category_id"], select#product_brand, select#select_location_id').on('change', function(e) {
       
        $('input#suggestion_page').val(1);
        var location_id = $('input#location_id').val();
        if (location_id != '' || location_id != undefined) {
            get_product_suggestion_list(
                $('input[name="category_id"]:checked').attr('value'),
                $('select#product_brand').val(),
                $('input#location_id').val(),
                null
            );
        }
        get_featured_products();
    });
    */


    //Show product list / New update onchange category event
    
    /*
    //Stop OnLoad Calling
    var product_category_select = $('select#product_category').val();
    var product_brand_select = $('select#product_brand').val();
    if(product_category_select == 'all' || product_brand_select == 'all') {
        product_category_select = 99999999999999;
        product_brand_select = 99999999999999;
    }
    get_product_suggestion_list(
        product_category_select,
        product_brand_select,
        $('input#location_id').val(),
        null,
        is_enabled_stock,
        device_model_id
    );
    */


    $('select#product_category, select#product_brand, select#select_location_id').on('change', function(e) {
        $('input#suggestion_page').val(1);
        var location_id = $('input#location_id').val();
        /*
        if (location_id != '' || location_id != undefined) {
            get_product_suggestion_list(
                $('select#product_category').val(),
                $('select#product_brand').val(),
                $('input#location_id').val(),
                null
            );
        }
        */
        $('div#category-list-wrapper').show();
        $('div#subcategory-list-wrapper').hide();
        $('.back-event').addClass('d-none');
        $('div#product_list_body').html('');
 
        get_featured_products();
    });






    $(document).on('click', 'div.product_box', function() {
        //Check if location is not set then show error message.
        if ($('input#location_id').val() == '') {
            toastr.warning(LANG.select_location);
        } else {
            pos_product_row($(this).data('variation_id'));
        }
    });

    $(document).on('shown.bs.modal', '.row_description_modal', function() {
        $(this)
            .find('textarea')
            .first()
            .focus();
    });

    //Press enter on search product to jump into last quantty and vice-versa
    $('#search_product').keydown(function(e) {
        var key = e.which;
        if (key == 9) {
            // the tab key code
            e.preventDefault();
            if ($('#pos_table tbody tr').length > 0) {
                $('#pos_table tbody tr:last')
                    .find('input.pos_quantity')
                    .focus()
                    .select();
            }
        }
    });
    $('#pos_table').on('keypress', 'input.pos_quantity', function(e) {
        var key = e.which;
        if (key == 13) {
            // the enter key code
            $('#search_product').focus();
        }
    });

    $('#exchange_rate').change(function() {
        var curr_exchange_rate = 1;
        if ($(this).val()) {
            curr_exchange_rate = __read_number($(this));
        }
        var total_payable = __read_number($('input#final_total_input'));
        var shown_total = total_payable * curr_exchange_rate;
        $('span#total_payable').text(__currency_trans_from_en(shown_total, false));
    });

    $('select#price_group').change(function() {
        $('input#hidden_price_group').val($(this).val());
    });

    //Quick add product
    $(document).on('click', 'button.pos_add_quick_product', function() {
        var url = $(this).data('href');
        var container = $(this).data('container');
        $.ajax({
            url: url + '?product_for=pos',
            dataType: 'html',
            success: function(result) {
                $(container)
                    .html(result)
                    .modal('show');
                $('.os_exp_date').datepicker({
                    autoclose: true,
                    format: 'dd-mm-yyyy',
                    clearBtn: true,
                });
            },
        });
    });

    $(document).on('change', 'form#quick_add_product_form input#single_dpp', function() {
        var unit_price = __read_number($(this));
        $('table#quick_product_opening_stock_table tbody tr').each(function() {
            var input = $(this).find('input.unit_price');
            __write_number(input, unit_price);
            input.change();
        });
    });

    $(document).on('quickProductAdded', function(e) {
        //Check if location is not set then show error message.
        if ($('input#location_id').val() == '') {
            toastr.warning(LANG.select_location);
        } else {
            pos_product_row(e.variation.id);
        }
    });

    $('div.view_modal').on('show.bs.modal', function() {
        __currency_convert_recursively($(this));
    });

    $('table#pos_table').on('change', 'select.sub_unit', function() {
        var tr = $(this).closest('tr');
        var base_unit_selling_price = tr.find('input.hidden_base_unit_sell_price').val();

        var selected_option = $(this).find(':selected');

        var multiplier = parseFloat(selected_option.data('multiplier'));

        var allow_decimal = parseInt(selected_option.data('allow_decimal'));

        tr.find('input.base_unit_multiplier').val(multiplier);

        var unit_sp = base_unit_selling_price * multiplier;

        var sp_element = tr.find('input.pos_unit_price');
        __write_number(sp_element, unit_sp);

        sp_element.change();

        var qty_element = tr.find('input.pos_quantity');
        var base_max_avlbl = qty_element.data('qty_available');
        var error_msg_line = 'pos_max_qty_error';

        if (tr.find('select.lot_number').length > 0) {
            var lot_select = tr.find('select.lot_number');
            if (lot_select.val()) {
                base_max_avlbl = lot_select.find(':selected').data('qty_available');
                error_msg_line = 'lot_max_qty_error';
            }
        }

        qty_element.attr('data-decimal', allow_decimal);
        var abs_digit = true;
        if (allow_decimal) {
            abs_digit = false;
        }
        qty_element.rules('add', {
            abs_digit: abs_digit,
        });

        if (base_max_avlbl) {
            var max_avlbl = parseFloat(base_max_avlbl) / multiplier;
            var formated_max_avlbl = __number_f(max_avlbl);
            var unit_name = selected_option.data('unit_name');
            var max_err_msg = __translate(error_msg_line, {
                max_val: formated_max_avlbl,
                unit_name: unit_name,
            });
            qty_element.attr('data-rule-max-value', max_avlbl);
            qty_element.attr('data-msg-max-value', max_err_msg);
            qty_element.rules('add', {
                'max-value': max_avlbl,
                messages: {
                    'max-value': max_err_msg,
                },
            });
            qty_element.trigger('change');
        }
        adjustComboQty(tr);
    });

    //Confirmation before page load.
    window.onbeforeunload = function() {
        if($('form#edit_pos_sell_form').length == 0){
            if($('table#pos_table tbody tr').length > 0) {
                return LANG.sure;
            } else {
                return null;
            }
        }
    }
    $(window).resize(function() {
        var win_height = $(window).height();
        div_height = __calculate_amount('percentage', 63, win_height);
        $('div.pos_product_div').css('min-height', div_height + 'px');
        $('div.pos_product_div').css('max-height', div_height + 'px');
    });

    //Used for weighing scale barcode
    $('#weighing_scale_modal').on('shown.bs.modal', function (e) {

        //Attach the scan event
        onScan.attachTo(document, {
            suffixKeyCodes: [13], // enter-key expected at the end of a scan
            reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
            onScan: function(sCode, iQty) {
                console.log('Scanned: ' + iQty + 'x ' + sCode); 
                $('input#weighing_scale_barcode').val(sCode);
                $('button#weighing_scale_submit').trigger('click');
            },
            onScanError: function(oDebug) {
                console.log(oDebug); 
            },
            minLength: 2
            // onKeyDetect: function(iKeyCode){ // output all potentially relevant key events - great for debugging!
            //     console.log('Pressed: ' + iKeyCode);
            // }
        });

        $('input#weighing_scale_barcode').focus();
    });

    $('#weighing_scale_modal').on('hide.bs.modal', function (e) {
        //Detach from the document once modal is closed.
        onScan.detachFrom(document);
    });

    $('button#weighing_scale_submit').click(function(){

        var price_group = '';
        if ($('#price_group').length > 0) {
            price_group = $('#price_group').val();
        }

        if($('#weighing_scale_barcode').val().length > 0){
            pos_product_row(null, null, $('#weighing_scale_barcode').val());
            $('#weighing_scale_modal').modal('hide');
            $('input#weighing_scale_barcode').val('');
        } else{
            $('input#weighing_scale_barcode').focus();
        }
    });

    $('#show_featured_products').click( function(){
        if (!$('#featured_products_box').is(':visible')) {
            $('#featured_products_box').fadeIn();
        } else {
            $('#featured_products_box').fadeOut();
        }
    });
    validate_discount_field();
    set_payment_type_dropdown();
    if ($('#__is_mobile').length) {
        $('.pos_form_totals').css('margin-bottom', $('.pos-form-actions').height() - 30);
    }

    setInterval(function () {
        if ($('span.curr_datetime').length) {
            $('span.curr_datetime').html(__current_datetime());
        }
    }, 60000);

    set_search_fields();
});


function set_payment_type_dropdown() {
    var payment_settings = $('#location_id').data('default_payment_accounts');
    payment_settings = payment_settings ? payment_settings : [];
    enabled_payment_types = [];
    for (var key in payment_settings) {
        if (payment_settings[key] && payment_settings[key]['is_enabled']) {
            enabled_payment_types.push(key);
        }
    }
    if (enabled_payment_types.length) {
        $(".payment_types_dropdown > option").each(function() {
            //skip if advance
            if ($(this).val() && $(this).val() != 'advance') {
                if (enabled_payment_types.indexOf($(this).val()) != -1) {
                    $(this).removeClass('hide');
                } else {
                    $(this).addClass('hide');
                }
            }
        });
    }
}

function get_featured_products() {
    var location_id = $('#location_id').val();
    if (location_id && $('#featured_products_box').length > 0) {
        $.ajax({
            method: 'GET',
            url: '/sells/pos/get-featured-products/' + location_id,
            dataType: 'html',
            success: function(result) {
                if (result) {
                    $('#feature_product_div').removeClass('hide');
                    $('#featured_products_box').html(result);
                } else {
                    $('#feature_product_div').addClass('hide');
                    $('#featured_products_box').html('');
                }
            },
        });
    } else {
        $('#feature_product_div').addClass('hide');
        $('#featured_products_box').html('');
    }
}

function get_product_suggestion_list(category_id, brand_id, location_id, url = null, is_enabled_stock = null, repair_model_id = null) {
    if($('div#product_list_body').length == 0) {
        return false;
    }
    if (url == null) {
        url = '/sells/pos/get-product-suggestion';
    }
    $('#suggestion_page_loader').fadeIn(700);
    var page = $('input#suggestion_page').val();
    if (page == 1) {
        $('div#product_list_body').html('');
    }
    if ($('div#product_list_body').find('input#no_products_found').length > 0) {
        $('#suggestion_page_loader').fadeOut(700);
        return false;
    }
    $.ajax({
        method: 'GET',
        url: url,
        data: {
            category_id: category_id,
            brand_id: brand_id,
            location_id: location_id,
            page: page,
            is_enabled_stock: is_enabled_stock,
            repair_model_id: repair_model_id
        },
        dataType: 'html',
        success: function(result) {
          
            $('div#product_list_body').html('');
            $('div#product_list_body').append(result);
            $('#suggestion_page_loader').fadeOut(700);
        },
    });
}


//Get recent transactions
function get_recent_transactions(status, element_obj) {
    if (element_obj.length == 0) {
        return false;
    }
    var transaction_sub_type = $("#transaction_sub_type").val();
    $.ajax({
        method: 'GET',
        url: '/sells/pos/get-recent-transactions',
        data: { status: status , transaction_sub_type: transaction_sub_type},
        dataType: 'html',
        success: function(result) {
            element_obj.html(result);
            __currency_convert_recursively(element_obj);
        },
    });
}

//variation_id is null when weighing_scale_barcode is used.
function pos_product_row(variation_id = null, purchase_line_id = null, weighing_scale_barcode = null, quantity = 1, dp = []) {

    //Get item addition method
    var item_addtn_method = 0;
    var add_via_ajax = true;

    if (variation_id != null && $('#item_addition_method').length) {
        item_addtn_method = $('#item_addition_method').val();
    }

    if (item_addtn_method == 0) {
        add_via_ajax = true;
    } else {
        var is_added = false;

        //Search for variation id in each row of pos table
        $('#pos_table tbody')
            .find('tr')
            .each(function() {
                var row_v_id = $(this)
                    .find('.row_variation_id')
                    .val();
                var enable_sr_no = $(this)
                    .find('.enable_sr_no')
                    .val();
                var modifiers_exist = false;
                if ($(this).find('input.modifiers_exist').length > 0) {
                    modifiers_exist = true;
                }

                if (
                    row_v_id == variation_id &&
                    enable_sr_no !== '1' &&
                    !modifiers_exist &&
                    !is_added
                ) {
                    add_via_ajax = false;
                    is_added = true;

                    //Increment product quantity
                    qty_element = $(this).find('.pos_quantity');
                    var qty = __read_number(qty_element);
                    __write_number(qty_element, qty + 1);
                    qty_element.change();

                    round_row_to_iraqi_dinnar($(this));

                    $('input#search_product')
                        .focus()
                        .select();
                }
        });
    }

    if (add_via_ajax) {
        var product_row = $('input#product_row_count').val();
        var location_id = $('input#location_id').val();
        var customer_id = $('select#customer_id').val();
        var is_direct_sell = false;
        if (
            $('input[name="is_direct_sale"]').length > 0 &&
            $('input[name="is_direct_sale"]').val() == 1
        ) {
            is_direct_sell = true;
        }

        var disable_qty_alert = false;

        if ($('#disable_qty_alert').length) {
            disable_qty_alert = true;
        }

        var is_sales_order = $('#sale_type').length && $('#sale_type').val() == 'sales_order' ? true : false;

        var price_group = '';
        if ($('#price_group').length > 0) {
            price_group = parseInt($('#price_group').val());
        }

        //If default price group present
        if ($('#default_price_group').length > 0 && 
            price_group === '') {
            price_group = $('#default_price_group').val();
        }

        //If types of service selected give more priority
        if ($('#types_of_service_price_group').length > 0 && 
            $('#types_of_service_price_group').val()) {
            price_group = $('#types_of_service_price_group').val();
        }

        var is_draft=false;
        if($('input#status') && ($('input#status').val()=='quotation' || 
        $('input#status').val()=='draft')) {
            is_draft=true;
        }
        
        $.ajax({
            method: 'GET',
            url: '/sells/pos/get_product_row/' + variation_id + '/' + location_id,
            async: false,
            data: {
                product_row: product_row,
                customer_id: customer_id,
                is_direct_sell: is_direct_sell,
                price_group: price_group,
                purchase_line_id: purchase_line_id,
                weighing_scale_barcode: weighing_scale_barcode,
                quantity: quantity,
                is_sales_order: is_sales_order,
                disable_qty_alert: disable_qty_alert,
                is_draft: is_draft
            },
            dataType: 'json',
            success: function(result) {
             
                if (result.success) {
                    $('input#product_row_count').val(parseInt(product_row) + 1);
                    var el = result.html_content;
                    
                    if(!isEmpty(dp)) {
                        el = el.replace('promo="0', 'promo="1');
                        el = el.replace('promo-index="x', 'promo-index="'+dp.index+'');
                    }
                    $('table#pos_table tbody')
                        .append(el)
                        .find('input.pos_quantity');
                    //increment row count
                    //setTimeout(function(){
                        if(!isEmpty(dp)) {
                            
                            console.log('dp', dp);
                            var qty = quantity;
                            // var product_class = '.product_row_'+dp.product_sku+'[data-promo="1"]';
                            // $(product_class_temp).attr('data-promo-index', dp.index);
                           
                            var product_class = '.product_row_'+dp.product_sku+'[data-promo="1"][data-promo-index="'+dp.index+'"]';

                            var final_item_price = dp.product_price;
                            $(product_class+' .pos_line_total_text').text(__currency_trans_from_en(final_item_price * parseFloat(qty), true));
                            $(product_class+' .pos_unit_price_inc_tax').attr('value', final_item_price);
                            $(product_class+' .pos_unit_price').val(final_item_price);
                            $(product_class+' .is_promo').val(1);
                            $(product_class+' .hidden_base_unit_sell_price').val(final_item_price);
                            $(product_class+' .pos_line_total').val(final_item_price * parseFloat(qty));
                            $(product_class+' .original_price').val(final_item_price * parseFloat(qty));
                            $(product_class+' .title').append('<strong>('+dp.product_suffix+')</strong>');
                        }             

                        
                        var this_row = $('table#pos_table tbody')
                            .find('tr')
                            .last();
                        pos_each_row(this_row);

                        //For initial discount if present
                        var line_total = __read_number(this_row.find('input.pos_line_total'));
                        this_row.find('span.pos_line_total_text').text(line_total);

                        if(!isEmpty(dp)) {
                            pos_total_row_a();
                        } else {
                            pos_total_row();
                        }

                        //Check if multipler is present then multiply it when a new row is added.
                        if(__getUnitMultiplier(this_row) > 1){
                            this_row.find('select.sub_unit').trigger('change');
                        }

                        if (result.enable_sr_no == '1') {
                            var new_row = $('table#pos_table tbody')
                                .find('tr')
                                .last();
                            new_row.find('.row_edit_product_price_model').modal('show');
                        }

                        round_row_to_iraqi_dinnar(this_row);
                        __currency_convert_recursively(this_row);

                        $('input#search_product')
                            .focus()
                            .select();

                        //Used in restaurant module
                        if(isEmpty(dp)) {
                            if (result.html_modifier) {
                                $('table#pos_table tbody')
                                    .find('tr')
                                    .last()
                                    .find('td:first')
                                    .append(result.html_modifier);
                            }
                        }

                        //scroll bottom of items list
                        $(".pos_product_div").animate({ scrollTop: $('.pos_product_div').prop("scrollHeight")}, 1000);

                    //},1000);
                } else {
                    toastr.error(result.msg);
                    $('input#search_product')
                        .focus()
                        .select();
                }
            },
        });
    }
}



function isEmpty(obj) {
    for (const prop in obj) {
      if (Object.hasOwn(obj, prop)) {
        return false;
      }
    }
  
    return true;
  }

//Update values for each row
function pos_each_row(row_obj) {
  
    var unit_price = __read_number(row_obj.find('input.pos_unit_price'));

    var discounted_unit_price = calculate_discounted_unit_price(row_obj);
    var tax_rate = row_obj
        .find('select.tax_id')
        .find(':selected')
        .data('rate');


    var unit_price_inc_tax =
        discounted_unit_price + __calculate_amount('percentage', tax_rate, discounted_unit_price);

    __write_number(row_obj.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);

    var discount = __read_number(row_obj.find('input.row_discount_amount'));

    if (discount > 0) {
        var qty = __read_number(row_obj.find('input.pos_quantity'));
        var line_total = qty * unit_price_inc_tax;
        __write_number(row_obj.find('input.pos_line_total'), line_total);
    }

    //var unit_price_inc_tax = __read_number(row_obj.find('input.pos_unit_price_inc_tax'));

    __write_number(row_obj.find('input.item_tax'), unit_price_inc_tax - discounted_unit_price);

}

function pos_total_row() {
   
        var total_quantity = 0;
        var price_total = get_subtotal();
        $('table#pos_table tbody tr').each(function() {
            total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
        });

        //updating shipping charges
        $('span#shipping_charges_amount').text(
            __currency_trans_from_en(__read_number($('input#shipping_charges_modal')), false)
        );

        $('span.total_quantity').each(function() {
            $(this).html(__number_f(total_quantity));
        });

        //$('span.unit_price_total').html(unit_price_total);
        $('span.price_total').html(__currency_trans_from_en(price_total, false));
    
        calculate_billing_details(price_total);
 
    
}

function pos_total_row_a() {
  
        var total_quantity = 0;
        var price_total = get_subtotal();
        $('table#pos_table tbody tr').each(function() {
            total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
        });

        //updating shipping charges
        $('span#shipping_charges_amount').text(
            __currency_trans_from_en(__read_number($('input#shipping_charges_modal')), false)
        );

        $('span.total_quantity').each(function() {
            $(this).html(__number_f(total_quantity));
        });

        //$('span.unit_price_total').html(unit_price_total);
        $('span.price_total').html(__currency_trans_from_en(price_total, false));
    
        calculate_billing_details(price_total, false);
   
}

function pos_total_row_b() {
  
    var total_quantity = 0;
    var price_total = get_subtotal();
    $('table#pos_table tbody tr').each(function() {
        total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
    });

    //updating shipping charges
    $('span#shipping_charges_amount').text(
        __currency_trans_from_en(__read_number($('input#shipping_charges_modal')), false)
    );

    $('span.total_quantity').each(function() {
        $(this).html(__number_f(total_quantity));
    });

    //$('span.unit_price_total').html(unit_price_total);
    $('span.price_total').html(__currency_trans_from_en(price_total, false));

}

function get_subtotal() {

   dp_rules(0, 0, true, ['product', 'product_cat', 'matched_products'], cart_total_amount = false);

    var price_total = 0;

    $('table#pos_table tbody tr').each(function() {
        price_total = price_total + __read_number($(this).find('input.pos_line_total'));
    });

    //Go through the modifier prices.
    $('input.modifiers_price').each(function() {
        var modifier_price = __read_number($(this));
        var modifier_quantity = $(this).closest('.product_modifier').find('.modifiers_quantity').val();
        var modifier_subtotal = modifier_price * modifier_quantity;
        price_total = price_total + modifier_subtotal;
    });
    
    return price_total;
}

function calculate_billing_details(price_total, dp = true) {
  
    var discount = pos_discount(price_total);

    var html_total_payable = $('span#total_payable');

    if ($('#reward_point_enabled').length) {
        total_customer_reward = $('#rp_redeemed_amount').val();
        discount = parseFloat(discount) + parseFloat(total_customer_reward);

        if ($('input[name="is_direct_sale"]').length <= 0) {
            $('span#total_discount').text(__currency_trans_from_en(discount, false));
        }
    }

    var order_tax = pos_order_tax(price_total, discount);
    
    //Add shipping charges.
    var shipping_charges = __read_number($('input#shipping_charges'));

    var additional_expense = 0;
    //calculate additional expenses
    if ($('input#additional_expense_value_1').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_1'));
    }
    if ($('input#additional_expense_value_2').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_2'))
    }
    if ($('input#additional_expense_value_3').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_3'))
    }
    if ($('input#additional_expense_value_4').length > 0) {
        additional_expense += __read_number($('input#additional_expense_value_4'))
    }

    //Add packaging charge
    var packing_charge = 0;
    if ($('#types_of_service_id').length > 0 && 
            $('#types_of_service_id').val()) {
        packing_charge = __calculate_amount($('#packing_charge_type').val(), 
            __read_number($('input#packing_charge')), price_total);

        $('#packing_charge_text').text(__currency_trans_from_en(packing_charge, false));
    }

    // Add gratuity setting
    var gratuity_charges = 0;
    if ($('.gratuity_wrapper').length > 0){
        var gratuity_percentage = parseFloat($('.gratuity_wrapper').attr('data-percentage'));
        var gratuity_charges = __calculate_amount('percentage', gratuity_percentage, price_total)
        $('.gratuity_charges').text(parseFloat(gratuity_charges).toFixed(2));
    }

    // Tips added by input
    var tips_amount = $('#tips_amount').val();
    if(tips_amount != ""){
        $('#tips_text').text(tips_amount);     
        tips_amount = parseFloat(tips_amount);
    }else{
        $('#tips_text').text("0"); 
    }
    
  

    if($('.wrapper-of-sale-return').length > 0){
        var total_payable = price_total;
    }else{
        var total_payable = price_total + order_tax - discount + shipping_charges + packing_charge + additional_expense + gratuity_charges + tips_amount;
    }

    var rounding_multiple = $('#amount_rounding_method').val() ? parseFloat($('#amount_rounding_method').val()) : 0;
    var round_off_data = __round(total_payable, rounding_multiple);
    var total_payable_rounded = round_off_data.number;

    var round_off_amount = round_off_data.diff;
    if (round_off_amount != 0) {
        $('span#round_off_text').text(__currency_trans_from_en(round_off_amount, false));
    } else {
        $('span#round_off_text').text(0);
    }
    $('input#round_off_amount').val(round_off_amount);

    //__write_number($('input#final_total_input'), total_payable_rounded);
    var curr_exchange_rate = 1;
    if ($('#exchange_rate').length > 0 && $('#exchange_rate').val()) {
        curr_exchange_rate = __read_number($('#exchange_rate'));
    }
    var total_payable_temp = total_payable_rounded * curr_exchange_rate;
    $('#total_payable').attr('data-original-price', total_payable_temp);
    var shown_total = total_payable_temp;


    shown_total = dp_rules(shown_total, price_total, dp);

    var has_dp = 0;
    $('.has_dp').each(function(){
        var data_has_dp = $(this).attr('data-has-dp');
        if(data_has_dp) {
            has_dp = 1;
        }
    });
    $('#dp_flag').val(has_dp);

    var has_dp_temp = 0;
    for(var x = 0; x <= 30; x++) {
        var has_dp = $('#total_payable').attr('data-dp-type-amount-description-'+x);
        if(has_dp) {
            has_dp_temp = 1;
        }
    }
    if(has_dp_temp != 1) {
        $('#dp_cart_description').html('');
        $('#dp_discount').val('');
    }
    

    html_total_payable.text(__currency_trans_from_en(shown_total, false));
    __write_number($('input#final_total_input'), shown_total);
    $('span.total_payable_span').text(__currency_trans_from_en(shown_total, true));

    //Check if edit form then don't update price.
    if ($('form#edit_pos_sell_form').length == 0 && $('form#edit_sell_form').length == 0) {
        __write_number($('.payment-amount').first(), shown_total);
    }

    $(document).trigger('invoice_total_calculated');

    calculate_balance_due();
    //pos_total_row_b();
}

function conditions_operator(value, limit, operator){
    const operators = [null, 'eq_number', 'eq_text', 'eq_bool', 'gt', 'lt', 'gt_eq', 'lt_eq', 'not_eq_number', 'not_eq_text'];
    if (operators.includes(operator)) {
        switch (operator) {
            case 'eq_number':
                return '('+value+' === '+limit+')';
            case 'eq_text':
                return '('+value+' === '+limit+')';
            case 'eq_bool':
                return '((('+value+' === true && '+limit+' === true) || (('+value+' === false && '+limit+' === true))';
            case 'not_eq_number':
                return '('+value+' !== '+limit+')';
            case 'not_eq_text':
                return '('+value+' !== '+limit+')';
            case 'gt':
                return '('+value+' > '+limit+')';
            case 'lt':
                return '('+value+' < '+limit+')';
            case 'gt_eq':
                return '('+value+' >= '+limit+')';
            case 'lt_eq':
                return '('+value+' <= '+limit+')';
            default:
                return '('+value+' == '+limit+')';
        }
    }
}

function dp_rules(shown_total, sub_total, dp, type="all", cart_total_amount = true) {

    var type_arr = [];
    if(type == "all") {
        type_arr = ['product','product_cat','cart','promo_products', 'matched_products'];
    } else {
        type_arr = type;
    }
    var dp_rules = JSON.parse($('#dp_rules').val());
    //var dp_rules = localStorage.getItem('dp_rules') ? JSON.parse(localStorage.getItem('dp_rules')) : JSON.parse($('#dp_rules').val());
    // $.ajax({
    //     type: 'GET',
    //     url: '/sells/pos/get-dp-rules',
    //     dataType: 'json',
    //     async:false,
    //     success: function(result) {
    //         dp_rules = result;
    //     }
    // });


    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = dd + '-' + mm + '-' + yyyy;
    var total_rules = 0;
    var location_select = ($('#select_location_id option:selected').text()) ? $('#select_location_id option:selected').text() : $('#location_id_code').text();
    
    if(dp_rules && dp_rules.length > 0 && dp) {
        dp_rules.forEach(function(rule, index){

            var condition_bool = true;
            var start_date = rule['start-date'];
            var end_date = rule['end-date'];

            const dateStr = today;
            const [day1, month1, year1] = dateStr.split('-');
            const today_d = new Date(+year1, month1 - 1, +day1);

            const startStr = start_date;
            const [day2, month2, year2] = startStr.split('-');
            const start_d = new Date(+year2, month2 - 1, +day2);

            const endStr = end_date;
            const [day3, month3, year3] = endStr.split('-');
            const end_d = new Date(+year3, month3 - 1, +day3);

            // var d1 = start_date.split("-");
            // var d2 = end_date.split("-");
            // var c = today.split("-");

            // var start_d = new Date(d1);  // -1 because months are from 0 to 11
            // var end_d   = new Date(d2);
            // var today_d = new Date(c);

          
            if(
                (today_d > start_d && today_d < end_d) &&
                (rule['active']) &&
                (rule['business-location'] === location_select.match(/\(([^)]+)\)/)[1])
            ) {
                console.log(rule['business-location']);
                if(rule['conditions']) {
                    condition_bool = false;
                    rule['conditions'].forEach(function(condition, id) {
                        if(condition['active'] && condition['not_required'] != 1) {
                            if(condition['condition_data'] == 'items_in_cart') {
                                
                                if($('#pos_table tbody tr').length > 0 ) {

                                    var sku_arr = [];
                                    $('#pos_table tbody tr').each(function() {
                                        var promo = $(this).attr('data-promo');
                                        var sku = $(this).attr('data-sku');
                                        if(promo == 0 && condition['product_sku'].includes(sku)) {
                                            if(!sku_arr.includes(sku)) {
                                                sku_arr.push(sku);
                                            }
                                        }
                                    });
                                    var items_in_cart_bool = arraysEqual(sku_arr.sort(), condition['product_sku'].sort());

                                    // console.log('sku_arr', sku_arr.sort());
                                    // console.log('product_sku', condition['product_sku'].sort());
                                    // console.log('items_in_cart_bool', items_in_cart_bool);
                                    const matched_products = condition['product_sku'];
                                    if(items_in_cart_bool) {
                                        shown_total = dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr, [], matched_products);
                                    } else {
                                        dp_rule_prices_reset(rule['prices'], index, [], matched_products);
                                    }

                                }
                                
                                
                            }
                            if(condition['condition_data'] == 'cart_total_item_count') {
                                var total_count = 0;
                                $('.pos_quantity').each(function(){
                                    var promo = $(this).parents('.product_row').attr('data-promo');
                                    if(promo == 0) {
                                        total_count += parseFloat($(this).val());
                                    }
                                });
                                var value = parseFloat(total_count);
                                var limit = condition['limit_numb'];
                                var operator = condition['operator'];
                                if(eval(conditions_operator(value, limit, operator))) {
                                    shown_total = dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr);
                                } else {
                                    dp_rule_prices_reset(rule['prices'], index);
                                }
                            }
                            if(condition['condition_data'] == 'cart_total_amount') {
                                
                                    var sub_total_cta = 0;

                                    $('table#pos_table tbody tr').each(function() {
                                        sub_total_cta = sub_total_cta + __read_number($(this).find('input.pos_line_total'));
                                    });
                                
                                    //Go through the modifier prices.
                                    $('input.modifiers_price').each(function() {
                                        var modifier_price = __read_number($(this));
                                        var modifier_quantity = $(this).closest('.product_modifier').find('.modifiers_quantity').val();
                                        var modifier_subtotal = modifier_price * modifier_quantity;
                                        sub_total_cta = sub_total_cta + modifier_subtotal;
                                    });

                                   
                                    var value = sub_total_cta;
                                    var limit = condition['limit_numb'];
                                    var operator = condition['operator'];
                                    if(eval(conditions_operator(value, limit, operator))) {
                                        cart_total_amount = false;
                                        shown_total = dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr);
                                    } else {
                                        dp_rule_prices_reset(rule['prices'], index);
                                    }
                            }
                            if(condition['condition_data'] == 'cart_item_count') {
                                if(condition['cart_item_count_sku'].length > 0) {
                                    condition['cart_item_count_sku'].forEach(function(item){

                                        var items_in_cart_bool = false;
                                        let condition_temp;
                                        if($('#pos_table tbody tr').length > 0 ) {
                                            $('#pos_table tbody tr').each(function() {
                                                if($(this).attr('data-promo') != 1) {
                                                    var elem_sku = $(this).attr('data-sku');
                                                    var elem_qty = $(this).find('.pos_quantity').val();

                                                    var item_sku = item.product_sku;
                                                    var value = parseFloat(elem_qty);
                                                    var limit = item.item_limit;
                                                    var operator = item.operator_items;
                                                    
                                                    if(elem_sku == item_sku) {
                                                        if(condition['periodic_condition']) {
                                                            if(value >= limit) {
                                                                items_in_cart_bool = true; 
                                                                var qty = value/limit;
                                                                condition_temp = {
                                                                    'conditon' : condition['condition_data'],
                                                                    'value' : value,
                                                                    'limit' : limit,
                                                                    'periodic_condition' : condition['periodic_condition'],
                                                                    'item_qty' : parseInt(qty),
                                                                    'item' : item 
                                                                }; 
                                                            }
                                                        } else {
                                                            if(eval(conditions_operator(value, limit, operator))) {
                                                                items_in_cart_bool = true; 
                                                                //dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr);
                                                            }
                                                        }
                                                    }
                                                }
                                            });
                                        }  
                                        const matched_products = condition['cart_item_count_sku'].map((item) => item.product_sku);
                                        if(items_in_cart_bool) {
                                            shown_total = dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr, condition_temp, matched_products);
                                        } else {
                                            dp_rule_prices_reset(rule['prices'], index, condition_temp, matched_products);
                                        }
                                    });
                                }
                                
                                
                            }
                            if(condition['condition_data'] == 'cart_item_in_cat_count') {
                                if(condition['cart_item_count_sku'].length > 0) {
                                    
                                    condition['cart_item_count_sku'].forEach(function(item){
                                        var items_in_cart_bool = false;
                                        if($('#pos_table tbody tr').length > 0 ) {
                                            var cat_count = 0;
                                            $('#pos_table tbody tr').each(function() {
                                                var item_slug = item.product_cat_slug;
                                                var elem_cat_slug = $(this).attr('data-cat-slug');
                                                var elem_sub_cat_slug = $(this).attr('data-sub-cat-slug');
                                                if(item_slug == elem_cat_slug || item_slug == elem_sub_cat_slug) {
                                                    cat_count += __read_number($(this).find('input.pos_quantity'));
                                                }
                                                var value = parseFloat(cat_count);
                                                var limit = item.item_limit;
                                                var operator = item.operator_items;

                                                if(eval(conditions_operator(value, limit, operator))) {

                                                    items_in_cart_bool = true;
                                                    //shown_total = dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr);
                                                } 
                                            });
                                        } 
                                        const condition_temp = {
                                            'conditon' : condition['condition_data'],
                                        }
                                        const matched_products = condition['cart_item_count_sku'].map((item) => item.product_cat_slug );
                                        if(items_in_cart_bool) {
                                            shown_total = dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr, condition_temp, matched_products);
                                        } else {
                                            dp_rule_prices_reset(rule['prices'], index, condition_temp, matched_products);
                                        }
              
                                    });
                                }
                            }
                        }
                    });
                } else {
                    condition_bool = true;
                }
                if(rule['prices'] && condition_bool) {
              
                    shown_total = dp_rule_prices(rule['prices'], shown_total, index, sub_total, type_arr);
                    //pos_total_row_a();

                }
            }
        });
    }
    return shown_total;

}

function dp_rule_prices(prices, shown_total, index, sub_total, type_arr, condition = [], matched_products=[]){
    if(prices) {
        
        var sale = true;
		var is_sale = false;
        prices.forEach(function(price, id) {
            // console.log('target', price['target']);
            // console.log('type_arr', type_arr);
            // console.log('matched_products', matched_products);
            if(price['active']) {
                if(price['target'] == 'product' && type_arr.includes('product')) {
                    if(price['product_sku'].length > 0) {
                        if(price['already_sale'] && is_sale) {
                                sale = false;
                        }
                        if($('#pos_table tbody tr').length > 0 ) {
                            $('#pos_table tbody tr').each(function() {
                                var sku = $(this).attr('data-sku');
                      
                                var product_class = '.product_row_'+sku+'[data-promo="0"]';

                                if(($(product_class).length && sale) && price['product_sku'].includes(sku)) {
                                   dp_rule_compute_row(index, sku, product_class, price['type'], price['percent'], price['amount']);
                                }
                            });
                        }
                    }
                }
                if(price['target'] == 'product_cat' && type_arr.includes('product_cat')) {
                    if(price['product_cat_slug'].length > 0) {
                        if(price['already_sale'] && is_sale) {
                                sale = false;
                        }
                        if($('#pos_table tbody tr').length > 0 ) {
                            $('#pos_table tbody tr').each(function() {
                                var cat_slug = $(this).attr('data-cat-slug');
                                var sub_cat_slug = $(this).attr('data-sub-cat-slug');
                                if( (price['product_cat_slug'].includes(cat_slug) || price['product_cat_slug'].includes(sub_cat_slug)) 
                                    && sale
                                ) {
                                    var sku = $(this).attr('data-sku');
                                    //var product_class = '.product_row_'+ sku + '[data-has-dp="0"]';
                                    var product_class = '.product_row_'+ sku+'[data-promo="0"]';
                                    if($(product_class).length) {
                                        dp_rule_compute_row(index, sku, product_class, price['type'], price['percent'], price['amount']);
                                    }

                                }
                            });
                        }
                    }
                }
                if(price['target'] == 'cart' && type_arr.includes('cart')) {

                    var data_dp_type = price['type'] ?? 0;
                    var data_dp_percent = price['percent'] ?? 0;
                    var data_dp_amount = price['amount'] ?? 0;
                    var data_dp_cart_include_tax = price['cart_include_tax'] ?? 0;
                    var data_dp_cart_include_shipping = price['cart_include_shipping'] ?? 0;

                    if(data_dp_type != 0 && sub_total > 0) {
                        $('#total_payable').removeAttr('data-dp-type-amount-'+index);
                        $('#total_payable').removeAttr('data-dp-type-amount-description-'+index);
                        $('#total_payable').removeAttr('data-has-dp');
                        var discount = '';
                        if(data_dp_type == 'discount') {
                            if(data_dp_percent) {
                                price_update = '(-) '+ __currency_trans_from_en(parseFloat(sub_total) * (parseFloat(data_dp_amount)/100));
                                discount = '-'+(parseFloat(sub_total) * (parseFloat(data_dp_amount)/100));
                            } else {
                                price_update = '(-) '+ __currency_trans_from_en(parseFloat(data_dp_amount));
                                discount = '-'+parseFloat(data_dp_amount);
                            }
                        } else if(data_dp_type == 'increase') {
                            
                            if(data_dp_percent) {
                                price_update = '(+) '+__currency_trans_from_en(parseFloat(sub_total) * (parseFloat(data_dp_amount)/100));
                                discount = '+'+((parseFloat(sub_total) * (parseFloat(data_dp_amount)/100)));
                            } else {
                                price_update = '(+) '+__currency_trans_from_en(parseFloat(data_dp_amount));
                                discount = '+'+parseFloat(data_dp_amount);
                            }
                        } else if(data_dp_type == 'fixed_price') {
                            if(discount > parseFloat(data_dp_amount)) {
                                price_update = '(-) '+__currency_trans_from_en(sub_total-parseFloat(data_dp_amount));
                            } else {
                                price_update = '(+) '+__currency_trans_from_en(sub_total-parseFloat(data_dp_amount));
                            }
                            discount = parseFloat(data_dp_amount);
                        }

                        $('#total_payable').attr('data-dp-type-amount-'+index, parseFloat(discount).toFixed(2));

                        var dp_discount_arr = {
                            'label' : price['description'],
                            'discount' : price_update
                        };
                        $('#total_payable').attr('data-dp-type-amount-description-'+index, JSON.stringify(dp_discount_arr));

     
                        var dp_discount = [];
                        var added_element = '';
                        for(var x = 0; x <= index; x++) {

                            var description = $('#total_payable').attr('data-dp-type-amount-description-'+x);
                            if(description) {
                                var data = JSON.parse(description);
                                dp_discount.push(data);
                                added_element += '<li data-index="'+x+'">'+data.label+':'+data.discount+'</li>';    
                            }
                        }

                        $('#total_payable').attr('data-has-dp', 1);
                        $('#dp_discount').val(JSON.stringify(dp_discount));
                        $('#dp_cart_description').html(added_element);
                        
                    }

                    if(sub_total <= 0) {
                        $('#total_payable').removeAttr('data-dp-type-amount-'+index);
                        $('#total_payable').removeAttr('data-dp-type-amount-description-'+index);
                        $('#total_payable').removeAttr('data-has-dp');
                    }

                }
                if(price['target'] == 'promo_products' && type_arr.includes('promo_products')) {
                    // var lastPart = window.location.href.split("/").pop();
                    // if(lastPart != 'edit') {
                        price['promo_product_sku'].forEach(function(product, id) {
                            console.log('test');
                            var product_class = '.product_row[data-promo="1"][data-promo-index="'+index+'"]';
                            $(product_class).remove();
                            if( $(product_class).length == 0) {
                                var qty = product.product_qty;
                                if(condition.periodic_condition) {
                                    qty = condition.item_qty;
                                }
                                product.index = index;
                            pos_product_row(product.product_variation_id, null, null, qty, product);
                            }
                        });
                    //}
                }
                if(price['target'] == 'matched_products' && type_arr.includes('matched_products')) {

                    if(condition) {
                        if(condition.conditon == 'cart_item_in_cat_count') {
                            if(matched_products.length > 0) {
                                if($('#pos_table tbody tr').length > 0 ) {
                                    $('#pos_table tbody tr').each(function() {
                                        var cat_slug = $(this).attr('data-cat-slug');
                                        var sub_cat_slug = $(this).attr('data-sub-cat-slug');
                                        if( (matched_products.includes(cat_slug) || matched_products.includes(sub_cat_slug)) ) {
                                            var sku = $(this).attr('data-sku');
                                            //var product_class = '.product_row_'+ sku + '[data-has-dp="0"]';
                                            var product_class = '.product_row_'+sku+'[data-promo="0"]';
                                            if($(product_class).length) {
                                                dp_rule_compute_row(index, sku, product_class, price['type'], price['percent'], price['amount']);
                                            }
                                            
                                        }
                                    });
                                }
                            }
                        } else {
                            if($('#pos_table tbody tr').length > 0 ) {
                                $('#pos_table tbody tr').each(function() {
                                    var sku = $(this).attr('data-sku');
                                    //var product_class = '.product_row_'+ sku + '[data-has-dp="0"]';
                                    var product_class = '.product_row_'+sku+'[data-promo="0"]';
                                    if(($(product_class).length) && matched_products.includes(sku)) {
                                        dp_rule_compute_row(index, sku, product_class, price['type'], price['percent'], price['amount']);
                                    }
                                });
                            }
                        }
                    }
                }
            }
        });
        var operand = 0;
        for(var x = 0; x <= index; x++) {
            var type_amount = $('#total_payable').attr('data-dp-type-amount-'+x);
            if(type_amount) {
                operand += parseFloat(type_amount);
            }
        }
        shown_total = parseFloat($('#total_payable').attr('data-original-price')) + (parseFloat(operand));
    }
    return shown_total;
}

function dp_rule_prices_reset(prices, index, condition = [], matched_products = []) {
    if(prices) {
        prices.forEach(function(price, id) {
            if(price['active']) {
                if(price['target'] == 'product') {
   
                    if(price['product_sku'].length > 0) {
 
                        if($('#pos_table tbody tr').length > 0 ) {
                            $('#pos_table tbody tr').each(function() {
                                var sku = $(this).attr('data-sku');
                                var tr_index = $(this).attr('data-dp-index');
                                //var product_class = '.product_row_'+ sku + '[data-has-dp="0"]';
                                var product_class = '.product_row_'+ sku;
                                if( $(product_class).length && price['product_sku'].includes(sku) && ( tr_index == index ) ) {
                        
                                    dp_rule_reset_row(index, product_class);
                                    // var orig_price = $(product_class+' .original_price').val();
                                    // var qty = $(product_class+' .input_quantity').val();
                                    // var final_item_price = orig_price;
                                    // $(product_class+' .pos_line_total_text').text(__currency_trans_from_en(final_item_price * parseFloat(qty), true));
                                    // $(product_class+' .pos_unit_price_inc_tax').val(final_item_price);
                                    // $(product_class+' .pos_unit_price_inc_tax').attr('value',final_item_price);
                                    // $(product_class+' .pos_unit_price').val(final_item_price);
                                    // $(product_class+' .hidden_base_unit_sell_price').val(final_item_price);
                                    // $(product_class+' .pos_line_total').val(final_item_price * parseFloat(qty));
                              
                                    // $(product_class).removeAttr('data-dp-action-'+index);
                                    // $(product_class).attr('data-has-dp', 0);
                                }
                            });
                        }
                    }
                }
                if(price['target'] == 'product_cat') {
                    if(price['product_cat_slug'].length > 0) {
                        if($('#pos_table tbody tr').length > 0 ) {
                            $('#pos_table tbody tr').each(function() {
                                var cat_slug = $(this).attr('data-cat-slug');
                                var sub_cat_slug = $(this).attr('data-sub-cat-slug');
                                var tr_index = $(this).attr('data-dp-index');

                                if( (price['product_cat_slug'].includes(cat_slug) || price['product_cat_slug'].includes(sub_cat_slug)) && ( tr_index == index ) ) {
                                    var sku = $(this).attr('data-sku');
                                    //var product_class = '.product_row_'+ sku + '[data-has-dp="0"]';
                                    var product_class = '.product_row_'+ sku;
                                    if($(product_class).length) {

                                        dp_rule_reset_row(index, product_class);
                                        // var orig_price = $(product_class+' .original_price').val();
                                        // var qty = $(product_class+' .input_quantity').val();
                                        // var final_item_price = orig_price;
                                        // $(product_class+' .pos_line_total_text').text(__currency_trans_from_en(final_item_price * parseFloat(qty), true));
                                        // $(product_class+' .pos_unit_price_inc_tax').val(final_item_price);
                                        // $(product_class+' .pos_unit_price_inc_tax').attr('value',final_item_price);
                                        // $(product_class+' .pos_unit_price').val(final_item_price);
                                        // $(product_class+' .hidden_base_unit_sell_price').val(final_item_price);
                                        // $(product_class+' .pos_line_total').val(final_item_price * parseFloat(qty));

                                        // $(product_class).removeAttr('data-dp-action-'+index);
                                        // $(product_class).attr('data-has-dp', 0);
                                    }

                                }
                            });
                        }
                    }
                }
                if(price['target'] == 'cart') {
            
                    $('#total_payable').removeAttr('data-dp-type-amount-'+index);
                    $('#total_payable').removeAttr('data-dp-type-amount-description-'+index);
                    $('#total_payable').removeAttr('data-has-dp');
                }
                if(price['target'] == 'promo_products') {
                    price['promo_product_sku'].forEach(function(product, id) {
                        var product_class = '.product_row_'+ product.product_sku+'[data-promo="1"][data-promo-index="'+index+'"]';
                        $(product_class).remove();
                    });
                }
                if(price['target'] == 'matched_products') {
                    if(condition) {
                        if(condition.conditon == 'cart_item_in_cat_count') {
                            if(matched_products.length > 0) {
                                if($('#pos_table tbody tr').length > 0 ) {
                                    $('#pos_table tbody tr').each(function() {
                                        var cat_slug = $(this).attr('data-cat-slug');
                                        var sub_cat_slug = $(this).attr('data-sub-cat-slug');
                                        var tr_index = $(this).attr('data-dp-index');
                                        if( (matched_products.includes(cat_slug) || matched_products.includes(sub_cat_slug)) && ( tr_index == index ) ) {
                                            var sku = $(this).attr('data-sku');
                                            var product_class = '.product_row_'+sku+'[data-promo="0"]';
                                            if($(product_class).length) {

                                                //dp_rule_reset_row(index, product_class);
                                                var orig_price = $(product_class+' .original_price').val();
                                                var qty = $(product_class+' .input_quantity').val();
                                                var final_item_price = orig_price;
                                                $(product_class+' .pos_line_total_text').text(__currency_trans_from_en(final_item_price * parseFloat(qty), true));
                                                $(product_class+' .pos_unit_price_inc_tax').val(final_item_price);
                                                $(product_class+' .pos_unit_price_inc_tax').attr('value',final_item_price);
                                                $(product_class+' .pos_unit_price').val(final_item_price);
                                                $(product_class+' .hidden_base_unit_sell_price').val(final_item_price);
                                                $(product_class+' .pos_line_total').val(final_item_price * parseFloat(qty));
        
                                                $(product_class).removeAttr('data-dp-action-'+index);
                                                $(product_class).attr('data-has-dp', 0);

                                            }
                                            
                                        }
                                    });
                                }
                            }
                        } else {
                            if($('#pos_table tbody tr').length > 0 ) {
                                $('#pos_table tbody tr').each(function() {
                                    var sku = $(this).attr('data-sku');
                                    //var product_class = '.product_row_'+ sku + '[data-has-dp="0"]';
                                    var product_class = '.product_row_'+sku+'[data-promo="0"]';
                                    var tr_index = $(this).attr('data-dp-index');
                                    if( ($(product_class).length) && matched_products.includes(sku) && ( tr_index == index ) ) {

                                        dp_rule_reset_row(index, product_class);
                                        // var orig_price = $(product_class+' .original_price').val();
                                        // var qty = $(product_class+' .input_quantity').val();
                                        // var final_item_price = orig_price;
                                        // $(product_class+' .pos_line_total_text').text(__currency_trans_from_en(final_item_price * parseFloat(qty), true));
                                        // $(product_class+' .pos_unit_price_inc_tax').val(final_item_price);
                                        // $(product_class+' .pos_unit_price_inc_tax').attr('value',final_item_price);
                                        // $(product_class+' .pos_unit_price').val(final_item_price);
                                        // $(product_class+' .hidden_base_unit_sell_price').val(final_item_price);
                                        // $(product_class+' .pos_line_total').val(final_item_price * parseFloat(qty));

                                        // $(product_class).removeAttr('data-dp-action-'+index);
                                        // $(product_class).attr('data-has-dp', 0);
                                        
                                    }
                                });
                            }
                        }
                    }
                }
            }
        });
    }
}

function dp_rule_compute_row(index, sku, product_class, data_dp_type = 0, data_dp_percent = 0, data_dp_amount = 0) {
    // var lastPart = window.location.href.split("/").pop();
    // if(lastPart != 'edit') {
        const action = {
            'type': data_dp_type,
            'percent' : data_dp_percent,
            'amount' : data_dp_amount
        }
        $('.product_row_'+ sku).attr('data-dp-action-'+index, JSON.stringify(action));
        $('.product_row_'+ sku + '[data-has-dp="0"][data-promo="0"]').attr('data-dp-index', index);
        $('.product_row_'+ sku + '[data-has-dp="0"][data-promo="0"]').attr('data-has-dp', 1);

        var orig_price = $(product_class+' .original_price').val();
        var item_price = orig_price;

        for(var x = 0; x <= index; x++) {
            var condition_action_attr = $('.product_row_'+ sku).attr('data-dp-action-'+x);
            if(condition_action_attr) {
                var condition_action = JSON.parse(condition_action_attr);

                data_dp_type = condition_action.type;
                data_dp_percent = condition_action.percent;
                data_dp_amount = condition_action.amount;

                if(data_dp_type == 'discount') {
                    if(data_dp_percent) {
                        item_price = parseFloat(item_price) - (parseFloat(item_price) * (parseFloat(data_dp_amount)/100));
                    } else {
                        item_price = parseFloat(item_price) - parseFloat(data_dp_amount);
                    }
                } else if(data_dp_type == 'increase') {
                    
                    if(data_dp_percent) {
                        item_price = parseFloat(item_price) + (parseFloat(item_price) * (parseFloat(data_dp_amount)/100));
            
                    } else {
                        item_price = parseFloat(item_price) + parseFloat(data_dp_amount);
                    }
                } else if(data_dp_type == 'fixed_price') {
                    item_price =  parseFloat(data_dp_amount);
                }
            }
        }

        var qty = $(product_class+' .input_quantity').val();
        var final_item_price = item_price;
        var final_orig_price = orig_price;
        $(product_class+' .pos_line_total_text').text(__currency_trans_from_en(final_item_price * parseFloat(qty), true));
        $(product_class+' .pos_unit_price_inc_tax').val(final_item_price);
        $(product_class+' .pos_unit_price_inc_tax').attr('value',final_item_price);
        $(product_class+' .pos_unit_price').val(final_item_price);
        $(product_class+' .hidden_base_unit_sell_price').val(final_item_price);
        $(product_class+' .pos_line_total').val(final_item_price * parseFloat(qty));
    // }
}

function dp_rule_reset_row(index, product_class) {
        var orig_price = $(product_class+' .original_price').val();
        var qty = $(product_class+' .input_quantity').val();
        var final_item_price = orig_price;
        $(product_class+' .pos_line_total_text').text(__currency_trans_from_en(final_item_price * parseFloat(qty), true));
        $(product_class+' .pos_unit_price_inc_tax').val(final_item_price);
        $(product_class+' .pos_unit_price_inc_tax').attr('value',final_item_price);
        $(product_class+' .pos_unit_price').val(final_item_price);
        $(product_class+' .hidden_base_unit_sell_price').val(final_item_price);
        $(product_class+' .pos_line_total').val(final_item_price * parseFloat(qty));

        $(product_class).removeAttr('data-dp-action-'+index);
        $(product_class).attr('data-has-dp', 0);
}

function arraysEqual(a, b) {
  if (a === b) return true;
  if (a == null || b == null) return false;
  if (a.length !== b.length) return false;

  // If you don't care about the order of the elements inside
  // the array, you should sort both arrays here.
  // Please note that calling sort on an array will modify that array.
  // you might want to clone your array first.

  for (var i = 0; i < a.length; ++i) {
    if (a[i] !== b[i]) return false;
  }
  return true;
}

function pos_discount(total_amount) {
    var calculation_type = $('#discount_type').val();
    var calculation_amount = __read_number($('#discount_amount'));

    var discount = __calculate_amount(calculation_type, calculation_amount, total_amount);

    $('span#total_discount').text(__currency_trans_from_en(discount, false));

    return discount;
}

function pos_order_tax(price_total, discount) {
    var tax_rate_id = $('#tax_rate_id').val();
    var calculation_type = 'percentage';
    var calculation_amount = __read_number($('#tax_calculation_amount'));
    var total_amount = price_total - discount;

    if (tax_rate_id) {
        var order_tax = __calculate_amount(calculation_type, calculation_amount, total_amount);
    } else {
        var order_tax = 0;
    }

    $('span#order_tax').text(__currency_trans_from_en(order_tax, false));

    return order_tax;
}

function calculate_balance_due() {
    var total_payable = __read_number($('#final_total_input'));
    var total_paying = 0;

    var elem_id = '#' + $('.modal.in').attr('id') + ' ';

    $(elem_id+'#payment_rows_div')
        .find('.payment-amount')
        .each(function() {
            if (parseFloat($(this).val())) {
                total_paying += __read_number($(this));
            }
        });
    
    localStorage.setItem('amount_tendered', total_paying);

    var bal_due = total_payable - total_paying;
    var change_return = 0;

    //change_return
    if (bal_due < 0 || Math.abs(bal_due) < 0.05) {
        __write_number($('input#change_return'), bal_due * -1);
        $('span.change_return_span').text(__currency_trans_from_en(bal_due * -1, true));
        change_return = bal_due * -1;
        bal_due = 0;
    } else {
        __write_number($('input#change_return'), 0);
        $('span.change_return_span').text(__currency_trans_from_en(0, true));
        change_return = 0;
        
    }

    if (change_return !== 0) {
        $('#change_return_payment_data').removeClass('hide');
    } else {
        $('#change_return_payment_data').addClass('hide');
    }

    __write_number($('input#total_paying_input'), total_paying);
    $('span.total_paying').text(__currency_trans_from_en(total_paying, true));

    __write_number($('input#in_balance_due'), bal_due);
    $('span.balance_due').text(__currency_trans_from_en(bal_due, true));

    __highlight(bal_due * -1, $('span.balance_due'));
    __highlight(change_return * -1, $('span.change_return_span'));
}

function isValidPosForm() {
    flag = true;
    $('span.error').remove();

    if ($('select#customer_id').val() == null) {
        flag = false;
        error = '<span class="error">' + LANG.required + '</span>';
        $(error).insertAfter($('select#customer_id').parent('div'));
    }

    if ($('tr.product_row').length == 0) {
        flag = false;
        error = '<span class="error">' + LANG.no_products + '</span>';
        $(error).insertAfter($('input#search_product').parent('div'));
    }

    return flag;
}

function reset_pos_form(){

	//If on edit page then redirect to Add POS page
	if($('form#edit_pos_sell_form').length > 0){
		setTimeout(function() {
			window.location = $("input#pos_redirect_url").val();
		}, 4000);
		return true;
	}
	
    //reset all repair defects tags
    if ($("#repair_defects").length > 0) {
        tagify_repair_defects.removeAllTags();
    }

	if(pos_form_obj[0]){
		pos_form_obj[0].reset();
	}
	if(sell_form[0]){
		sell_form[0].reset();
	}
	set_default_customer();
	set_location();

	$('tr.product_row').remove();
	$('span.total_quantity, span.price_total, span#total_discount, span#order_tax, span#total_payable, span#shipping_charges_amount').text(0);
	$('span.total_payable_span', 'span.total_paying', 'span.balance_due').text(0);

	$('#modal_payment').find('.remove_payment_row').each( function(){
		$(this).closest('.payment_row').remove();
	});

    if ($('#is_credit_sale').length) {
        $('#is_credit_sale').val(0);
    }

    //Gratuity Check
    if ($('.gratuity_charges').length) {
        $('.gratuity_charges').text(0);
    }

	//Reset discount
	__write_number($('input#discount_amount'), $('input#discount_amount').data('default'));
	$('input#discount_type').val($('input#discount_type').data('default'));

	//Reset tax rate
	$('input#tax_rate_id').val($('input#tax_rate_id').data('default'));
	__write_number($('input#tax_calculation_amount'), $('input#tax_calculation_amount').data('default'));

	$('select.payment_types_dropdown').val('cash').trigger('change');
	$('#price_group').trigger('change');

	//Reset shipping
	__write_number($('input#shipping_charges'), $('input#shipping_charges').data('default'));
	$('input#shipping_details').val($('input#shipping_details').data('default'));
    $('input#shipping_address, input#shipping_status, input#delivered_to').val('');
	if($('input#is_recurring').length > 0){
		$('input#is_recurring').iCheck('update');
	};
    if($('#invoice_layout_id').length > 0){
        $('#invoice_layout_id').trigger('change');
    };
    $('span#round_off_text').text(0);

    //repair module extra  fields reset
    if ($('#repair_device_id').length > 0) {
        $('#repair_device_id').val('').trigger('change');
    }

    //Status is hidden in sales order
    if ($('#status').length > 0 && $('#status').is(":visible")) {
        $('#status').val('').trigger('change');
    }
    if ($('#transaction_date').length > 0) {
        $('#transaction_date').data("DateTimePicker").date(moment());
    }
    if ($('.paid_on').length > 0) {
        $('.paid_on').data("DateTimePicker").date(moment());
    }
    if ($('#commission_agent').length > 0) {
        $('#commission_agent').val('').trigger('change');
    } 

    //reset contact due
    $('.contact_due_text').find('span').text('');
    $('.contact_due_text').addClass('hide');

    localStorage.setItem('pos_table', '');
    localStorage.setItem('pos_location', '');
    localStorage.setItem('total_payable', 0);
    localStorage.setItem('total_discount', 0);
    localStorage.setItem('order_tax', 0);
    localStorage.setItem('gratuity_charges', 0);
    localStorage.setItem('tips_text', 0);
    localStorage.setItem('gratuity_charges_label', 0);
    localStorage.setItem('packing_charge_text', 0);
    localStorage.setItem('amount_change', 0);

    $(document).trigger('sell_form_reset');
    calculate_billing_details(0, true);

    //waka
    $('#dp_flag').val(0);
    $('#dp_cart_description').html('');
    $('#dp_discount').val('');

    $('.table-chair-btn').each(function(){
        $(this).removeClass('active');
    });

    //Disabled table in pos
    if($('select[name="res_table_id"]').length != 0){
        check_table_is_occupied();
    }
    
}

function set_default_customer() {
    var default_customer_id = $('#default_customer_id').val();
    var default_customer_name = $('#default_customer_name').val();
    var default_customer_balance = $('#default_customer_balance').val();
    var default_customer_address = $('#default_customer_address').val();
    var exists = default_customer_id ? $('select#customer_id option[value=' + default_customer_id + ']').length : 0;
    if (exists == 0 && default_customer_id) {
        $('select#customer_id').append(
            $('<option>', { value: default_customer_id, text: default_customer_name })
        );
    }
    $('#advance_balance_text').text(__currency_trans_from_en(default_customer_balance), true);
    $('#advance_balance').val(default_customer_balance);
    $('#shipping_address_modal').val(default_customer_address);
    if (default_customer_address) {
        $('#shipping_address').val(default_customer_address);
    }
    $('select#customer_id')
        .val(default_customer_id)
        .trigger('change');

    if ($('#default_selling_price_group').length) {
        $('#price_group').val($('#default_selling_price_group').val());
        $('#price_group').change();
    }

    //initialize tags input (tagify)
    if ($("textarea#repair_defects").length > 0 && !customer_set) {
        let suggestions = [];
        if ($("input#pos_repair_defects_suggestion").length > 0 && $("input#pos_repair_defects_suggestion").val().length > 2) {
            suggestions = JSON.parse($("input#pos_repair_defects_suggestion").val());    
        }
        let repair_defects = document.querySelector('textarea#repair_defects');
        tagify_repair_defects = new Tagify(repair_defects, {
                  whitelist: suggestions,
                  maxTags: 100,
                  dropdown: {
                    maxItems: 100,           // <- mixumum allowed rendered suggestions
                    classname: "tags-look", // <- custom classname for this dropdown, so it could be targeted
                    enabled: 0,             // <- show suggestions on focus
                    closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
                  }
                });
    }

    customer_set = true;
}

//Set the location and initialize printer
function set_location() {
    if ($('select#select_location_id').length == 1) {
        $('input#location_id').val($('select#select_location_id').val());
        $('input#location_id').data(
            'receipt_printer_type',
            $('select#select_location_id')
                .find(':selected')
                .data('receipt_printer_type')
        );
        $('input#location_id').data(
            'default_payment_accounts',
            $('select#select_location_id')
                .find(':selected')
                .data('default_payment_accounts')
        );

        $('input#location_id').attr(
            'data-default_price_group',
            $('select#select_location_id')
                .find(':selected')
                .data('default_price_group')
        );
    }

    if ($('input#location_id').val()) {
        $('input#search_product')
            .prop('disabled', false)
            .focus();
    } else {
        $('input#search_product').prop('disabled', true);
    }

    initialize_printer();
}

function initialize_printer() {
    if ($('input#location_id').data('receipt_printer_type') == 'printer') {
        initializeSocket();
    }
}

$('body').on('click', 'label', function(e) {
    var field_id = $(this).attr('for');
    if (field_id) {
        if ($('#' + field_id).hasClass('select2')) {
            $('#' + field_id).select2('open');
            return false;
        }
    }
});

$('body').on('focus', 'select', function(e) {
    var field_id = $(this).attr('id');
    if (field_id) {
        if ($('#' + field_id).hasClass('select2')) {
            $('#' + field_id).select2('open');
            return false;
        }
    }
});

function round_row_to_iraqi_dinnar(row) {
    if (iraqi_selling_price_adjustment) {
        var element = row.find('input.pos_unit_price_inc_tax');
        var unit_price = round_to_iraqi_dinnar(__read_number(element));
        __write_number(element, unit_price);
        element.change();
    }
}

function pos_print(receipt) {
    //If printer type then connect with websocket
    if (receipt.print_type == 'printer') {
        var content = receipt;
        content.type = 'print-receipt';

        //Check if ready or not, then print.
        if (socket != null && socket.readyState == 1) {
            socket.send(JSON.stringify(content));
        } else {
            initializeSocket();
            setTimeout(function() {
                socket.send(JSON.stringify(content));
            }, 700);
        }

    } else if (receipt.html_content != '') {
        var title = document.title;
        if (typeof receipt.print_title != 'undefined') {
            document.title = receipt.print_title;
        }

        //If printer type browser then print content
        $('#receipt_section').html(receipt.html_content);
        __currency_convert_recursively($('#receipt_section'));
        __print_receipt('receipt_section');

        setTimeout(function() {
            document.title = title;
        }, 1200);
    }
}

function calculate_discounted_unit_price(row) {
    var this_unit_price = __read_number(row.find('input.pos_unit_price'));
    var row_discounted_unit_price = this_unit_price;
    var row_discount_type = row.find('select.row_discount_type').val();
    var row_discount_amount = __read_number(row.find('input.row_discount_amount'));
    if (row_discount_amount) {
        if (row_discount_type == 'fixed') {
            row_discounted_unit_price = this_unit_price - row_discount_amount;
        } else {
            row_discounted_unit_price = __substract_percent(this_unit_price, row_discount_amount);
        }
    }

    return row_discounted_unit_price;
}

function get_unit_price_from_discounted_unit_price(row, discounted_unit_price) {
    var this_unit_price = discounted_unit_price;
    var row_discount_type = row.find('select.row_discount_type').val();
    var row_discount_amount = __read_number(row.find('input.row_discount_amount'));
    if (row_discount_amount) {
        if (row_discount_type == 'fixed') {
            this_unit_price = discounted_unit_price + row_discount_amount;
        } else {
            this_unit_price = __get_principle(discounted_unit_price, row_discount_amount, true);
        }
    }

    return this_unit_price;
}

//Update quantity if line subtotal changes
$('table#pos_table tbody').on('change', 'input.pos_line_total', function() {
    var subtotal = __read_number($(this));
    var tr = $(this).parents('tr');
    var quantity_element = tr.find('input.pos_quantity');
    var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
    var quantity = subtotal / unit_price_inc_tax;
    __write_number(quantity_element, quantity);

    if (sell_form_validator) {
        sell_form_validator.element(quantity_element);
    }
    if (pos_form_validator) {
        pos_form_validator.element(quantity_element);
    }
    tr.find('span.pos_line_total_text').text(__currency_trans_from_en(subtotal, true));

    pos_total_row();
});

$('div#product_list_body').on('scroll', function() {
    if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        var page = parseInt($('#suggestion_page').val());
        page += 1;
        $('#suggestion_page').val(page);
        var location_id = $('input#location_id').val();
        var category_id = $('select#product_category').val();
        var brand_id = $('select#product_brand').val();

        var is_enabled_stock = null;
        if ($("#is_enabled_stock").length) {
            is_enabled_stock = $("#is_enabled_stock").val();
        }

        var device_model_id = null;
        if ($("#repair_model_id").length) {
            device_model_id = $("#repair_model_id").val();
        }

        get_product_suggestion_list(category_id, brand_id, location_id, null, is_enabled_stock, device_model_id);
    }
});

$(document).on('ifChecked', '#is_recurring', function() {
    $('#recurringInvoiceModal').modal('show');
});

$(document).on('shown.bs.modal', '#recurringInvoiceModal', function() {
    $('input#recur_interval').focus();
});

$(document).on('click', '#select_all_service_staff', function() {
    var val = $('#res_waiter_id').val();
    $('#pos_table tbody')
        .find('select.order_line_service_staff')
        .each(function() {
            $(this)
                .val(val)
                .change();
        });
});

$(document).on('click', '.print-invoice-link', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('href') + "?check_location=true",
        dataType: 'json',
        success: function(result) {
            if (result.success == 1) {
                //Check if enabled or not
                if (result.receipt.is_enabled) {
                    pos_print(result.receipt);
                }
            } else {
                toastr.error(result.msg);
            }

        },
    });
});

function getCustomerRewardPoints() {
    if ($('#reward_point_enabled').length <= 0) {
        return false;
    }
    var is_edit = $('form#edit_sell_form').length || 
    $('form#edit_pos_sell_form').length ? true : false;
    if (is_edit && !customer_set) {
        return false;
    }

    var customer_id = $('#customer_id').val();

    $.ajax({
        method: 'POST',
        url: '/sells/pos/get-reward-details',
        data: { 
            customer_id: customer_id
        },
        dataType: 'json',
        success: function(result) {
            $('#available_rp').text(result.points);
            $('#rp_redeemed_modal').data('max_points', result.points);
            updateRedeemedAmount();
            $('#rp_redeemed_amount').change()
        },
    });
}

function updateRedeemedAmount(argument) {
    var points = $('#rp_redeemed_modal').val().trim();
    points = points == '' ? 0 : parseInt(points);
    var amount_per_unit_point = parseFloat($('#rp_redeemed_modal').data('amount_per_unit_point'));
    var redeemed_amount = points * amount_per_unit_point;
    $('#rp_redeemed_amount_text').text(__currency_trans_from_en(redeemed_amount, true));
    $('#rp_redeemed').val(points);
    $('#rp_redeemed_amount').val(redeemed_amount);
}

$(document).on('change', 'select#customer_id', function(){
    var default_customer_id = $('#default_customer_id').val();
    if ($(this).val() == default_customer_id) {
        //Disable reward points for walkin customers
        if ($('#rp_redeemed_modal').length) {
            $('#rp_redeemed_modal').val('');
            $('#rp_redeemed_modal').change();
            $('#rp_redeemed_modal').attr('disabled', true);
            $('#available_rp').text('');
            updateRedeemedAmount();
            pos_total_row();
        }
    } else {
        if ($('#rp_redeemed_modal').length) {
            $('#rp_redeemed_modal').removeAttr('disabled');
        }
        getCustomerRewardPoints();
    }

    get_sales_orders();
});

$(document).on('change', '#rp_redeemed_modal', function(){
    var points = $(this).val().trim();
    points = points == '' ? 0 : parseInt(points);
    var amount_per_unit_point = parseFloat($(this).data('amount_per_unit_point'));
    var redeemed_amount = points * amount_per_unit_point;
    $('#rp_redeemed_amount_text').text(__currency_trans_from_en(redeemed_amount, true));
    var reward_validation = isValidatRewardPoint();
    if (!reward_validation['is_valid']) {
        toastr.error(reward_validation['msg']);
        $('#rp_redeemed_modal').select();
    }
});

$(document).on('change', '.direct_sell_rp_input', function(){
    updateRedeemedAmount();
    pos_total_row();
});

function isValidatRewardPoint() {
    var element = $('#rp_redeemed_modal');
    var points = element.val().trim();
    points = points == '' ? 0 : parseInt(points);

    var max_points = parseInt(element.data('max_points'));
    var is_valid = true;
    var msg = '';

    if (points == 0) {
        return {
            is_valid: is_valid,
            msg: msg
        }
    }

    var rp_name = $('input#rp_name').val();
    if (points > max_points) {
        is_valid = false;
        msg = __translate('max_rp_reached_error', {max_points: max_points, rp_name: rp_name});
    }

    var min_order_total_required = parseFloat(element.data('min_order_total'));

    var order_total = __read_number($('#final_total_input'));

    if (order_total < min_order_total_required) {
        is_valid = false;
        msg = __translate('min_order_total_error', {min_order: __currency_trans_from_en(min_order_total_required, true), rp_name: rp_name});
    }

    var output = {
        is_valid: is_valid,
        msg: msg,
    }

    return output;
}

function adjustComboQty(tr){
    if(tr.find('input.product_type').val() == 'combo'){
        var qty = __read_number(tr.find('input.pos_quantity'));
        var multiplier = __getUnitMultiplier(tr);

        tr.find('input.combo_product_qty').each(function(){
            $(this).val($(this).data('unit_quantity') * qty * multiplier);
        });
    }
}

$(document).on('change', '#types_of_service_id', function(){
    var types_of_service_id = $(this).val();
    var location_id = $('#location_id').val();

    if(types_of_service_id) {
        $.ajax({
            method: 'POST',
            url: '/sells/pos/get-types-of-service-details',
            data: { 
                types_of_service_id: types_of_service_id,
                location_id: location_id
            },
            dataType: 'json',
            success: function(result) {
                if(result.packing_charge != 0.0000) {
                    //reset form if price group is changed
                    var prev_price_group = $('#types_of_service_price_group').val();
                    if(result.price_group_id) {
                        $('#types_of_service_price_group').val(result.price_group_id);
                        $('#price_group_text').removeClass('hide');
                        $('#price_group_text span').text(result.price_group_name);
                    } else {
                        $('#types_of_service_price_group').val('');
                        $('#price_group_text').addClass('hide');
                        $('#price_group_text span').text('');
                    }
                    $('#types_of_service_id').val(types_of_service_id);
                    $('.types_of_service_modal').html(result.modal_html);
                    
                    if (prev_price_group != result.price_group_id) {
                        if ($('form#edit_pos_sell_form').length > 0) {
                            $('table#pos_table tbody').html('');
                            pos_total_row();
                        } else {
                            reset_pos_form();
                        }
                    } else {
                        pos_total_row();
                    }

                    $('.types_of_service_modal').modal('show');
                }
            },
        });
    } else {
        $('.types_of_service_modal').html('');
        $('#types_of_service_price_group').val('');
        $('#price_group_text').addClass('hide');
        $('#price_group_text span').text('');
        $('#packing_charge_text').text('');
        if ($('form#edit_pos_sell_form').length > 0) {
            $('table#pos_table tbody').html('');
            pos_total_row();
        } else {
            reset_pos_form();
        }
    }
});

$(document).on('change', 'input#packing_charge, #additional_expense_value_1, #additional_expense_value_2, \
        #additional_expense_value_3, #additional_expense_value_4', function() {
    pos_total_row();
});

$(document).on('click', '.service_modal_btn', function(e) {
    if ($('#types_of_service_id').val()) {
        $('.types_of_service_modal').modal('show');
    }
});

$(document).on('change', '.payment_types_dropdown', function(e) {
    var default_accounts = $('select#select_location_id').length ? 
                $('select#select_location_id')
                .find(':selected')
                .data('default_payment_accounts') : $('#location_id').data('default_payment_accounts');
    var payment_type = $(this).val();
    var payment_row = $(this).closest('.payment_row');
    if (payment_type && payment_type != 'advance') {
        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
            default_accounts[payment_type]['account'] : '';
        var row_index = payment_row.find('.payment_row_index').val();

        var account_dropdown = payment_row.find('select#account_' + row_index);
        if (account_dropdown.length && default_accounts) {
            account_dropdown.val(default_account);
            account_dropdown.change();
        }
    }

    //Validate max amount and disable account if advance 
    amount_element = payment_row.find('.payment-amount');
    account_dropdown = payment_row.find('.account-dropdown');
    if (payment_type == 'advance') {
        max_value = $('#advance_balance').val();
        msg = $('#advance_balance').data('error-msg');
        amount_element.rules('add', {
            'max-value': max_value,
            messages: {
                'max-value': msg,
            },
        });
        if (account_dropdown) {
            account_dropdown.prop('disabled', true);
            account_dropdown.closest('.form-group').addClass('hide');
        }
    } else {
        amount_element.rules("remove", "max-value");
        if (account_dropdown) {
            account_dropdown.prop('disabled', false); 
            account_dropdown.closest('.form-group').removeClass('hide');
        }    
    }
});

$(document).on('show.bs.modal', '#recent_transactions_modal', function () {
    get_recent_transactions('final', $('div#tab_final'));
});
$(document).on('shown.bs.tab', 'a[href="#tab_quotation"]', function () {
    get_recent_transactions('quotation', $('div#tab_quotation'));
});
$(document).on('shown.bs.tab', 'a[href="#tab_draft"]', function () {
    get_recent_transactions('draft', $('div#tab_draft'));
});

function disable_pos_form_actions(){
    if (!window.navigator.onLine) {
        return false;
    }

    $('div.pos-processing').show();
    $('#pos-save').attr('disabled', 'true');
    $('div.pos-form-actions').find('button').attr('disabled', 'true');
}

function enable_pos_form_actions(){
    $('div.pos-processing').hide();
    $('#pos-save').removeAttr('disabled');
    $('div.pos-form-actions').find('button').removeAttr('disabled');
}

$(document).on('change', '#recur_interval_type', function() {
    if ($(this).val() == 'months') {
        $('.subscription_repeat_on_div').removeClass('hide');
    } else {
        $('.subscription_repeat_on_div').addClass('hide');
    }
});

function validate_discount_field() {
    discount_element = $('#discount_amount_modal');
    discount_type_element = $('#discount_type_modal');

    if ($('#add_sell_form').length || $('#edit_sell_form').length) {
        discount_element = $('#discount_amount');
        discount_type_element = $('#discount_type');
    }
    var max_value = parseFloat(discount_element.data('max-discount'));
    if (discount_element.val() != '' && !isNaN(max_value)) {
        if (discount_type_element.val() == 'fixed') {
            var subtotal = get_subtotal();
            //get max discount amount
            max_value = __calculate_amount('percentage', max_value, subtotal)
        }

        discount_element.rules('add', {
            'max-value': max_value,
            messages: {
                'max-value': discount_element.data('max-discount-error_msg'),
            },
        });
    } else {
        discount_element.rules("remove", "max-value");      
    }
    discount_element.trigger('change');
}

$(document).on('change', '#discount_type_modal, #discount_type', function() {
    validate_discount_field();
});

function update_shipping_address(data) {
    if ($('#shipping_address_div').length) {
        var shipping_address = '';
        if (data.supplier_business_name) {
            shipping_address += data.supplier_business_name;
        }
        if (data.name) {
            shipping_address += ',<br>' + data.name;
        }
        if (data.text) {
            shipping_address += ',<br>' + data.text;
        }
        shipping_address += ',<br>' + data.shipping_address ;
        $('#shipping_address_div').html(shipping_address);
    }
    if ($('#billing_address_div').length) {
        var address = [];
        if (data.supplier_business_name) {
            address.push(data.supplier_business_name);
        }
        if (data.name) {
            address.push('<br>' + data.name);
        }
        if (data.text) {
            address.push('<br>' + data.text);
        }
        if (data.address_line_1) {
            address.push('<br>' + data.address_line_1);
        }
        if (data.address_line_2) {
            address.push('<br>' + data.address_line_2);
        }
        if (data.city) {
            address.push('<br>' + data.city);
        }
        if (data.state) {
            address.push(data.state);
        }
        if (data.country) {
            address.push(data.country);
        }
        if (data.zip_code) {
            address.push('<br>' + data.zip_code);
        }
        var billing_address = address.join(', ');
        $('#billing_address_div').html(billing_address);
    }

    if ($('#shipping_custom_field_1').length) {
        let shipping_custom_field_1 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_1 : '';
        $('#shipping_custom_field_1').val(shipping_custom_field_1);
    }

    if ($('#shipping_custom_field_2').length) {
        let shipping_custom_field_2 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_2 : '';
        $('#shipping_custom_field_2').val(shipping_custom_field_2);
    }

    if ($('#shipping_custom_field_3').length) {
        let shipping_custom_field_3 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_3 : '';
        $('#shipping_custom_field_3').val(shipping_custom_field_3);
    }

    if ($('#shipping_custom_field_4').length) {
        let shipping_custom_field_4 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_4 : '';
        $('#shipping_custom_field_4').val(shipping_custom_field_4);
    }

    if ($('#shipping_custom_field_5').length) {
        let shipping_custom_field_5 = data.shipping_custom_field_details != null ? data.shipping_custom_field_details.shipping_custom_field_5 : '';
        $('#shipping_custom_field_5').val(shipping_custom_field_5);
    }
    
    //update export fields
    if (data.is_export) {
        $('#is_export').prop('checked', true);
        $('div.export_div').show();
        if ($('#export_custom_field_1').length) {
            $('#export_custom_field_1').val(data.export_custom_field_1);
        }
        if ($('#export_custom_field_2').length) {
            $('#export_custom_field_2').val(data.export_custom_field_2);
        }
        if ($('#export_custom_field_3').length) {
            $('#export_custom_field_3').val(data.export_custom_field_3);
        }
        if ($('#export_custom_field_4').length) {
            $('#export_custom_field_4').val(data.export_custom_field_4);
        }
        if ($('#export_custom_field_5').length) {
            $('#export_custom_field_5').val(data.export_custom_field_5);
        }
        if ($('#export_custom_field_6').length) {
            $('#export_custom_field_6').val(data.export_custom_field_6);
        }
    } else {
        $('#export_custom_field_1, #export_custom_field_2, #export_custom_field_3, #export_custom_field_4, #export_custom_field_5, #export_custom_field_6').val('');
        $('#is_export').prop('checked', false);
        $('div.export_div').hide();
    }
    
    $('#shipping_address_modal').val(data.shipping_address);
    $('#shipping_address').val(data.shipping_address);
}

function get_sales_orders() {
    if ($('#sales_order_ids').length) {
        if ($('#sales_order_ids').hasClass('not_loaded')) {
            $('#sales_order_ids').removeClass('not_loaded');
            return false;
        }
        var customer_id = $('select#customer_id').val();
        var location_id = $('input#location_id').val();
        $.ajax({
            url: '/get-sales-orders/' + customer_id + '?location_id=' + location_id,
            dataType: 'json',
            success: function(data) {
                $('#sales_order_ids').select2('destroy').empty().select2({data: data});
                $('table#pos_table tbody').find('tr').each( function(){
                    if (typeof($(this).data('so_id')) !== 'undefined') {
                        $(this).remove();
                    }
                });
                pos_total_row();
            },
        });
    }
}

$("#sales_order_ids").on("select2:select", function (e) {
    var sales_order_id = e.params.data.id;
    var product_row = $('input#product_row_count').val();
    var location_id = $('input#location_id').val();
    $.ajax({
        method: 'GET',
        url: '/get-sales-order-lines',
        async: false,
        data: {
            product_row: product_row,
            sales_order_id: sales_order_id
        },
        dataType: 'json',
        success: function(result) {
            if (result.html) {
                var html = result.html;
                $(html).find('tr').each(function(){
                    $('table#pos_table tbody')
                    .append($(this))
                    .find('input.pos_quantity');
                    
                    var this_row = $('table#pos_table tbody')
                        .find('tr')
                        .last();
                    pos_each_row(this_row);

                    product_row = parseInt(product_row) + 1;

                    //For initial discount if present
                    var line_total = __read_number(this_row.find('input.pos_line_total'));
                    this_row.find('span.pos_line_total_text').text(line_total);

                    //Check if multipler is present then multiply it when a new row is added.
                    if(__getUnitMultiplier(this_row) > 1){
                        this_row.find('select.sub_unit').trigger('change');
                    }

                    round_row_to_iraqi_dinnar(this_row);
                    __currency_convert_recursively(this_row);
                });

                set_so_values(result.sales_order);

                //increment row count
                $('input#product_row_count').val(product_row);
                
                pos_total_row();
            
            } else {
                toastr.error(result.msg);
                $('input#search_product')
                    .focus()
                    .select();
            }
        },
    });
});

function set_so_values(so) {
    $('textarea[name="sale_note"]').val(so.additional_notes);
    if ($('#shipping_details').is(':visible')) {
        $('#shipping_details').val(so.shipping_details);
    }
    $('#shipping_address').val(so.shipping_address);
    $('#delivered_to').val(so.delivered_to);
    $('#shipping_charges').val( __number_f(so.shipping_charges));
    $('#shipping_status').val(so.shipping_status);
    if ($('#shipping_custom_field_1').length) {
        $('#shipping_custom_field_1').val(so.shipping_custom_field_1);
    }
    if ($('#shipping_custom_field_2').length) {
        $('#shipping_custom_field_2').val(so.shipping_custom_field_2);
    }
    if ($('#shipping_custom_field_3').length) {
        $('#shipping_custom_field_3').val(so.shipping_custom_field_3);
    }
    if ($('#shipping_custom_field_4').length) {
        $('#shipping_custom_field_4').val(so.shipping_custom_field_4);
    }
    if ($('#shipping_custom_field_5').length) {
        $('#shipping_custom_field_5').val(so.shipping_custom_field_5);
    }
}

$("#sales_order_ids").on("select2:unselect", function (e) {
    var sales_order_id = e.params.data.id;
    $('table#pos_table tbody').find('tr').each( function(){
        if (typeof($(this).data('so_id')) !== 'undefined' 
            && $(this).data('so_id') == sales_order_id) {
            $(this).remove();
        pos_total_row();
        }
    });
});

$(document).on('click', '#add_expense', function(){
    $.ajax({
        url: '/expenses/create',
        data: { 
            location_id: $('#select_location_id').val()
        },
        dataType: 'html',
        success: function(result) {
            $('#expense_modal').html(result);
            $('#expense_modal').modal('show');
        },
    });
});

//Open Security Pin Modal
$(document).on('click', '#open_security_pin_modal', function(){
    $("#frmSecurityPinVerify")[0].reset();
    $('#security-pin-modal').modal('show');
});


$(document).on('shown.bs.modal', '#expense_modal', function(){
    $('#expense_transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
    $('#expense_modal .paid_on').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
    $(this).find('.select2').select2();
    $('#add_expense_modal_form').validate();
});

$(document).on('hidden.bs.modal', '#expense_modal', function(){
    $(this).html('');
});

$(document).on('submit', 'form#add_expense_modal_form', function(e) {
    e.preventDefault();
    var data = $(this).serialize();

    $.ajax({
        method: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        success: function(result) {
            if (result.success == true) {
                $('#expense_modal').modal('hide');
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
});

function get_contact_due(id) {
    $.ajax({
        method: 'get',
        url: /get-contact-due/ + id,
        dataType: 'text',
        success: function(result) {
            if (result != '') {
                $('.contact_due_text').find('span').text(result);
                $('.contact_due_text').removeClass('hide');
            } else {
                $('.contact_due_text').find('span').text('');
                $('.contact_due_text').addClass('hide');
            }
        },
    });
}


function submitQuickContactForm(form) {
    var data = $(form).serialize();
    $.ajax({
        method: 'POST',
        url: $(form).attr('action'),
        dataType: 'json',
        data: data,
        beforeSend: function(xhr) {
            __disable_submit_button($(form).find('button[type="submit"]'));
        },
        success: function(result) {
            if (result.success == true) {
                var name = result.data.name;

                if (result.data.supplier_business_name) {
                    name += result.data.supplier_business_name;
                }
                
                $('select#customer_id').append(
                    $('<option>', { value: result.data.id, text: name })
                );
                $('select#customer_id')
                    .val(result.data.id)
                    .trigger('change');
                $('div.contact_modal').modal('hide');
                update_shipping_address(result.data)
                toastr.success(result.msg);
            } else {
                toastr.error(result.msg);
            }
        },
    });
}

$(document).on('click', '#send_for_sell_return', function(e) {
    var invoice_no = $('#send_for_sell_return_invoice_no').val();

    if (invoice_no) {
        $.ajax({
            method: 'get',
            url: /validate-invoice-to-return/ + encodeURI(invoice_no),
            dataType: 'json',
            success: function(result) {
                if (result.success == true) {
                    window.location = result.redirect_url ;
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    }
})

$(document).on('ifChanged', 'input[name="search_fields[]"]', function(event) {
    var search_fields = [];
    $('input[name="search_fields[]"]:checked').each(function() {
       search_fields.push($(this).val());
    });

    localStorage.setItem('pos_search_fields', search_fields);
});

function set_search_fields() {
    if ($('input[name="search_fields[]"]').length == 0) {
        return false;
    }

    var pos_search_fields = localStorage.getItem('pos_search_fields');

    if (pos_search_fields === null) {
        pos_search_fields = ['name', 'sku', 'lot'];
    }

    $('input[name="search_fields[]"]').each(function() {
        if (pos_search_fields.indexOf($(this).val()) >= 0) {
            $(this).iCheck('check');
        } else {
            $(this).iCheck('uncheck');
        }
    });
}

$(document).on('click', '#show_service_staff_availability', function(){
    loadServiceStaffAvailability();
})
$(document).on('click', '#refresh_service_staff_availability_status', function(){
    loadServiceStaffAvailability(false);
})
$(document).on('click', 'button.pause_resume_timer', function(e){
    $('.view_modal').find('.overlay').removeClass('hide');
    $.ajax({
        method: 'get',
        url: $(this).attr('data-href'),
        dataType: 'json',
        success: function(result) {
            loadServiceStaffAvailability(false);
        },
    });
})

$(document).on('click', '.mark_as_available', function(e){
    e.preventDefault()
    $('.view_modal').find('.overlay').removeClass('hide');
    $.ajax({
        method: 'get',
        url: $(this).attr('href'),
        dataType: 'json',
        success: function(result) {
            loadServiceStaffAvailability(false);
        },
    });
})
var service_staff_availability_interval = null;

function loadServiceStaffAvailability(show = true) {
    var location_id = $('[name="location_id"]').val();
    $.ajax({
        method: 'get',
        url: $('#show_service_staff_availability').attr('data-href'),
        dataType: 'html',
        data: {location_id: location_id},
        success: function(result) {
            $('.view_modal').html(result);
            if (show) {
                $('.view_modal').modal('show')

                //auto refresh service staff availabilty if modal is open
                service_staff_availability_interval = setInterval(function () {
                    loadServiceStaffAvailability(false);
                }, 60000);
            }
        },
    });
}

function GetCardType(number)
{
    // visa
    var re = new RegExp("^4");
    if (number.match(re) != null)
        return "visa";

    // Mastercard 
    // Updated for Mastercard 2017 BINs expansion
     if (/^(5[1-5][0-9]{14}|2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12}))$/.test(number)) 
        return "mastercard";

    // AMEX
    re = new RegExp("^3[47]");
    if (number.match(re) != null)
        return "amex";

    // Discover
    re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
    if (number.match(re) != null)
        return "discover";

    // Diners
    re = new RegExp("^36");
    if (number.match(re) != null)
        return "diners";

    // Diners - Carte Blanche
    re = new RegExp("^30[0-5]");
    if (number.match(re) != null)
        return "diners-carte-blanche";

    // JCB
    re = new RegExp("^35(2[89]|[3-8][0-9])");
    if (number.match(re) != null)
        return "jcb";

    // Visa Electron
    re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
    if (number.match(re) != null)
        return "visa-electron";

    return "";
}

function cardChargeSplitPayment()
{
    var charge = 0;
    var symbol = $('#__symbol').val();
    var card_charge_percent = parseFloat($('#card_charge_percent_hidden').val()) / 100;
    $('.payment_types_dropdown').each(function(){
        var val = $(this).val();
        if(val === 'card') {
           var parent = $(this).parents('.payment_row');
           var amount = parent.find('.payment-amount').val();
           charge += amount * card_charge_percent;
           //console.log('test: ', amount * card_charge_percent);
        }
    });
    $('.additional_card_charge_split_payment').text(symbol +' '+ charge.toFixed(2))
}

$(document).on('change', '.payment_types_dropdown', function(){
    cardChargeSplitPayment();
});

$(document).on('keyup', '.payment-amount', function(){
    cardChargeSplitPayment();
});

$(document).on('hidden.bs.modal', '.view_modal', function(){
    if (service_staff_availability_interval !== null) {
        clearInterval(service_staff_availability_interval);
    }
    service_staff_availability_interval = null;
});

$(document).on('click', '.clock_in_btn:not(:disabled), .clock_out_btn:not(:disabled)', function() {
    var type = $(this).data('type');
    if (type == 'clock_in') {
        $('#clock_in_clock_out_modal').find('#clock_in_text').removeClass('hide');
        $('#clock_in_clock_out_modal').find('#clock_out_text').addClass('hide');
        $('#clock_in_clock_out_modal').find('.clock_in_note').removeClass('hide');
        $('#clock_in_clock_out_modal').find('.clock_out_note').addClass('hide');
    } else if (type == 'clock_out') {
        $('#clock_in_clock_out_modal').find('#clock_in_text').addClass('hide');
        $('#clock_in_clock_out_modal').find('#clock_out_text').removeClass('hide');
        $('#clock_in_clock_out_modal').find('.clock_in_note').addClass('hide');
        $('#clock_in_clock_out_modal').find('.clock_out_note').removeClass('hide');
    }
    $('#clock_in_clock_out_modal').find('input#type').val(type);

    $('#clock_in_clock_out_modal').modal('show');

});


// $(document).on('submit', 'form#clock_in_clock_out_form', function(e) {
//     e.preventDefault();
//     $(this).find('button[type="submit"]').attr('disabled', true);
//     var data = $(this).serialize();
//     $.ajax({
//         method: $(this).attr('method'),
//         url: $(this).attr('action'),
//         dataType: 'json',
//         data: data,
//         success: function(result) {
//             if (result.success == true) {
//                 $('div#clock_in_clock_out_modal').modal('hide');

//                 var shift_details = document.createElement("div");
//                 if (result.current_shift) {
//                     shift_details.innerHTML = result.current_shift;
//                 }

//                 // swal({
//                 //     title: result.msg,
//                 //     content: shift_details,
//                 //     icon: 'success'
//                 // });
//                 toastr.success(result.msg);

//                 if (typeof attendance_table !== 'undefined') {
//                     attendance_table.ajax.reload();
//                 }
//                 if (result.type == 'clock_in') {
//                     $('.clock_in_btn').attr('disabled', 'disabled');
//                     $('.clock_in_btn').addClass('disabled');
//                     $('.clock_out_btn').attr('disabled',false);
//                     $('.clock_out_btn').removeClass('disabled');
//                 } else if(result.type == 'clock_out') {
//                     $('.clock_out_btn').attr('disabled', 'disabled');
//                     $('.clock_out_btn').addClass('disabled');
//                     $('.clock_in_btn').attr('disabled',false);
//                     $('.clock_in_btn').removeClass('disabled');
//                 }
//             } else {
//                 var shift_details = document.createElement("p");
//                 if (result.shift_details) {
//                     shift_details.innerHTML = result.shift_details;
//                 }

//                 swal({
//                     title: result.msg,
//                     content: shift_details,
//                     icon: 'error'
//                 })
//                 //toastr.error(result.msg);

//             }
//             $('#clock_in_clock_out_form')[0].reset();
//             $('#clock_in_clock_out_form').find('button[type="submit"]').removeAttr('disabled');
//         },
//     });
// });



//Clock in Clockout ReWrite by P
$(document).on('submit', 'form#clock_in_clock_out_form', function(e) {
    
    e.preventDefault();
    $(this).find('button[type="submit"]').attr('disabled', true);
    var data = $(this).serialize();
    var cico_action = $('#cico_action').val();
    $.ajax({
        method: $(this).attr('method'),
        url: $(this).attr('action'),
        dataType: 'json',
        data: data,
        success: function(result) {
            
            if (result.success == true){
                if(cico_action == ""){
                    $('.cl-co-keyboard, .mf-input-box').hide();
                    $('.cl-co-type-choose').show();
                    
                    $('.ci-co-user').text(result.data.first_name+' '+result.data.last_name);
                    $('.ci-co-user-role').text(result.data.role);
                    $('.ci-co-business').text(result.data.business.name);
                }else{
                    $('#clock_in_clock_out_modal').modal('hide');
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = "<div>"+result.current_shift+"</div>";
                    swal({ 
                        icon: 'success', 
                        html:true, 
                        title:result.msg, 
                        content: wrapper, 
                    });
                }
            }else{
                if(cico_action == ""){
                    toastr.error(result.msg);
                }else{
                    swal({ 
                        icon: 'error', 
                        html:true, 
                        title:result.msg, 
                    });
                }
            }

        },
    });
});

//CICO btn action

$('body').on('click', 'button.cl-co-btn-action', function() {
    var action = $(this).data('action');
    $('#cico_action').val(action);
    $('#clock_in_clock_out_form').submit();
});

//Clock in Clock out on single button
$('body').on('click', 'button.clockinout', function() {
    $('.cl-co-keyboard, .mf-input-box').show();
    $('.cl-co-type-choose').hide();
    $('#user_pin').val('');
    $('#cico_action').val('');
    $('#clock_in_clock_out_modal').modal('show');
});

$('body').on('click', '.cl-key-input', function() {
    var input = $(this).data('value');
    var current_val = $('#user_pin').val();
    if(input == "CANCEL"){
        $('#user_pin').val('');
    }else if(input == "ENTER"){
        //Submit to server
        var total_length = current_val.length;
        if(current_val == ""){
            toastr.error("Please enter the security pin.");
        }

    }else{
        current_val += input
        $('#user_pin').val(current_val);
    }
});


// Use function for allow only decimal input
$(".allow-decimal-number-only").on("keypress", function (evt) {
    var $txtBox = $(this);
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;
    else {
        var len = $txtBox.val().length;
        var index = $txtBox.val().indexOf('.');
        if (index > 0 && charCode == 46) {
          return false;
        }
        if (index > 0) {
            var charAfterdot = (len + 1) - index;
            if (charAfterdot > 3) {
                return false;
            }
        }
    }
    return $txtBox; //for chaining
});

//Use function for open tips amount input modal in pos screen
$('.tip_service_modal_btn').click(function(){
    $('#tips-modal').modal('show'); 
});

//Function use for apply the tips on order
$('.btn-submit-tips').click(function(){
    pos_total_row();
    $('#tips-modal').modal('hide'); 
});


setFakeWebsocket();
$(document).on('click', 'body, .pos_remove_row, .quantity-up, .quantity-down, .add_modifier', function(){
    setFakeWebsocket();
});

function setFakeWebsocket() {
    var pos_table = $('#pos_table tbody').html();
    pos_table = pos_table.replace(/modal/g, '');
    localStorage.setItem('pos_table', pos_table);

    var location = $('#select_location_id option:selected').text();
    localStorage.setItem('pos_location', location);

    var total_payable = $('#total_payable').text();
    localStorage.setItem('total_payable', total_payable);

    var total_discount = $('#total_discount').text();
    localStorage.setItem('total_discount', total_discount);

    var order_tax = $('#order_tax').text();
    localStorage.setItem('order_tax', order_tax);

    var gratuity_charges = $('.gratuity_charges').text();
    localStorage.setItem('gratuity_charges', gratuity_charges);

    var tips_text = $('#tips_text').text();
    localStorage.setItem('tips_text', tips_text);

    var gratuity_charges_label = $('.gratuity_wrapper b').text();
    localStorage.setItem('gratuity_charges_label', gratuity_charges_label);

    var packing_charge_text = $('#packing_charge_text').text();
    localStorage.setItem('packing_charge_text', packing_charge_text);

    var amount_change = $('.in .change_return_span').text();
    localStorage.setItem('amount_change', amount_change !='' ? amount_change : 0);

}

$('form#add_pos_sell_form, form#edit_pos_sell_form').submit(function() {
    setTimeout(function(){
        setFakeWebsocket();
    },1000)
});

//Function use for new category through get subcategory or products.
$('body').on('click', '.category_box', function(){
    var parent_id = $(this).data('parent-id');
    var category_id = $(this).data('id');
    var total_subcatgory = $(this).data('subcategory-count');
    var level = $(this).data('level');
    var is_show_direct_product = $(this).data('direct-product');
    filterProductByCategory(parent_id, category_id, total_subcatgory, level, is_show_direct_product);
});

//Common function for filter pos categories
function filterProductByCategory(parent_id, category_id, total_subcatgory, level, is_show_direct_product){
    var location_id = $('input#location_id').val();
    var is_enabled_stock = null;
    if ($("#is_enabled_stock").length) {
        is_enabled_stock = $("#is_enabled_stock").val();
    }
    var device_model_id = null;
    if ($("#repair_model_id").length) {
        device_model_id = $("#repair_model_id").val();
    }
    
    var url = '/sells/pos/get-product-by-filter';
    $('#suggestion_page_loader').fadeIn(700);
    $.ajax({
        method: 'GET',
        url: url,
        data: {
            category_id: category_id,
            location_id: location_id,
            is_enabled_stock: is_enabled_stock,
            is_show_direct_product:is_show_direct_product
        },
        dataType: 'html',
        success: function(result) {
           
            $('div#product_list_body').html('');
            $('div#product_list_body').append(result);
            $('div#category-list-wrapper').hide();
            $('div#subcategory-list-wrapper').hide();
            
            if(level == ""){
                $('div#subcategory-list-wrapper').hide();
            }
            $('button.back-event').removeClass('d-none');
            
            if($('div#product_list_body .subcategory-wrapper').length != 0){
                var subcategory_html = $('div#product_list_body .subcategory-wrapper').html();
                $('div#subcategory-list-wrapper').html('');
                $('div#subcategory-list-wrapper').append(subcategory_html);
                $('div#product_list_body').find('.subcategory-wrapper').remove();
                $('div#subcategory-list-wrapper').show();
            }

            if(parent_id != ""){
                $('div#subcategory-list-wrapper').html('');
                $('button.back-event').attr('data-parent-id', parent_id);
            }

            /*
            if(is_show_direct_product == 1){
               level = 2;
               $('button.back-event').attr('data-parent-id', category_id);
            }
            */

            $('button.back-event').attr('data-category', category_id);
            $('button.back-event').attr('data-subcategory-count', total_subcatgory);
            $('button.back-event').attr('data-level', level);
            $('#suggestion_page_loader').fadeOut(700);

        },
    });
}

//POS Category Back Button Event
$('body').on('click', '.back-btn-wrapper button', function(){
    $('div#product_list_body').html('');
    $('div#subcategory-list-wrapper').hide();
    var level = $('.back-btn-wrapper button').attr('data-level');
    var total_subcatgory = $('.back-btn-wrapper button').attr('data-subcategory-count');
    if(level == "1"){
        $('button.back-event').addClass('d-none');
        $('div#category-list-wrapper').fadeIn(700);
    }else if(level == "2"){
        var parent_category_id = $('.back-btn-wrapper button').attr('data-parent-id');
        if(parent_category_id == undefined){
            $('div#product_list_body').html('');
            $('div#subcategory-list-wrapper').hide();
            $('button.back-event').addClass('d-none');
            $('div#category-list-wrapper').fadeIn(700);
            $('button.back-event').attr('data-level', 1);
        }else{
            filterProductByCategory("", parent_category_id, total_subcatgory, level);
            $('div#subcategory-list-wrapper').show();
            $('button.back-event').removeAttr('data-parent-id');
        }        
    }
});


//Split Payment Update the Payment Method
$('body').on('change', '.payment_types_dropdown', function() {
	var selected_val = $(this).val();
	$(this).find('option').removeAttr('selected');
	$(this).find('option[value="'+selected_val+'"]').attr('selected', 'selected');
});

//Check location is open
function check_location_is_open(){
    
    var location_id = $('#location_id').val();
    if(location_id != ""){
        $.ajax({
            method: 'POST',
            url: '/sells/pos/check-location-is-open/' + location_id,
            success: function(result) {

                //Selected Option Restore
                var active_location_id = result.data.active_location_id;
                $('#select_location_id').find('option').removeAttr('selected');
                $('#select_location_id').find('option[value='+active_location_id+']').attr('selected', 'selected');
                reset_pos_form();
                set_payment_type_dropdown();

                //Product sidebar reset
                $('input#suggestion_page').val(1);
                $('div#category-list-wrapper').show();
                $('div#subcategory-list-wrapper').hide();
                $('.back-event').addClass('d-none');
                $('div#product_list_body').html('');
                get_featured_products();
                

                if(result.status == true){
                    //Open confirm popup
                    swal({
                        title: result.message,
                        text: "",
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true,
                        buttons: ['No', 'Yes']
                    }).then((result) => {
                        if (result) {
                            $('#close_register').trigger('click');
                        }
                    })

                }else{
                    if(result.data.is_popup_open == 1){
                        swal({
                            title: result.message,
                            text: "",
                            icon: 'error'
                        });
                    }
                }
            },
        });
    }
}


//Check table occupied
function check_table_is_occupied(){
    $.ajax({
    method: 'GET',
    url: '/bookings/get-occupied-table-chairs?location_id=' + $('#select_location_id').val(),
        success: function(result){
            $('select[name="res_table_id"] option').each(function(){
                var id = $(this).attr('value');
                if(result.includes(id)) {
                    $(this).attr('disabled', 'disabled');
                }
            });
        }
    });
}


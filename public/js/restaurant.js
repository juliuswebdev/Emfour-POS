$(document).ready(function() {
    //If location is set then show tables.
    getLocationTables($('input#location_id').val());

    $('select#select_location_id').change(function() {
        var location_id = $(this).val();
        getLocationTables(location_id);
    });

    $(document).on('click', 'button.add_modifier', function() {
        var checkbox = $(this)
            .closest('div.modal-content')
            .find('input:checked');
        selected = [];
        checkbox.each(function() {
            selected.push($(this).val());
        });
        var index = $(this)
            .closest('div.modal-content')
            .find('input.index')
            .val();

        var quantity = __read_number($(this).closest('tr').find('input.pos_quantity'));
        add_selected_modifiers(selected, index, quantity);
    });
    $(document).on('click', '#refresh_orders', function() {
        refresh_orders();
    });

    //Auto refresh orders
    if ($('#refresh_orders').length > 0) {
        var refresh_interval = parseInt($('#__orders_refresh_interval').val()) * 1000;

        setInterval(function(){ 
            refresh_orders();
        }, refresh_interval);
    }
});

function getLocationTables(location_id, $type=null) {
    
    var transaction_id = $('span#restaurant_module_span').data('transaction_id');

    if (location_id != '') {
        $.ajax({
            method: 'GET',
            url: '/modules/data/get-pos-details',
            data: { location_id: location_id, transaction_id: transaction_id },
            dataType: 'html',
            success: function(result) {
                $('span#restaurant_module_span').html(result);

                //Inject the service staff box in after the walk in
                if($('#res_waiter_id').length != 0){
                    var box_service_staff = $('.box-of-service-staff').html();
                    $('.inject-service-staff').html(box_service_staff);
                    $('#restaurant_module_span').find('.box-of-service-staff').remove();
                }
                
                //Inject the table list in after the type of services
                if($('.table-mapping-dropdown').length != 0){
                    var box_table_mapping = $('.table-mapping-dropdown').html();
                    $('.inject-tables').html(box_table_mapping);
                    $('#restaurant_module_span').remove();
                    $('.inject-tables').attr('id', 'restaurant_module_span');
                }

                //REPAIR MODULE:set technician from repair module
                if ($("#repair_technician").length) {
                    $("select#res_waiter_id").val($("#repair_technician").val()).change();
                }

                //Sale Return Disable Dropdown.
                var length_of_sale_return_btn = $('.wrapper-of-sale-return').length;
                if(length_of_sale_return_btn != 0){
                    $('span#restaurant_module_span').find('select').attr("disabled", "disabled")
                }

                if($type == 'table_mapping') {
                    $('.table-mapping-dropdown').addClass('active');
                    $.ajax({
						method: 'GET',
						url: '/bookings/get-occupied-table-chairs?location_id=' + $('#select_location_id').val(),
							success: function(result){
					
								$('select[name="res_table_id"] option').each(function(){
                                    var id = $(this).attr('value');
                                    var selected = $(this).attr('selected');
                                    if(result.includes(id) && !selected) {
                                        $(this).attr('disabled', 'disabled');
                                    }
								});
							}
					});
                }

                //Check table occupied
                check_table_is_occupied();

            },
        });
    }
}

function add_selected_modifiers(selected, index, quantity = 1) {
    if (selected.length > 0) {
        $.ajax({
            method: 'GET',
            url: $('button.add_modifier').data('url'),
            data: { selected: selected, index: index, quantity: quantity },
            dataType: 'html',
            success: function(result) {
                if (result != '') {
                    $('table#pos_table tbody')
                        .find('tr')
                        .each(function() {
                            if ($(this).data('row_index') == index) {
                                $(this)
                                    .find('td:first .selected_modifiers')
                                    .html(result);
                                return false;
                            }
                        });

                    //Update total price.
                    pos_total_row();
                }
            },
        });
    } else {
        $('table#pos_table tbody')
            .find('tr')
            .each(function() {
                if ($(this).data('row_index') == index) {
                    $(this)
                        .find('td:first .selected_modifiers')
                        .html('');
                    return false;
                }
            });

        //Update total price.
        pos_total_row();
    }
}

function refresh_orders() {
    $('.overlay').removeClass('hide');
    var orders_for = $('input#orders_for').val();
    var service_staff_id = '';
    if ($('select#service_staff_id').val()) {
        service_staff_id = $('select#service_staff_id').val();
    } else {
        const urlParams = new URL(window.location.href).searchParams;
        service_staff_id = urlParams.get('service_staff');
    }
    $.ajax({
        method: 'POST',
        url: '/modules/refresh-orders-list',
        data: { orders_for: orders_for, service_staff_id: service_staff_id },
        dataType: 'html',
        success: function(data) {
            $('#orders_div').html(data);
            //$('.overlay').addClass('hide');
        },
    });

    $.ajax({
        method: 'POST',
        url: '/modules/refresh-line-orders-list',
        data: { orders_for: orders_for, service_staff_id: service_staff_id },
        dataType: 'html',
        success: function(data) {
            $('#line_orders_div').html(data);
            //$('.overlay').addClass('hide');
        },
    });
}

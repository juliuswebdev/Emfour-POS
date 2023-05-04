$(document).ready(function() {

    $('form#stock_adjustment_form').validate();
    inventory_count_table = $('#inventory_count_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/inventory-count',
            data: function (d) {
                d.status = $('#status').val()
            }
        },
        columnDefs: [
            {
                targets: 0,
               // orderable: false,
                searchable: false,
            },
        ],
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date', name: 'date' },
            { data: 'count_reference', name: 'count_reference' },
            { data: 'count_description', name: 'count_description' },
            { data: 'location', name: 'location' },
            { data: 'categories', name: 'categories' },
            { data: 'sub_categories', name: 'sub_categories' },
            { data: 'brands', name: 'brands' },
            { data: 'type', name: 'type' },
            { data: 'status', name: 'status' }
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#inventory_count_table'));
        },
    });

    $('#status').change(function(){
        inventory_count_table.draw();
    });

    $('#count_type').change(function() {
        const count_type = $(this).val();
        if(count_type === 'partial') {
            $('#partial_area').show();
        } else {
            $('#partial_area').hide();
            // $('#categories').select2('val', []);
            // $('#brands').select2('val', []);
            // $('#sub_categories').select2('val', []);
            $('.select2-multiple').val([]).change();
        }
    });

    $('.select2-multiple').select2();

    $(document).on('change', '#categories', function() {
        get_sub_categories($(this).find(':selected').data('cat_id'));
    });
   
    function get_sub_categories(cat) {
        $.ajax({
            method: 'POST',
            url: '/products/get_sub_categories',
            dataType: 'html',
            data: { cat_id: cat },
            success: function(result) {
                if (result) {
                    $('#sub_categories').html(result.replace('<option value="">None</option>', ''));
                }
            },
        });
    }

    $(document).on('click', '.download_inventory_count', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var count_reference = $(this).data('count_reference');
        $.ajax({
            type: "GET",
            url: url,
            xhrFields: {
                responseType: 'blob',
            },
            success: function (data) {
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(data);
                link.download = count_reference + `.csv`;
                link.click();
            }
        });
    });

    $(document).on('click', '.action_modal_form', function(e) {
        var action = $(this).data('action');
        var target = $(this).data('target');
        $(target+' form').attr('action', action);
    });


    $(document).on('click', '.final_report', function(e) {
        var url = $(this).data('action');
        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                $('#final_report.modal .modal-body').html(data);
            }
        });
    });

    $("#search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
    
        $("#myTable tbody tr").each(function(index) {
                $row = $(this);
                var id = $row.find("td:first").text().toLowerCase();
                if (id.indexOf(value) != 0) {
                    $(this).hide();
                }
                else {
                    $(this).show();
                }
        });
    });

    var count_detail_id_arr = [];
    $('.count-detail-remove').click(function() {
        if(confirm('Are you sure you want to remove this?')) {
            var count_detail_id = $(this).data('count_id');
            count_detail_id_arr.push(count_detail_id);
            $('input[name="count_detail_id_deleted"]').val(count_detail_id_arr.join());
            $(this).parents('tr').remove();
            get_total_qty();
        }
    });

    $(document).on('click', '.freeze_confirm', function(e) {
        var count_reference = $(this).data('count_reference');
        $('#freeze_confirm .modal-body p.italic').prepend('<p><strong>Reference Count: </strong>'+count_reference+'</p>')
    });

    get_total_qty();
    $('input.qty').keyup(function(){
        get_total_qty();
    });

    function get_total_qty() {
        var total_qty = 0;
        $('input.qty').each(function(){
            var value = $(this).val();
            if(value != '') {
                total_qty = total_qty +  parseInt(value);
            }
        });
        $('#total-qty').text(total_qty);
    }


});


$(document).on('shown.bs.modal', '.view_modal', function() {
    __currency_convert_recursively($('.view_modal'));
});

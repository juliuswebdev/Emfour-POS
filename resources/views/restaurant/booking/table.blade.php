@php
    //$url = file_get_contents($business_details->wpc_reservation_site_link.'wp-json/wpc/table_mapping');
    $url = file_get_contents('https://restaurant.emfoursolutions.com/wp-json/wpc/table_mapping');
    $data = json_decode($url, true);
    $map = $data['content']['common_mapping'];
@endphp

    <div class="modal-dialog modal-lg" role="document" style="width: {{ $map['canvasWidth'] + 30 }}px;">
        <div class="modal-content">
            <div class="modal-body">
                <div id="table-layout-container">
                    <h4>@lang('restaurant.table_mapping')</h4>
                    <div id="table-layout-map" style="
                            position: relative;
                            width: {{ $map['canvasWidth'] }}px;
                            height: {{ $map['canvasHeight'] }}px;
                            background: url({{ $map['bg_image'] }})
                        ">
                        @foreach($map['mapping'] as $item)
                            @php 
                            $off = 0;
                            if($item['type'] == 'table_circle') {
                                $off = 20;
                            }
                            @endphp
                            <div class="{{$item['type']}}" id="{{$item['id']}}" style="
                                position: absolute;
                                top: {{$item['positions']['top'] - $off}}px;
                                left: {{$item['positions']['left'] - $off}}px;
                                width: {{$item['positions']['width']}}px;
                                height: {{$item['positions']['height']}}px;
                                background-color: {{$item['color']}};
                                transform: scale({{ $item['scaleX'] }}, {{ $item['scaleY'] }});

                            ">
                                <span>{{$item['name']}}{{$item['number']}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    #table-layout-container {
        background-color: #fff;
    }
    #table-layout-container h4 {
        padding: 20px;
        margin: 0;
    }
    #table-layout-map {
        border: 1px solid #666;
        background-color: #eee;
        position: relative;   
    }
    #table-layout-map .table_circle {
        border-radius: 100%;
    }
    #table-layout-map div span {
        position: absolute;
        display: block;
        text-align: center;
        top: 50%;
        width: 100%;
        transform: translateY(-50%);
        color: #fff;
    }
</style>
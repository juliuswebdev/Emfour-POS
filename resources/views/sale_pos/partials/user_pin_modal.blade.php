<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="pin_server_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">@lang('repair::lang.security')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::open(['action' => '\App\Http\Controllers\Restaurant\OrderController@userCheckPin', 'id' => 'check_user_pin', 'method' => 'post']) !!}
                        <input id="user_id" name="user_id" type="hidden">
                        {!! Form::label('pin', __('business.digits_pin') . ':') !!}
                        <input type="password" name="pin" id="pin" class="form-control" placeholder="{{  __('business.digits_pin') }}">
                        <br>
                        <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
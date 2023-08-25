<div class="modal fade" tabindex="-1" role="dialog" id="confirmSuspendModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang('lang_v1.suspend_sale')</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
				        <div class="form-group">
				            {!! Form::label('additional_notes', __('lang_v1.suspend_note') . ':' ) !!}
				            {!! Form::textarea('additional_notes', !empty($transaction->additional_notes) ? $transaction->additional_notes : null, ['class' => 'form-control','rows' => '4']); !!}
				            {!! Form::hidden('is_suspend', 0, ['id' => 'is_suspend']); !!}
							{!! Form::hidden('send_to_kitchen', 0, ['id' => 'send_to_kitchen']); !!}
							{!! Form::hidden('is_preauthorize', 0, ['id' => 'is_preauthorize']); !!}
				        </div>
				    </div>
				</div>
			</div>
			<div class="modal-footer d-flex-wrapper accept-order-action">
				<div>
					<button type="button" class="btn btn-primary" id="pos-preauthorize">@lang('messages.preauthorize')</button>
				</div>
				<div>
					<button type="button" class="btn btn-primary" id="pos-suspend">@lang('messages.submit')</button>
				</div>
				<div>
					<button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
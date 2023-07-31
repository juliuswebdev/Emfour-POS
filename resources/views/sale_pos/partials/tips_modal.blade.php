<div class="modal fade in" tabindex="-1" role="dialog" id="tips-modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title">@lang('lang_v1.tips')</h4>
			</div>
			<div class="modal-body">
				<!-- /.box-header -->
				<div class="box-body">
						<label>@lang('lang_v1.enter_tips_amount')</label>
						<div class="row">
							<div class="col-md-12">
								<input type="text" placeholder="@lang('lang_v1.enter_tips_amount')" id="tips_amount" required="required" name="tips_amount" 
                                value="@if(!empty($edit)) {{ sprintf('%0.2f', $transaction->tips_amount) }} @endif"
                                class="allow-decimal-number-only form-control">
							</div>
                        </div>
                </div>
				<!-- /.box-body --> 
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-submit-tips">@lang('lang_v1.update')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang_v1.cancel')</button>
            </div>
		</div>
	</div>
</div>

<div class="modal fade in" tabindex="-1" role="dialog" id="tips-v2-modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				{{-- <h4 class="modal-title">@lang('lang_v1.tips')</h4> --}}
			</div>
			<div class="modal-body">
				<!-- /.box-header -->
				<div class="box-body" id="tips_v2_form">
                    <h2 id="tips_v2_total_amount"></h2>
                    @include('sale_pos.partials.tips_v2_form')
                </div>
				<!-- /.box-body --> 
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary tips_v2_btn_update">@lang('lang_v1.update')</button>
                <button type="button" class="btn btn-default tips_v2_btn_cancel" data-dismiss="modal">@lang('lang_v1.cancel')</button>
            </div>
		</div>
	</div>
</div>

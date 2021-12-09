<div class="modal fade" id="modalShow" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">{{__('admin.submit')}}</h4>
            </div>
            <div class="modal-body modal-m-body">
                <div class="form-confirm" id="entry_form_modal">
                    <div class="form-group row">
                        <label for="name" class="col-md-4 control-label">{{__('admin.header.category')}}</label>
                        <div class="col-md-8  modal-category"></div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-md-4 control-label">{{__('admin.learning.question_text')}}</label>
                        <div class="col-md-8  modal-question"></div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-md-4 control-label">{{__('admin.learning.answer_text')}}</label>
                        <div class="col-md-8  modal-answer"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('admin.cancel')}}</button>
            </div>
        </div>
    </div>
</div>
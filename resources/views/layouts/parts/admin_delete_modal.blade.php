<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['method'=>'DELETE','id'=>'delete_form']) }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">{{__('admin.delete_confirm')}}</h4>
            </div>
            <div class="modal-body">
                <template id="deleteModalTemplate">
                    <div class="form-group">
                        <label class="col-md-4 control-label"></label>
                        <label class="control-label"></label>
                    </div>
                </template>
                <div class="form-horizontal" id="deleteModalBody">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger delete_button">{{__('admin.execution')}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('admin.close')}}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div class="modal fade" id="csvImportModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(['method'=>'POST','enctype'=>'multipart/form-data','id'=>'csv_import_form']) }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h4 class="modal-title">CSV {{__('admin.import')}}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        {{ Form::form_file('csv',__('admin.excel_file')) }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary import_button">{{__('admin.import')}}</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('admin.cancel')}}</button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
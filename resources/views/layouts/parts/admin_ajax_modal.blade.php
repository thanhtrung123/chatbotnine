<div class="modal fade" id="ajaxModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <p class="modal-message"></p>
                <div class="modal-status">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped modal-status-message" role="progressbar" style="width: 100%;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary ajax_exec_button">{{__('admin.learning.実行')}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('admin.learning.閉じる')}}</button>
            </div>
        </div>
    </div>
</div>
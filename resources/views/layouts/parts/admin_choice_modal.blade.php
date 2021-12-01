<div class="modal fade" id="choiceModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <p class="modal-message"></p>

                <div class="row">
                    <div class="col-md-4" id="choice_title"></div>
                    <div class="col-md-8" id="choice_body"></div>
                </div>

                <div class="modal-status">
                    {{--                    <div class="progress">--}}
                    {{--                        <div class="progress-bar progress-bar-success progress-bar-striped modal-status-message" role="progressbar" style="width: 100%;">--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary choice_select_button">{{__('admin.execution')}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('admin.cancel')}}</button>
            </div>
        </div>
    </div>
</div>
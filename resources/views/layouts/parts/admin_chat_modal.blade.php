<div class="modal fade" id="chatModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                <h4 class="modal-title">{{__('admin.chat_bot')}}</h4>
            </div>
            <div class="modal-body">
                <iframe id="chat_bot_iframe" data-src="{{ route('home') }}" style="border:1px solid gray;width: 100%;height: 480px;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="document.getElementById('chat_bot_iframe').contentDocument.location.reload(true);">{{__('admin.reset')}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('admin.cancel')}}</button>
            </div>
        </div>
    </div>
</div>
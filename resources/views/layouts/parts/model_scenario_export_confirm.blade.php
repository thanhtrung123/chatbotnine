{{ Form::open(['method'=>'POST', 'enctype'=>'multipart/form-data', 'class'=>'form-horizontal','id'=>'formScenarioExport']) }}
    {{ Form::hidden('confirm', old('confirm'), ['class' => 'confirm'])}}
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    <h4 class="modal-title">インポート確認</h4>
                </div>
                <div class="modal-body" style="margin-right: 15px; color:red">
                ！！注意！！現状のシナリオデータは全て削除されます。<br />
                このまま、シナリオデータを復元される場合、以下の復元ボタンをクリックして下さい。
                </div>
                <div class="modal-footer-center modal-footer--mine">
                    <button type="button" id="import-confirm" class="btn btn-primary">復元</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">キャンセル</button>
                </div>
            </div>
    </div>
{{ Form::close() }}
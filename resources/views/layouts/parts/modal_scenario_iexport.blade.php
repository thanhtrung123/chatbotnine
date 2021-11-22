<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h4>インポート</h4>
        </div>
        <!-- Body -->
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    {{ Form::open(['url'=>'','method'=>'POST', 'enctype'=>'multipart/form-data','id'=>'formScenarioIExport']) }}
                        {{ Form::hidden('confirm', old('confirm'), ['class' => 'confirm'])}}
                        {{ Form::hidden('export-zip', old('export-zip'), ['class' => 'export-zip'])}}
                        <div class="form-group text-center">
                            シナリオデータ（バックアップ用）からダウンロードしたZIPファイルを指定してください。
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 control-label">
                                {{ Form::label('scenario_file', 'シナリオファイル') }}
                            </div>
                            <div class="col-md-10 upload-frm">
                                <label for="zip"><span>ファイルを選択</span></label>
                                {{ Form::file('zip',['class' => 'form-control upload-input', 'id' => 'zip']) }}
                                {{ Form::text('uploadName', '', ['class' => 'form-control upload-name', 'id' => 'uploadName', 'disabled' => 'disabled']) }}
                            </div>
                        </div>
                        <div class="row form-group" style="padding-bottom: 15px;border-bottom: 1px solid #e5e5e5;">
                            <div class="col-md-2 control-label">
                            </div>
                            <div class="col-md-10">
                                <label class="error_message" style="display:none;width: 90%;color: red;">
                                </label>
                                <button type="submit" id="import-zip" class="btn btn-iexport-sm btn-lg btn-block" title="シナリオ復元" name="confirm" value="0"> シナリオ復元</button>
                            </div>
                        </div>
                        <div class="row" style="padding-left:15px;">
                            <h4>エクスポート</h4>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 control-label">
                            </div>
                            <div class="col-md-10">
                                <button type="submit" id="export-excel" class="btn btn-iexport-sm btn-lg btn-block" title="シナリオ一覧（閲覧用）ダウンロード"> シナリオ一覧（閲覧用）ダウンロード</button>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 control-label">
                            </div>
                            <div class="col-md-10">
                                <button type="submit" id="download-zip" class="btn btn-iexport-sm btn-lg btn-block" title="シナリオデータ（バックアップ用）ダウンロード"> シナリオデータ（バックアップ用）ダウンロード</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="modal-footer-center modal-footer--mine">
                <button type="button" class="btn btn-default closeModalExport" data-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-dialog modal-lg form-horizontal">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="modal-title">ＱＡデータ</h4>
        </div>
        <!-- Body -->
        @php
            $data = app('request')->all();
        @endphp
        <div class="modal-body message_body">
            <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <div class="form-group row">
                    <div class="col-md-3 control-label">
                        <label>{{ __('カテゴリ') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        <p>{{ $categories['name'] ?? 'なし' }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 control-label">
                        <label>{{ __('質問文章') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        <p>{{ $data['question'] }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 control-label">
                        <label>{{ __('回答文章') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        <p>{{ $data['answer'] }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 control-label">
                        <label>{{ __('メタデータ(仮)') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        <p>{{ $data['metadata'] }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 control-label">
                        <label>{{ __('キーフレーズ') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        @foreach ($key_phrases as $row)
                            <div class="row">
                                <div class="keyword_block">
                                    <div class="col-md-12">
                                        <span> {{ $row }} </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <!-- footer -->
        <div class="modal-footer modal-footer--mine" style="text-align: center">
            <button type="button" class="btn btn-default closeModalAddSc" data-dismiss="modal">閉じる</button>
        </div>
    </div>
</div>
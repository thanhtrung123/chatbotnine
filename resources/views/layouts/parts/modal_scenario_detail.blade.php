
<div class="modal-dialog modal-lg form-horizontal">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="modal-title">シナリオ 情報</h4>
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
                        <label>{{ __('シナリオ') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        <p>{{ $data['name'] }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 control-label">
                        <label>{{ __('表示順') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        <p>{{ $data['order'] }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 control-label">
                        <label>{{ __('関連キーワード') }}</label>
                    </div>
                    <div class="col-md-9 control-content">
                        @foreach ($keywords as $rows)
                            <div class="row form-group">
                                @foreach ($rows as $row)
                                <div class="keyword_block">
                                    <div class="col-keyword">
                                        <span> {{ $row }} </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
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

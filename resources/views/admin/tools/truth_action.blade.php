@extends('layouts.admin')
@section('pageTitle', '裏ツール 真理表(操作)')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>@yield('pageTitle')</h3>
            <a href="{{route('admin.tools.index')}}">裏ツールトップへ戻る</a>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">操作</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <a class="btn btn-default" href="javascript:void(0);" id="sync_truth">真理表再生成</a>
                                    <div id="sync_disp"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    var api_ids = @json($api_ids), id_len = api_ids.length;
            //クリック時
            $('#sync_truth').click(function (e) {
        if (!confirm("真理表の再生成を行います。よろしいですか？"))
            return false;
        var $this = $(this), sd = $('#sync_disp');

        $this.addClass('disabled');

        var syncTruth = function (idx) {
            sd.text('真理表生成中…[' + api_ids[idx] + '] (' + (idx + 1) + '/' + id_len + ')');
            base.util.ajax('get', "{{route('admin.tools.truth_sync')}}", {api_id: api_ids[idx]}).done(function (ret) {
                idx++;
                if (api_ids[idx] == void 0) {
                    $this.removeClass('disabled');
                    sd.text('完了');
                } else {
                    syncTruth(idx);
                }
            }).fail(function (ret) {
                base.util.ajaxFail(ret);
                $this.removeClass('disabled');
                sd.text('エラー');
            });
        };
        syncTruth(0);

    });


</script>


@endsection

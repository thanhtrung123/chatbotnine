@extends('layouts.admin')
@section('pageTitle', __('キーフレーズ').' 一覧')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>

                <div class="row">
                    <div class="col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {{ Form::open(['class'=>'form-horizontal','id'=>'search_form']) }}
                                {{ Form::form_text('keyword','キーワード',false,['autofocus'=>true]) }}
                                {{ Form::form_checkbox('disabled[]','状態',$statuses) }}
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">検索</button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                @if(auth()->user()->can('key_phrase create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.key_phrase.create') }}">新規追加</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">新規追加</a>
                                @endif
                                @if(auth()->user()->can('key_phrase import'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.key_phrase.import') }}">インポート</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">インポート</a>
                                @endif
                                @if(auth()->user()->can('key_phrase export'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.key_phrase.export') }}">エクスポート</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">エクスポート</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <table class="table data-table" id="dtable_key_phrase" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.key_phrase.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="cnt">件数</th>
                                        <th data-name="key_phrase_id">ID</th>
                                        <th data-name="word">{{ __('キーフレーズ') }}</th>
                                        <th data-name="replace_word">置換後文字</th>
                                        <th data-name="priority">優先度</th>
                                        <th data-name="type">タイプ</th>
                                        <th data-name="disabled">状態</th>
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('key_phrase edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.key_phrase.edit',['key_phrase'=>'%id%']) }}">修正</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">修正</a>
                                                @endif

                                                {{--                                                @php--}}
                                                {{--                                                    $disable_btn = ['type' => 'disabled','params' => ['action'=>route('admin.key_phrase.destroy',['key_phrase'=>'%id%','status'=>'%status_orig%'])]];--}}
                                                {{--                                                @endphp--}}
                                                {{--                                                @if(auth()->user()->can('key_phrase edit'))--}}
                                                {{--                                                    <a class="btn btn-default" data-modal='@json($disable_btn)'>無効</a>--}}
                                                {{--                                                @else--}}
                                                {{--                                                    <a class="btn btn-default" disabled="">無効</a>--}}
                                                {{--                                                @endif--}}

                                                @if(auth()->user()->can('key_phrase destroy'))
                                                    <a class="btn btn-default del_btn" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.key_phrase.destroy',['key_phrase'=>'%id%'])]
                                                ])'>削除</a>
                                                @else
                                                    <a class="btn btn-default del_btn" disabled="">削除</a>
                                                @endif
                                            </template>
                                        </th>
                                    </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(function () {
            base.dataTables.callback.onDraw.add(function (e, s) {
                // console.log(e);
                $.each(s.json.data, (i, row) => {
                    if (row.cnt > 0) $('.del_btn').eq(i).addClass('disabled');
                });
            })
        });
    </script>

@endsection

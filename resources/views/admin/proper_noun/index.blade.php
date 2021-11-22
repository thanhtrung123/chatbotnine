@extends('layouts.admin')
@section('pageTitle', __('固有名詞').' 一覧')
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
                                @if(auth()->user()->can('proper_noun create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.proper_noun.create') }}">新規追加</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">新規追加</a>
                                @endif
                                @if(auth()->user()->can('proper_noun import'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.proper_noun.import') }}">インポート</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">インポート</a>
                                @endif
                                @if(auth()->user()->can('proper_noun export'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.proper_noun.export') }}">エクスポート</a>
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

                                <table class="table data-table" id="dtable_proper_noun" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.proper_noun.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="proper_noun_id">ID</th>
                                        <th data-name="word">{{ __('固有名詞') }}</th>
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('proper_noun edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.proper_noun.edit',['proper_noun'=>'%id%']) }}">修正</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">修正</a>
                                                @endif
                                                @if(auth()->user()->can('proper_noun destroy'))
                                                    <a class="btn btn-default del_btn" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.proper_noun.destroy',['proper_noun'=>'%id%'])]
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

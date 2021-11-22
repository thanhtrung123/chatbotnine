@extends('layouts.admin')
@section('pageTitle', __('アカウント情報').' 一覧')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <div class="row bottom-buf15">
                                    <div class="col-md-10"></div><div class="col-md-2">
                                    @if(auth()->user()->can('user create'))
                                        <a class="btn btn-default btn-block" href="{{ route('admin.user.create') }}">新規作成</a>
                                    @else
                                        <a class="btn btn-default btn-block" disabled="">新規作成</a>
                                    @endif
                                    </div>
                                </div>

                                <table class="table data-table" id="dtable_user" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.user.list') ],
                                   'setting' =>['stateSave'=>true],
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="display_name">表示名</th>
                                        <th data-name="name">ログインID</th>
                                        <th data-name="email">メールアドレス</th>
                                        <th data-template="true">
                                            <template><a class="btn btn-default" href="{{ route('admin.user.show',['user'=>'%id%']) }}">詳細</a></template>
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
@endsection

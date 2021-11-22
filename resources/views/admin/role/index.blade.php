@extends('layouts.admin')
@section('pageTitle', __('権限情報').' 一覧')
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
                                    <div class="col-md-10"></div>
                                    <div class="col-md-2">
                                        @if(auth()->user()->can('role create'))
                                            <a class="btn btn-default btn-block" href="{{ route('admin.role.create') }}">新規作成</a>
                                        @else
                                            <a class="btn btn-default btn-block" disabled="">新規作成</a>
                                        @endif
                                    </div>
                                </div>

                                <table class="table data-table" id="dtable_role" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.role.list') ],
                                   'setting' =>['stateSave'=>true],
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="display_name">表示名</th>
                                        <th data-name="name">ロール名</th>
                                        <th data-template="true">
                                            <template><a class="btn btn-default" href="{{ route('admin.role.show',['role'=>'%id%']) }}">詳細</a></template>
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

@extends('layouts.admin')
@section('pageTitle', __('admin.header.account_inf').__('admin.list'))
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
                                        <a class="btn btn-default btn-block" href="{{ route('admin.user.create') }}">{{__('admin.user.create_new')}}</a>
                                    @else
                                        <a class="btn btn-default btn-block" disabled="">{{__('admin.user.create_new')}}</a>
                                    @endif
                                    </div>
                                </div>

                                <table class="table data-table" id="dtable_user" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.user.list') ],
                                   'setting' =>['stateSave'=>true],
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="display_name">{{__('admin.user.display_name')}}</th>
                                        <th data-name="name">{{__('admin.user.login_id')}}</th>
                                        <th data-name="email">{{__('admin.user.email')}}</th>
                                        <th data-template="true">
                                            <template><a class="btn btn-default" href="{{ route('admin.user.show',['user'=>'%id%']) }}">{{__('admin.detail')}}</a></template>
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

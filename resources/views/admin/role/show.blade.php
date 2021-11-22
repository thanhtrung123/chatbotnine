@extends('layouts.admin')
@section('pageTitle', __('権限情報').' 詳細')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {{ Form::open(['url'=>route('admin.role.destroy',['role'=>$id]),'method'=>'DELETE','class'=>'form-horizontal','id'=>'delete_form']) }}
                                <div class="form-group" data-confirm>
                                    <label class="col-md-4 control-label">表示名</label>
                                    <label class="col-md-6 checkbox-inline">
                                        {{ $data['display_name'] }}
                                    </label>
                                </div>
                                <div class="form-group" data-confirm>
                                    <label class="col-md-4 control-label">ロール名</label>
                                    <label class="col-md-6 checkbox-inline">
                                        {{ $data['name'] }}
                                    </label>
                                </div>
                                
                                @include('admin.role.permission',['readonly'=>true])
                                
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        @if(auth()->user()->can('role edit'))
                                            <a class="btn btn-success btn-block" href="{{ route('admin.role.edit',['role'=>$id]) }}">修正</a>
                                        @else
                                            <a class="btn btn-success btn-block" disabled="">修正</a>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        @if(auth()->user()->can('role destroy') && $data['name'] != config('acl.admin.role'))
                                            <button class="btn btn-danger btn-block" type="submit" name="confirm" value="0">削除</button>
                                        @else
                                            <a class="btn btn-danger btn-block" disabled="">削除</a>
                                        @endif
                                    </div>
                                    <div class="col-md-2"><a class="btn btn-default btn-block" type="submit" href="{{ route('admin.role.index',['r'=>1]) }}">戻る</a></div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isConfirm)
        {{ Form::form_confirm_script('delete_form','delete')  }}
    @endif
@endsection

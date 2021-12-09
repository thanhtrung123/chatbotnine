@extends('layouts.admin')
@section('pageTitle', __('admin.header.related_questions').__('admin.list'))
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
                                {{ Form::form_text('keyword',__('admin.keyword'),false,['autofocus'=>true]) }}
                                {{ Form::form_text('api_id','API_ID',false,[]) }}
                                {{ Form::form_text('relation_api_id',__('admin.learning_relation.relation_api_id'),false,[]) }}
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.search')}}</button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                @if(auth()->user()->can('learning_relation create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning_relation.create') }}">{{__('admin.create')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.create')}}</a>
                                @endif
                                @if(auth()->user()->can('learning_relation import'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning_relation.import') }}">{{__('admin.import')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.import')}}</a>
                                @endif
                                @if(auth()->user()->can('learning_relation export'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning_relation.export') }}">{{__('admin.export')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.export')}}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                @php
                                    $dtable = [
                                       'ajax'=>[ 'url' => route('api.admin.learning_relation.list') ],
                                       'setting' =>['stateSave'=>true,'order'=>[[2,'asc'],[4,'asc']]],
                                       'form' => ['search_form']
                                       ];
                                @endphp

                                <table class="table data-table" id="dtable_learning_relation" style="width: 100%;" data-tables='@json($dtable)'>
                                    <thead>
                                    <tr>
                                        <th data-name="id">ID</th>
                                        <th data-name="name">{{__('admin.learning_relation.related_question_name')}}</th>
                                        <th data-name="api_id">API_ID</th>
                                        <th data-name="relation_api_id">{{__('admin.learning_relation.relation_api_id')}}</th>
                                        <th data-name="order">{{__('admin.learning_relation.display_order')}}</th>
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('learning_relation edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.learning_relation.edit',['user'=>'%id%']) }}">{{__('admin.edit')}}</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">{{__('admin.edit')}}</a>
                                                @endif
                                                @if(auth()->user()->can('learning_relation destroy'))
                                                    <a class="btn btn-default" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.learning_relation.destroy',['id'=>'%id%'])]
                                                ])'>{{__('admin.delete')}}</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">{{__('admin.delete')}}</a>
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
@endsection

@extends('layouts.admin')
@section('pageTitle', __('admin.header.training_data'). __('admin.list'))
@section('content')
@section('cssfiles')
    <link rel="stylesheet" href="{{ asset(mix('css/synonym.css')) }}" type="text/css">
@endsection
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
                                @if(config('bot.truth.enabled'))
                                    {{ Form::form_text('keyword_key_phrase',__('admin.learning.key_phrase'),false,[]) }}
                                @endif
                                {{ Form::form_select('category_id',__('admin.learning.category'), $category_data,false,['class'=>'select2']) }}
                                {{--                                {{ Form::form_text('keyword_meta','キーワード(メタ)',false,[]) }}--}}
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
                                @if(auth()->user()->can('learning create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning.create') }}">{{__('admin.create')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.create')}}</a>
                                @endif
                                @if(auth()->user()->can('learning import'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning.import') }}">{{__('admin.import')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.import')}}</a>
                                @endif
                                @if(auth()->user()->can('learning export'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.learning.export') }}">{{__('admin.export')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.export')}}</a>
                                @endif
                                @php
                                    $chain = [];
                                    $chain[] = route('api.admin.learning.sync',['mode'=>'delete']);
                                    $chain[] = route('api.admin.learning.sync',['mode'=>'add']);
                                    $ajax_modal = [
                                        'type' => 'ajax',
                                        'ajax' => ['url' => route('api.admin.learning.sync',['mode'=>'morph'])],
                                        'chain' => $chain,
                                        'message' => [
                                            'title' => __('admin.learning.training_data').__('admin.learning.sync'),
                                            'body' => __('admin.learning.for_chatbot_api').__('admin.learning.training_data').__('admin.learning.を反映させます。').__('admin.learning.よろしければ、実行ボタンを押下してください。'),
                                        ],
                                    ];
                                @endphp
                                <a id='sysn' class="btn btn-block {{ ($count_learning > 0) ? 'button-glow' : 'btn-default' }}" data-modal='@json($ajax_modal)'>{{__('admin.learning.sync')}}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <table class="table data-table" id="dtable_learning" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.learning.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="api_id">ID</th>
                                        <th data-name="question">{{__('admin.learning.question_text')}}</th>
                                        <th data-name="answer">{{__('admin.learning.answer_text')}}</th>
                                        {{--                                        <th data-name="question_morph">解析後(仮</th>--}}
                                        {{--                                        <th data-name="metadata">メタ(仮</th>--}}
                                        @if(config('bot.truth.enabled'))
                                            <th data-name="key_phrase">{{__('admin.learning.key_phrase')}}</th>
                                        @endif
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('learning edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.learning.edit',['user'=>'%id%']) }}">{{__('admin.edit')}}</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">{{__('admin.edit')}}</a>
                                                @endif
                                                @if(auth()->user()->can('learning destroy'))
                                                    <a class="btn btn-default" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.learning.destroy',['learning'=>'%id%'])]
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

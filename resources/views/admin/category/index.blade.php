@extends('layouts.admin')
@section('pageTitle', __('admin.header.カテゴリ').__('admin.一覧'))
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
                                {{ Form::form_text('keyword',__('admin.キーワード'),false,['autofocus'=>true]) }}
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.検索')}}</button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                @if(auth()->user()->can('category create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.category.create') }}">{{__('admin.新規追加')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.新規追加')}}</a>
                                @endif
{{--                                @if(auth()->user()->can('category import'))--}}
{{--                                    <a class="btn btn-block btn-default" href="{{ route('admin.category.import') }}">インポート</a>--}}
{{--                                @else--}}
{{--                                    <a class="btn btn-block btn-default" disabled="">インポート</a>--}}
{{--                                @endif--}}
{{--                                @if(auth()->user()->can('category export'))--}}
{{--                                    <a class="btn btn-block btn-default" href="{{ route('admin.category.export') }}">エクスポート</a>--}}
{{--                                @else--}}
{{--                                    <a class="btn btn-block btn-default" disabled="">エクスポート</a>--}}
{{--                                @endif--}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <table class="table data-table" id="dtable_category" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.category.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="name">{{__('admin.category.カテゴリ名')}}</th>
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('category edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.category.edit',['user'=>'%id%']) }}">{{__('admin.修正')}}</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">{{__('admin.修正')}}</a>
                                                @endif
                                                @if(auth()->user()->can('category destroy'))
                                                    <a class="btn btn-default" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.category.destroy',['category'=>'%id%'])]
                                                ])'>{{__('admin.削除')}}</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">{{__('admin.削除')}}</a>
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

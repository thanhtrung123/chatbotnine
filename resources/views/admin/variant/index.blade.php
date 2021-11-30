@extends('layouts.admin')
@section('pageTitle', __('admin.header.異表記データ').__('admin.一覧'))
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
                            @if(auth()->user()->can('variant create'))
                            <a class="btn btn-block btn-default" href="{{ route('admin.variant.create') }}">{{__('admin.新規追加')}}</a>
                            @else
                            <a class="btn btn-block btn-default" disabled="">{{__('admin.新規追加')}}</a>
                            @endif
                            @if(auth()->user()->can('variant import'))
                            <a class="btn btn-block btn-default" href="{{ route('admin.variant.import') }}">{{__('admin.インポート')}}</a>
                            @else
                            <a class="btn btn-block btn-default" disabled="">{{__('admin.インポート')}}</a>
                            @endif
                            @if(auth()->user()->can('variant export'))
                            <a class="btn btn-block btn-default" href="{{ route('admin.variant.export') }}">{{__('admin.エクスポート')}}</a>
                            @else
                            <a class="btn btn-block btn-default" disabled="">エクスポート{{__('admin.検索')}}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <table class="table data-table" id="dtable_variant" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.variant.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                   <thead>
                                    <tr>
                                        <th data-name="noun_variant_text">{{__('admin.variant.異表記文字')}}</th>
                                        <th data-name="noun_text">{{__('admin.variant.置換後文字')}}</th>
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('variant edit'))
                                                <a class="btn btn-default" href="{{ route('admin.variant.edit',['user'=>'%id%']) }}">{{__('admin.修正')}}</a>
                                                @else
                                                <a class="btn btn-default" disabled="">{{__('admin.修正')}}</a>
                                                @endif
                                                @if(auth()->user()->can('variant destroy'))
                                                <a class="btn btn-default" data-modal='@json([
                                                   'type' => 'delete',
                                                   'params' => ['action'=>route('admin.variant.destroy',['variant'=>'%id%'])]
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

@extends('layouts.admin')
@section('pageTitle', __('admin.header.synonym_data').__('admin.list'))
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
                                @if(auth()->user()->can('synonym create'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.synonym.create') }}">{{__('admin.create')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.create')}}</a>
                                @endif
                                @if(auth()->user()->can('synonym import'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.synonym.import') }}">{{__('admin.import')}}</a>
                                @else
                                    <a class="btn btn-block btn-default" disabled="">{{__('admin.import')}}</a>
                                @endif
                                @if(auth()->user()->can('synonym export'))
                                    <a class="btn btn-block btn-default" href="{{ route('admin.synonym.export') }}">{{__('admin.export')}}</a>
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

                                <table class="table data-table" id="dtable_synonym" style="width: 100%;" data-tables='@json([
                                   'ajax'=>[ 'url' => route('api.admin.synonym.list') ],
                                   'setting' =>['stateSave'=>true],
                                   'form' => ['search_form']
                                   ])'>
                                    <thead>
                                    <tr>
                                        <th data-name="keyword">{{__('admin.synonym.synonyms_char')}}</th>
                                        <th data-name="synonym">{{__('admin.synonym.text_after_rep')}}</th>
                                        <th data-template="true">
                                            <template>
                                                @if(auth()->user()->can('synonym edit'))
                                                    <a class="btn btn-default" href="{{ route('admin.synonym.edit',['user'=>'%id%']) }}">{{__('admin.edit')}}</a>
                                                @else
                                                    <a class="btn btn-default" disabled="">{{__('admin.edit')}}</a>
                                                @endif
                                                @if(auth()->user()->can('synonym destroy'))
                                                    <a class="btn btn-default" data-modal='@json([
                                                'type' => 'delete',
                                                'params' => ['action'=>route('admin.synonym.destroy',['synonym'=>'%id%'])]
                                                ])'>{{__('admin.削除')}}</a>
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

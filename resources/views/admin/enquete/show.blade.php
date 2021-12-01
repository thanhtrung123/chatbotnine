@extends('layouts.admin')
@section('pageTitle', __('admin.header.questionnaire').__('admin.detail'))
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <table class="table data-table" id="dtable_user" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>{{__('admin.enquete.question')}}</th>
                                            <th>{{__('admin.enquete.answer')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($enq_collection->toArray() as $enq_item)
                                            <tr>
                                                <th>{{ $enq_item->id }}</th>
                                                <th>{{ $enq_item->question }}</th>
                                                <th>
                                                    @if ($enq_item->type == config('const.enquete.form.checkbox.id'))
                                                        @foreach(explode(',', $enq_item->answer) as $answer)
                                                            {{ $answer }}<br />
                                                        @endforeach
                                                    @else
                                                        {{ $enq_item->answer }}
                                                    @endif
                                                </th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        <a class="btn btn-default btn-block" href="{{ route('admin.enquete.index',['r'=>1]) }}">{{__('admin.cancel')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

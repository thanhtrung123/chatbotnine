@extends('layouts.admin')
@section('pageTitle', __('admin.header.questionnaire').__('admin.list'))
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
                            {{ Form::form_text('chat_id',__('admin.enquete.questionnaire_id'),false,['autofocus'=>true]) }}
                            {{ Form::form_text('date_s',__('admin.enquete.date_start'),false,['class'=>'datepicker', 'autocomplete'=>'off']) }}
                            {{ Form::form_text('date_e',__('admin.enquete.date_end'),false,['class'=>'datepicker', 'autocomplete'=>'off']) }}
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
{{--
                            @if(auth()->user()->can('enquete export'))
--}}
                                <a id='export_btn' class="btn btn-block btn-default" href="{{ route('admin.enquete.export') }}">{{__('admin.export')}}</a>
{{--
                            @else
                                <a class="btn btn-block btn-default" disabled="">エクスポート</a>
                            @endif
--}}
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
                                   'ajax'=>[ 'url' => route('api.admin.enquete.list') ],
                                   'setting' =>['stateSave'=>true, 'order'=>[[0, 'asc']]],
                                   'form' => ['search_form']
                                ];
                            @endphp
                            <table class="table data-table" id="dtable_user" style="width: 100%;" data-tables='@json($dtable)'>
                                <thead>
                                    <tr>
                                        <th data-name="post_id">{{__('admin.enquete.questionnaire_id')}}</th>
                                        <th data-name="posted_at" data-format="datetime">{{__('admin.enquete.date_time')}}</th>
                                        <th data-name="chat_id">{{__('admin.enquete.chat_id')}}</th>
                                        <th data-template="true">
                                            <template><a class="btn btn-default" href="{{ route('admin.enquete.show',['post_id'=>'%post_id%']) }}">{{__('admin.detail')}}</a></template>
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
<script type="text/javascript">
    jQuery(function(){
        var base_url = '{{ route('admin.enquete.export') }}';
        // 初回時の設定
        jQuery('#export_btn').attr('href', base_url + '?' + $('form#search_form').serialize());
        // 検索時の設定
        jQuery('#search_form').submit(function(){
            jQuery('#export_btn').attr('href', base_url + '?' + $('form#search_form').serialize());
        });
    })
</script>
@endsection

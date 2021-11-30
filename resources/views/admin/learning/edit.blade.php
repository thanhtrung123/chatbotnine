@extends('layouts.admin')
@section('pageTitle', __('admin.header.学習データ') .__('admin.修正'))
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-10">
                                        {{ Form::open(['url'=>route('admin.learning.update',['user'=>$id]),'method'=>'PUT','class'=>'form-horizontal','id'=>'entry_form']) }}

                                        <div id="confirm_area_1">
                                            {{ Form::form_select('category_id',__('admin.learning.カテゴリ'), $category_data,true,['class'=>'select2']) }}
                                            {{ Form::form_textarea('question',__('admin.learning.質問文章'),true,['required'=>true,'autofocus'=>true,'rows'=>3]) }}
                                            {{ Form::form_textarea('answer',__('admin.learning.回答文章'),true,['required'=>true,'rows'=>6]) }}
                                            {{ Form::form_text('metadata',__('admin.learning.メタデータ(仮)')) }}
                                            @if(config('bot.truth.enabled'))
                                                {{ Form::form_checkbox('auto_key_phrase_disabled',__('admin.learning.キーフレーズ設定'),['1'=>__('admin.learning.キーフレーズを手動で設定する')],true,['data-input-toggle'=>'#key_phrase_area']) }}
                                            @endif
                                        </div>

                                        {{-- 追加用クローンテンプレート--}}
                                        <template id="clone_src">
                                            <div class="row bottom-buf5 key_phrase_block" id="key_phrase_block_%idx%">
                                                <div class="col-md-6">
                                                    <input class="form-control" name="truth_data[%idx%][key_phrase]" required>
                                                    <input type="hidden" name="truth_data[%idx%][truth_id]">
                                                    <input type="hidden" class="clone_idx" value="%idx%">
                                                </div>
                                                <div class="col-md-1 text-center">
                                                    <input type="hidden" name="truth_data[%idx%][auto_key_phrase_priority_disabled]" value="0">
                                                    <input type="checkbox" class="checkbox-inline" name="truth_data[%idx%][auto_key_phrase_priority_disabled]" value="1"
                                                           data-input-toggle='@json(['area'=>'#priority_area_%idx%','readonly'=>'1'])'>
                                                </div>
                                                <div class="col-md-2" id="priority_area_%idx%">
                                                    <input class="form-control" name="truth_data[%idx%][key_phrase_priority]">
                                                </div>
                                                @php
                                                    $replace_input = [
                                                        'type' => 'select',
                                                        'ajax' => ['url'=>route('api.admin.key_phrase.choice')],
                                                        'target' => '[name="truth_data[%idx%][key_phrase]"]',
                                                        'replace_name' => ['key_phrase','key_phrase_id'],
                                                    ];
                                                @endphp
                                                <div class="col-md-3" data-confirm-ignore="">
                                                    <input type="button" value="変更" class="btn btn-default" data-replace-input='@json($replace_input)'>
                                                    <input type="button" value="削除" class="btn btn-default" data-dom-delete='@json(['target'=>'#key_phrase_block_%idx%','parent'=>'.key_phrase_block','limit'=>1])'>
                                                </div>
                                            </div>
                                        </template>
                                        {{-- 追加用クローンテンプレート--}}

                                        @if(config('bot.truth.enabled'))
                                            <div id="key_phrase_area">
                                                <div class="form-group" id="key_phrase_confirm">
                                                    <label for="name" class="col-md-4 control-label">{{ __('admin.learning.キーフレーズ') }}</label>
                                                    <div class="col-md-8">

                                                        <div class="row bottom-buf5">
                                                            <div class="col-md-6">
                                                            </div>
                                                            {{--                                                            <div class="col-md-2 text-center">--}}
                                                            {{--                                                                <b>優先度<br/>を指定</b>--}}
                                                            {{--                                                            </div>--}}
                                                            <div class="col-md-3 text-center">
                                                                <b>{{__('admin.learning.優先度を指定/値')}}</b>
                                                            </div>
                                                            <div class="col-md-3">
                                                            </div>
                                                        </div>

                                                        <div id="clone_area">
                                                            @foreach ($key_phrases as $idx => $row)
                                                                <div class="row bottom-buf5 key_phrase_block" id="key_phrase_block_{{$idx}}">
                                                                    <div class="col-md-6{{ $errors->has("truth_data.{$idx}.key_phrase") ? ' has-error' : '' }}">
                                                                        <input class="form-control" {{ empty($row['truth_id']) ? '' : 'readonly' }} name="truth_data[{{$idx}}][key_phrase]"
                                                                               value="{{ old("truth_data.{$idx}.key_phrase",request()->truth_data[$idx]['key_phrase']) }}">
                                                                        <input type="hidden" name="truth_data[{{$idx}}][truth_id]" value="{{ old("truth_data.{$idx}.truth_id",request()->truth_data[$idx]['truth_id']) }}">
                                                                        <input type="hidden" class="clone_idx" value="{{$idx}}">
                                                                        {{ Form::form_line_error("truth_data.{$idx}.key_phrase") }}

                                                                    </div>
                                                                    <div class="col-md-1 text-center">
                                                                        <input type="hidden" name="truth_data[{{$idx}}][auto_key_phrase_priority_disabled]" value="0">
                                                                        <input type="checkbox" class="checkbox-inline" name="truth_data[{{$idx}}][auto_key_phrase_priority_disabled]"
                                                                               {{ empty(old("truth_data.{$idx}.auto_key_phrase_priority_disabled",request()->truth_data[$idx]['auto_key_phrase_priority_disabled'])) ? '' : 'checked' }} value="1"
                                                                               data-input-toggle='@json(['area'=>"#priority_area_{$idx}",'readonly'=>'1'])' data-confirm-value="✔">
                                                                    </div>
                                                                    <div class="col-md-2{{ $errors->has("truth_data.{$idx}.key_phrase_priority") ? ' has-error' : '' }}" id="priority_area_{{$idx}}">
                                                                        <input class="form-control" name="truth_data[{{$idx}}][key_phrase_priority]"
                                                                               value="{{ old("truth_data.{$idx}.key_phrase_priority",request()->truth_data[$idx]['key_phrase_priority']) }}">
                                                                        {{ Form::form_line_error("truth_data.{$idx}.key_phrase_priority") }}
                                                                    </div>
                                                                    @php
                                                                        $replace_input = [
                                                                            'type' => 'select',
                                                                            'ajax' => ['url'=>route('api.admin.key_phrase.choice')],
                                                                            'target' => '[name="truth_data['.$idx.'][key_phrase]"]',
                                                                            'replace_name' => ['key_phrase','key_phrase_id'],
                                                                        ];
                                                                    @endphp
                                                                    <div class="col-md-3" data-confirm-ignore="">
                                                                        <input type="button" value="{{__('admin.変更')}}" class="btn btn-default" data-replace-input='@json($replace_input)'>

                                                                        <input type="button" value="{{__('admin.削除')}}" class="btn btn-default" data-dom-delete='@json(['target'=>'#key_phrase_block_'.$idx,'parent'=>'.key_phrase_block','limit'=>1])'>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="row bottom-buf5">
                                                            <div class="col-md-9"></div>
                                                            <div class="col-md-3" data-confirm-ignore="">
                                                                @php
                                                                    $add=['src'=>'#clone_src','area'=>'#clone_area','idx_src'=>'.clone_idx']
                                                                @endphp
                                                                <input type="button" value="{{__('admin.追加')}}" class="btn btn-default" data-dom-copy='@json($add)'>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary btn-block" type="submit" name="confirm" value="0">{{__('admin.確認')}}</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a class="btn btn-default btn-block" href="{{ route('admin.learning.index',['r'=>1]) }}">{{__('admin.戻る')}}</a>
                                            </div>
                                        </div>

                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isConfirm)
        {{ Form::form_confirm_script('entry_form','update',['confirm_area_1','key_phrase_area'])  }}
    @endif
    <script>
        //チェックボックス変更時
        base.form.callback.inputToggle.add(function (flg, chk) {
            //キーフレーズ手動・自動切替時、確認画面に出すかどうかを設定
            if (chk.attr('name') != 'auto_key_phrase_disabled') return;
            if (flg) $('#key_phrase_confirm').attr('data-confirm', '');
            else $('#key_phrase_confirm').removeAttr('data-confirm');
        });
        //DOMコピー時
        base.form.callback.domCopy.after.add(function (src) {
            //行を追加したらチェックボックス変更を監視
            base.form.bindInputToggle(src);
        });
    </script>
@endsection


<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h4 class="modal-title">シナリオ 編集</h4>
        </div>
        <!-- Body -->
        <?php
$data = app('request')->all();
?>
        <div class="modal-body message_body">
            <div class="row">
                <div class="col-md-10">
                    {{ Form::open(['url'=>route('admin.scenario.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'entry_form_edit']) }}
                    <input type="hidden" name="id" value="{{$data['id']}}" />
                    {{ Form::form_select('category_id','カテゴリ', ['' => 'なし'] + $categories, $data['category_id'],['class'=>'select2', 'disabled']) }}
                    {{ Form::form_text('name','シナリオ',true,['required'=>true,'autofocus'=>true]) }}
                    {{ Form::form_text('order','表示順',true,['required'=>false,]) }}
                    {{-- 追加用クローンテンプレート--}}
                    <template id="clone_src_edit">
                        <div class="row bottom-buf5 keyword_block" id="keyword_block_%idx%">
                            <div class="col-md-10">
                                {{ Form::select('multi_data[%idx%][]', $all_keywords, '', ['class' => 'form-control select2 select2-keyword', 'data-width' => '100%', 'multiple' => 'multiple', 'id' => 'select%idx%']) }}
                                <input type="hidden" class="clone_idx" value="%idx%">
                            </div>
                            <div class="col-md-2" data-confirm-ignore="">
                                <input type="button" value="削除" class="btn btn-default" data-dom-delete='@json(['target'=>'#keyword_block_%idx%','parent'=>'.keyword_block','limit'=>1])'>
                            </div>
                        </div>
                    </template>
                    {{-- 追加用クローンテンプレート--}}

                    <div id="keyword_area" data-confirm="">
                        <div class="form-group" id="keyword_confirm">
                            <label for="name" class="col-md-4 control-label">{{ __('関連キーワード') }}</label>
                            <div class="col-md-8">

                                <div class="row bottom-buf5">
                                    <div class="col-md-8">
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                </div>

                                    <div id="clone_area_edit">
                                    @foreach ($keywords as $idx => $row)
                                        <div class="row bottom-buf5 keyword_block" id="keyword_block_{{$idx}}">
                                            <div class="col-md-10{{ $errors->has("multi_data.{$idx}.keyword") ? ' has-error' : '' }}">
                                                {{ Form::select('multi_data[' . $idx . '][]', $all_keywords, '', ['class' => 'form-control select2 select2-keyword', 'data-width' => '100%', 'multiple' => 'multiple', 'id' => 'select' . $idx]) }}
                                                <input type="hidden" class="clone_idx" value="{{$idx}}">
                                                {{ Form::form_line_error("multi_data.{$idx}.keyword") }}
                                            </div>
                                            <div class="col-md-2" data-confirm-ignore="">
                                                <input type="button" value="削除" class="btn btn-default" data-dom-delete='@json(['target'=>'#keyword_block_'.$idx,'parent'=>'.keyword_block','limit'=>0])'>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row bottom-buf5 keyword_block err-edit-scenario">
                                    <div class="col-md-10"></div>
                                    <div class="col-md-2" data-confirm-ignore="">
                                        @php
                                            $add=['src'=>'#clone_src_edit','area'=>'#clone_area_edit','idx_src'=>'.clone_idx']
                                        @endphp
                                        <input type="button" value="追加" class="btn btn-default" data-dom-add-select2='@json($add)'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>

                {{-- 追加用クローンテンプレート--}}
                <template id="clone_src">
                    <div class="row bottom-buf5 keyword_block" id="keyword_block_%idx%">
                        <div class="col-md-8">
                            <input class="form-control" name="multi_data[%idx%][keyword]" required>
                            <input type="hidden" name="multi_data[%idx%][keyword_id]">
                            <input type="hidden" class="clone_idx" value="%idx%">
                        </div>
                        @php
                            $replace_input = [
                                'type' => 'select',
                                'ajax' => ['url'=>route('api.admin.scenario_keyword.choice')],
                                'target' => '[name="multi_data[%idx%][keyword]"]',
                                'replace_name' => ['keyword','keyword_id'],
                            ];
                        @endphp
                        <div class="col-md-4" data-confirm-ignore="">
                            <input type="button" value="変更" class="btn btn-default" data-replace-input='@json($replace_input)'>
                            <input type="button" value="削除" class="btn btn-default" data-dom-delete='@json(['target'=>'#keyword_block_%idx%','parent'=>'.keyword_block','limit'=>0])'>
                        </div>
                    </div>
                </template>
        </div>
        <!-- footer -->
        <div class="modal-footer modal-footer--mine">
            <button type="button" class="btn btn-primary edit-scenario-button">確認</button>
            <button type="button" class="btn btn-default closeModalAddSc" data-dismiss="modal">閉じる</button>
        </div>
    </div>
</div>
<script>
	{!! "keywords = ". json_encode($keywords) . "" !!};
</script>
<script src="{{ asset(mix('js/select2_replace_modal_edit.js')) }}"></script>
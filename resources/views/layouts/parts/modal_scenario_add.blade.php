<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header">
            <h4>{{__('admin.scenario.scenario')}} {{__('admin.register')}}</h4>
        </div>
        <!-- Body -->
        <div class="modal-body">
            <div class="row">
                <div class="col-md-10">
                    {{ Form::open(['url'=>route('admin.scenario.store'),'method'=>'POST','class'=>'form-horizontal','id'=>'formScenario']) }}
                        <div class="row form-group">
                            <div class="col-md-4 control-label">
                                {{ Form::label('category', __('admin.header.category')) }}
                            </div>
                            <div class="col-md-8">
                                {{ Form::select('category_id', ['' => 'なし'] + $categories, '' ,['class' => 'form-control select2 scenarioCategory', 'data-width' => '100%', 'id' => 'category_id', 'disabled']) }}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4 control-label">
                                {{ Form::label('category', __('admin.scenario.scenario')) }}
                            </div>
                            <div class="col-md-8">
                                {{ Form::text('name', '' ,['class' => 'form-control', 'id' => 'nameScenario']) }}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4 control-label">
                                {{ Form::label('category',__('admin.learning_relation.display_order')) }}
                            </div>
                            <div class="col-md-8">
                                {{ Form::text('order', '' ,['class' => 'form-control order', 'id'=> 'order']) }}
                            </div>
                        </div>
                        {{ Form::hidden('parent_ids', '' ,['class' => 'form-control order', 'id'=> 'order']) }}
                        {{ Form::hidden('api_ids', '' ,['class' => 'form-control order', 'id'=> 'order']) }}
                        {{-- <div class="row"> --}}
                            {{-- 追加用クローンテンプレート--}}
                            <template id="clone_src">
                                <div class="row bottom-buf5 keyword_block" id="keyword_block_%idx%">
                                    <div class="col-md-10">
                                        {{ Form::select('multi_data[%idx%][]', $all_keywords, '', ['class' => 'form-control select2 select2-keyword', 'data-width' => '100%', 'multiple' => 'multiple', 'id' => 'select%idx%']) }}
                                        <input type="hidden" class="clone_idx" value="%idx%">
                                    </div>
                                    <div class="col-md-2" data-confirm-ignore="">
                                        <input type="button" value="{{__('admin.delete')}}" class="btn btn-default" data-dom-delete='@json(['target'=>'#keyword_block_%idx%','parent'=>'.keyword_block','limit'=>0])'>
                                    </div>
                                </div>
                            </template>
                            <div class="form-group">
                                <div class="col-md-4 control-label">
                                    {{ Form::label('keyword', __('admin.scenario.related_keywords')) }}
                                </div>
                                <div class="col-md-8">
                                    <div id="clone_area" class="keyword-add-scenario">
                                        @foreach ($keywords as $idx => $row)
                                            <div class="row bottom-buf5 keyword_block" id="keyword_block_{{$idx}}">
                                                <div class="col-md-10{{ $errors->has("multi_data.{$idx}.keyword") ? ' has-error' : '' }}">
                                                    <input class="form-control" {{ empty($row['keyword_id']) ? '' : 'readonly' }} name="multi_data[{{$idx}}][keyword]"
                                                    value="{{ old("multi_data.{$idx}.keyword",request()->multi_data[$idx]['keyword']) }}">
                                                    <input type="hidden" name="multi_data[{{$idx}}][keyword_id]" value="{{ old("multi_data.{$idx}.keyword_id",request()->multi_data[$idx]['keyword_id']) }}">
                                                    <input type="hidden" class="clone_idx" value="{{$idx}}">
                                                    {{ Form::form_line_error("multi_data.{$idx}.keyword") }}
                                                </div>
                                            </div>
                                            @php
                                            $replace_input = [
                                            'type' => 'select',
                                            'ajax' => ['url'=>route('api.admin.scenario_keyword.choice')],
                                            'target' => '[name="multi_data['.$idx.'][keyword]"]',
                                            'replace_name' => ['keyword','keyword_id'],
                                            ];
                                            @endphp
                                            <div class="col-md-2" data-confirm-ignore="">
                                                <input type="button" value="削除" class="btn btn-default" data-dom-delete='@json(['target'=>'#keyword_block_'.$idx,'parent'=>'.keyword_block','limit'=>1])'>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row bottom-buf5 err-add-scenario">
                                        <div class="col-md-10"></div>
                                        <div class="col-md-2" data-confirm-ignore="">
                                        @php
                                            $add=['src'=>'#clone_src','area'=>'#clone_area','idx_src'=>'.clone_idx']
                                        @endphp
                                        <input type="button" id="keyword_add_btn" value="{{__('admin.create')}}" class="btn btn-default" data-dom-add-select2='@json($add)'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="modal-footer modal-footer--mine">
                    <button type="button" class="btn btn-primary add-node-element-scenario">{{__('admin.submit')}}</button>
                    <button type="button" class="btn btn-default closeModalAddSc" data-dismiss="modal">{{__('admin.cancel')}}</button>
                </div>
        </div>
    </div>
</div>
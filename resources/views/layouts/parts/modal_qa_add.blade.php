<div class="modal-dialog modal-lg">
    <div class="modal-content qa-modal">
        <div class="modal-header">
            <h4>{{__('admin.scenario.qa_data')}}</h4>
        </div>
            <div class="modal-body">
            {{ Form::open(['class'=>'form-horizontal','id'=>'search_form']) }}
            <div class="row">
                <div class="col-md-12">
                    <div class="row form-group">
                        <div class="col-md-3 control-label">
                            {{ Form::label('qaId', 'QA_ID') }}
                        </div>
                        <div class="col-md-8">
                            {{ Form::text('apiId', '' ,['class' => 'form-control apiId']) }}
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 control-label">
                            {{ Form::label('qaKeyword', __('admin.keyword')) }}
                        </div>
                        <div class="col-md-8">
                            {{ Form::text('keyword', '' ,['class' => 'form-control qaKeyword']) }}
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 control-label">
                            {{ Form::label('qaCategory', __('admin.header.category')) }}
                        </div>
                        <div class="col-md-8">
                            {{ Form::select('category_id', ['' => 'なし'] + $categories, '' ,['class' => 'form-control select2 qaCategory', 'data-width' => '100%',]) }}
                        </div>
                    </div>
                    <div class="row text-center">
                        <button type="button" class="btn btn-primary qa-seach"><i class="fa fa-search"> {{__('admin.search')}}</i></button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table-striped table-scroll">
                        <thead>
                            <tr>
                                {{-- <th class="text-center">QA_ID</th> --}}
                                <th class="text-center api_id_qa">{{__('admin.scenario.qa_id')}}</th>
                                <th class="text-center">{{__('admin.scenario.question_text')}}</th>
                                <th class="text-center">{{__('admin.scenario.answer_text')}}</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- dataAjax --}}
                        </tbody>
                    </table>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <div class="modal-footer-center modal-footer--mine">
            <button type="button" class="btn btn-default closeModalQa" data-dismiss="modal">{{__('admin.cancel')}}</button>
        </div>
    </div>
</div>
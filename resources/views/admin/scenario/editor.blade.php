@extends('layouts.admin')
@section('pageTitle', __('admin.header.シナリオ管理'))
@section('content')
@section('cssfiles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset(mix('css/drawflow.css')) }}" rel="stylesheet">
    <link href="{{ asset(mix('css/select2_replace.css')) }}" rel="stylesheet">
    <link href="{{ asset(mix('css/upload-filestyle.css')) }}" rel="stylesheet">
@stop
<div class="container">
    <!-- The Modal Loading -->
    <div id="loadingScenarioModal" class="modal none">
        <p>{{__(config('bot.const.bot_dialog_voice_api_loading'))}}</p>
        <img src="{{asset('img/images/loading.gif')}}" alt="loading" width="150" height="80">
    </div>
    <div id="dropdown_select2"></div>
    <div id="wrapper" class="row">
        <div class="col-md-12">
            <h3>@yield('pageTitle')</h3>
            <div class="row">
                <div class="col-md-12" id="drawflow-editor">
                    <div id="mySelect2"></div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="wrapper editor-body">
                                        <div class="col-md-2 col-left-btn" style="padding: 0">
                                            <div class="click-drawflow" draggable="false" data-node="category">
                                                <button type="button" class="btn btn-blue" data-toggle="modal" data-target="#scenarioAdd" title=" シナリオ追加" data-keyboard="false" data-backdrop="static"> シナリオ追加</button>
                                                <button type="button" class="btn btn-pink edit-btn-scenario  edit-scenario" title="シナリオ編集">  シナリオ編集 </button>
                                                <button type="button" class="btn btn-gr add-qa" title="QAデータ追加"> QAデータ追加 </button>
                                                <button type="button" class="btn btn-org edit-btn-scenario copy-node" data-toggle="modal" data-target="" title="Ctrl + C ➝ Ctrl + V"> 選択ノード複製</button>
                                                <button type="button" class="btn btn-yl edit-btn-scenario delete-node" data-toggle="modal" data-target="" title="Delete キー"> 選択ノード削除</button>
                                            </div>
                                            <div class="btn-col">
                                                <button type="button" class="btn btn-pri status-scenario save-scenario" data-toggle="modal" data-target="" title="Ctrl + S"> すべてのノードを保存 </button>
                                                <button type="button" class="btn btn-red delete-all-scenario" data-toggle="modal" data-target="" title="すべてのノードを削除"> すべてのノードを削除</button>
                                            </div>
                                            <div class="btn-col-import">
                                                <button type="button" class="btn btn-iexport import-export-scenario" data-toggle="modal" data-target="#scenarioImportExport" title="インポート/エクスポート">インポート/エクスポート</button>
                                            </div>
                                            <div class="search-col">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li role="presentation" class="active tabscenario"><a href="#scenario" aria-controls="scenario" role="tab" data-toggle="tab">シナリオ</a></li>
                                                    <li role="presentation" style="float:right" class="tabqa"><a href="#QA" aria-controls="QA" role="tab" data-toggle="tab">QAデータ</a></li>
                                                </ul>
                                                <div class="tab-content" style="padding: 10px 0 0 0">
                                                    <div role="tabpanel" class="tab-pane active" id="scenario">
                                                        <div class="input-icons">
                                                            <i class="fa fa-search icon"></i>
                                                        <input class="input-field search search-scenario" type="text">
                                                        </div>
                                                        <div class="content-data content-scenario">
                                                            {{-- data content scenario --}}
                                                        </div>
                                                    </div>
                                                    <div role="tabpanel" class="tab-pane" id="QA">
                                                        <div class="input-icons">
                                                            <i class="fa fa-search icon"></i>
                                                            <input class="input-field search search-QA" type="text" name="search">
                                                        </div>
                                                        <div class="content-data content-qa">
                                                            {{-- data content qa --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lds-roller">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                        <div class="col-md-10 col-right drawflow-area">
                                            <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
                                            </div>
                                            <div class="btn-select-lock">
                                                <div class="col-md-12">
                                                    {{ Form::select('fillterCategory', ['' => 'カテゴリなし'] + $categories, '' ,['class' => 'form-control select2 fillterCategory fillterCategoryChange', 'data-width' => '100%']) }}
                                                </div>
                                            </div>
                                            <div class="bar-zoom bar-zoom-in" style="align-items: center" onclick="editor.zoom_out()">
                                                <i class="fa fa-minus fa-xs"></i>
                                            </div>
                                            <div class="bar-zoom bar-zoom-out" style="align-items: center" onclick="editor.zoom_in()">
                                                <i class="fa fa-plus fa-xs"></i>
                                            </div>
                                            <div class="bar-zoom bar-zoom-fill" style="align-items: center">
                                                <i class="fa fa-expand fa-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="qaModal" class="editor-modal modal fade">
                        @include('layouts.parts.modal_qa_add')
                    </div>
                    <div id="scenarioAdd" class="editor-modal modal fade">
                        @include('layouts.parts.modal_scenario_add')
                    </div>
                    <div id="scenarioImportExport" class="editor-modal modal fade" data-backdrop="static">
                        @include('layouts.parts.modal_scenario_iexport')
                    </div>
                    <div id="scenarioExportConfirm" class="editor-modal modal fade" data-backdrop="static">
                        @include('layouts.parts.model_scenario_export_confirm')
                    </div>
                    <div id="scenarioEdit" class="editor-modal modal fade">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                                        <h4 class="modal-title"></h4>
                                    </div>
                                    <div class="modal-body message_body" style="margin-right: 15px;">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger delete-scenario-all" data-dismiss="modal">削除</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="detailSenarioQA" class="editor-modal modal fade">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                                        <h4 class="modal-title"></h4>
                                    </div>
                                    <div class="modal-body message_body" style="margin-right: 15px;">
                                    </div>
                                    <div class="modal-footer" style="text-align: center">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="err" class="editor-modal modal fade"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jsfiles')
    <script src="{{ asset(mix('js/drawflow.js')) }}"></script>
    <script src="{{ asset(mix('js/iexport-custom.js')) }}"></script>
    <script type="text/javascript">
        let urlScenarioDelete = "{!! route('admin.scenario.delete')!!}";
        var urlSenario = "{!! route('admin.scenario.store') !!}";
        var urlQaSearch = "{!! route('admin.scenario.getDataQa') !!}";
        var urlScenarioFillter = "{!! route('admin.scenario.fillter') !!}";
        var editScenario = "{!! route('admin.scenario.editor.edit') !!}";
        var detailScenario = "{!! route('admin.scenario.editor.detail') !!}";
        var detailQA = "{!! route('admin.scenario.editor.learningDetail') !!}";
        var storeScenario = "{!! route('admin.scenario.connection.store') !!}";
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var temp = 0;
        var arr_insert_relation = new Array(),
            actionHistory, actionCurrent = 0;;
        var arr_delete_connection = new Array();
        var arr_id_learning = new Array();
        var id = document.getElementById("drawflow");
        var editor = new window.Drawflow(id);
        var dataQa = new Array();
        var dataScenario = new Array();
        var index_remove = '';
        {!! "var all_keywords = ". json_encode($all_keywords) . "" !!};
        editor.start();
        var keywords = {};
        var modifiedData = {};
        var oldParams = '';
        var dataSame = {};
         // let scenario_id = 0;
        var err_msg = "{{ ($errors->has('error_message')) ? $errors->first('error_message') : '' }}";
        if (err_msg != '') {
            alert(err_msg);
        }
        var iexport = {};
        iexport.download_zip = "{!! route('admin.scenario.download.zip') !!}";
        iexport.ajax_save_zip = "{!! route('api.admin.scenario.save.zip') !!}";
        iexport.download_excel = "{!! route('admin.scenario.export.file') !!}";
        iexport.ajax_save_excel = "{!! route('api.admin.scenario.save.file') !!}";
        iexport.ajax_import_zip = "{!! route('api.admin.scenario.import.zip') !!}";
        iexport.post_max_size = "{{ config('scenario.import_export.post_max_size') }}";
    </script>
    <script src="{{ asset(mix('js/drawflow-custom.js')) }}"></script>
    <script src="{{ asset(mix('js/select2_replace.js')) }}"></script>
@stop
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use App\Services\Admin\LearningService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\ScenarioService;
use App\Http\Requests\Admin\ScenarioRequest;
use App\Exports\Admin\ScenarioExport;
use Response;

/**
 * シナリオデータ管理コントローラ
 * Class ScenarioController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ScenarioController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.scenario';
    /**
     * @var ScenarioService
     */
    private $service;
    /**
     * @var LearningService
     */
    
    private $learning_service;

    /**
     * @var \App\Services\Bot\BotDbService
     */
    private $db_service;

    /**
     * ScenarioController constructor.
     * @param ScenarioService $service
     * @param LearningService $learning_service
     */
    public function __construct(ScenarioService $service, LearningService $learning_service)
    {
        ini_set('max_input_time', 0);
        ini_set('max_execution_time', 0);
        $this->service = $service;
        $this->learning_service = $learning_service;
        $this->db_service = $service->getBotDbService();
        $this->autoSetPermission('scenario');
    }

    /**
     * キーワードチョイス(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function keywordChoice(Request $request)
    {
        $keyword_data = $this->db_service->getScenarioKeywordRepository()->getChoice('keyword_id', 'keyword', false);
        return response(['data' => $keyword_data]);
    }

    /**
     * シナリオ登録処理
     * @param ScenarioRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(ScenarioRequest $request)
    {
        if ($request->ajax()) {
            parse_str($request->get('data'), $params);
            if ($params['name']) {
                $params['name'] = trim($params['name']," ");
            }
            if (isset($params['multi_data'])) {
                foreach ($params['multi_data'] as $idx => $row) {
                    if (gettype($row) === "string") {
                        $params['multi_data'][$idx] = [$row];
                    }
                }
            }
            return response()->json($params);
        }
    }
    
    /**
     * シナリオ詳細
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editor(Request $request)
    {
        // Get category
        $categories = $this->db_service->getCategoryRepository()->getChoice('id', 'name');
        // Get keyword
        $keywords = old('multi_data') ?? [];
        // Get all keyword
        $all_keywords = $this->service->getAllKeyword();
        // Set data modal
        $data_modal = ['name' => '', 'order' => 0];
        return view(self::ROUTE_NAME . '.editor')->with($this->getRedirectConfirm($request))
            ->with('categories', $categories)->with('keywords', $keywords)->with('data', $data_modal)->with('all_keywords', $all_keywords);
    }

    /**
     * list scenario, learning & caculate position node
     * @param Request $request
     * @return json
     */
    public function scenarioListAjax(Request $request)
    {
        $category_id = ($request->params) ? $request->params : '' ;
        // Get scenario data
        $scenario_datas = $this->service->getRepository()->joinKeyword()->joinLearning()->setParams(['category_id' => $category_id])->filterByParams()->getData();
        // Get scenario id
        $scenario_id_ary = array();
        foreach ($scenario_datas as $scenario) {
            $scenario_id_ary[] = object_get($scenario, 'scenario_id');
        }
        // Get learning data
        $answer_datas = $this->db_service->getLearningRepository()->joinScenario($scenario_id_ary, $request->params, null)->getData();
        // Get learning data copy
        $answer_data_copy = $this->db_service->getLearningRepository()->joinScenario($scenario_id_ary, $request->params, 'node')->getData();
        $data_common_qa = array_merge($answer_datas, $answer_data_copy);
        // Sort qa and qa copy
        $qa_common_sort = collect($data_common_qa)->sortBy('order');
        // Get data qa copy
        $qa_copy_position = $this->service->getDataCopyAndPosition($scenario_datas, $qa_common_sort);
        return response()->json([
            'scenario' => $scenario_datas,
            'answer' => $answer_datas,
            'position' => $qa_copy_position['arr_position'],
            'answerCopy' => $qa_copy_position['arr_qa_copy'],
        ]);
    }
    
    /**
     * get all data learning by category
     * search data learning by $request
     * @param Request $request
     * @return json
     */
    public function getDataQa(Request $request)
    {
        $params['apiId'] = $request->get('apiId');
        $params['keyword'] = $request->get('keyword');
        $params['category_id'] = $request->get('category_id');
        $params['type'] = $request->has('type') ? $request->get('type'): 'search';
        $repository_qa = $this->learning_service->getRepository();
        $datatable_qa = $repository_qa->findBy($params);
        return response()->json([
            'datatable_qa' => $datatable_qa,
            'token' => $request->session()->token()
        ]);
    }

    /**
     * Delete scenario or learning 
     * @param $request
     * @return json
     */
    public function deleteNode(Request $request)
    {
        $arr_id = $request->get('id');
        if ($arr_id) {
            $delete_scenario = $this->service->deleteNode($arr_id, self::ROUTE_NAME);
            if (!$delete_scenario) {
                return response()->json(false);
            }
        }
        return response()->json(true);
    }
    /**
     * Save connection scenario 
     * @param $request
     * @return json
     */
    public function connectionStore(Request $request)
    {
        $category_id = ($request->has('category_id') AND $request->get('category_id') != NULL) ? $request->get('category_id') : '';
        $id_scenario = $request->has('id_scenario') ? $request->get('id_scenario') : '';
        if (!empty($id_scenario)) {
            $id_scenario = explode('@@add_item_node@@', $id_scenario);
        }
        $relate_scenario = $request->has('relate_scenario') ? $request->get('relate_scenario') : '';
        if (!empty($relate_scenario)) {
            $relate_scenario = explode('@@add_item_relation@@', $relate_scenario);
        }
        $qa_position = $request->has('arr_qa_position') ? $request->get('arr_qa_position') : [];
        if (!empty($qa_position)) {
            $qa_position = explode('@@add_item_qa@@', $qa_position);
            foreach ($qa_position as $key => $value) {
                $qa_position[$key] = json_decode($value, true);
            }
        }
        $scenario_datas = $this->service->getRepository()->setParams(['category_id' => $category_id])->filterByParams()->getData();
        $save_scenario = $this->service->saveScenarioAndRelation($category_id, $id_scenario, $relate_scenario, $scenario_datas, $qa_position, self::ROUTE_NAME);
        if (!$save_scenario) {
            return response()->json(false);
        }
        return response()->json(true);
    }

    /**
     * Edit Scenario
     * @param $request
     * @return view
     */
    public function editScenario(Request $request) {
        // Get data scenario from client
        $data = json_decode($request->get('data'), true);
        $categories = $this->db_service->getCategoryRepository()->getChoice('id', 'name');
        $request->request->add($data);
        $keywords = $data['multi_data'] ?? NULL;
        if ($data['name']) {
            $data['name'] = trim($data['name'], " ");
        }
        if ($keywords) {
            foreach ($keywords as $key => $value) {
                if (($value['keyword_id'] ?? NULL)) {
                    $data_keyword = $this->db_service->getScenarioKeywordRepository()->getOneById(($value['keyword_id'] ?? NULL));
                    $keywords[$key]['keyword'] = $data_keyword['keyword'];
                } else if (($value['keyword'] ?? NULL)) {
                    $keywords[$key]['keyword_id'] = "";
                }
            }
        } else {
            $keywords = [];
        }
        // Get all keyword
        $all_keywords = $this->service->getAllKeyword();
        return view('layouts.parts.modal_scenario_edit', compact('categories', 'data', 'keywords', 'all_keywords'));
    }

    /**
     * Detail Scenario
     * @param $request
     * @return view
     */
    public function detailScenario(Request $request) {
        // Get data scenario from client
        $data = json_decode($request->get('data'), true);
        $categories = $this->db_service->getCategoryRepository()->getOneById($data['category_id']);
        $request->request->add($data);
        $keywords = $data['multi_data'] ?? [];
        if ($data['name']) {
            $data['name'] = trim($data['name'], " ");
        }
        if ($keywords) {
            foreach ($keywords as $key => $value) {
                if (($value['keyword'] ?? NULL)) {
                    $keywords[$key]['keyword_id'] = "";
                }
            }
        }
        return view('layouts.parts.modal_scenario_detail', compact('categories', 'keywords'));
    }

    /**
     * Detail Qa
     * @param $request
     * @return view
     */
    public function detailLearning(Request $request) {
        // Get data scenario from client
        $data = json_decode($request->get('data'), true);
        $categories = $this->db_service->getCategoryRepository()->getOneById($data['category_id']);
        $request->request->add($data);
        $key_phrases = $data['key_phrase'] ? explode(',', $data['key_phrase']) : NULL;
        if ($data['question']) {
            $data['question'] = trim($data['question'], " ");
        }
        if ($data['answer']) {
            $data['answer'] = trim($data['answer'], " ");
        }
        if ($data['metadata']) {
            $data['metadata'] = trim($data['metadata'], " ");
        }
        return view('layouts.parts.modal_qa_detail', compact('categories', 'key_phrases'));
    }
    
    /**
     * Backup data scenario to file json compress to zip
     * @param Request $request
     * @return @json : contain path file
     */
    public function saveFileZip(Request $request)
    {
        $data_scenario = array();
        // Get table scenario
        $data_scenario['tbl_scenario'] = $this->db_service->getScenarioRepository()->getAll();
        // Get table tbl_scenario_relation
        $data_scenario['tbl_scenario_relation'] = $this->db_service->getScenarioRelationRepository()->getAll();
        // Get table tbl_scenario_keyword
        $data_scenario['tbl_scenario_keyword'] = $this->db_service->getScenarioKeywordRepository()->getAll();
        // Get table tbl_scenario_keyword_relation
        $data_scenario['tbl_scenario_keyword_relation'] = $this->db_service->getScenarioKeywordRelationRepository()->getAll();
        // Get table tbl_scenario_learning_relation
        $data_scenario['tbl_scenario_learning_relation'] = $this->db_service->getScenarioLearningRelationRepository()->getAll();
        // Save Zip file
        $save_file_zip = $this->service->saveZipFileJson($data_scenario, self::ROUTE_NAME);
        if ($save_file_zip) {
            $export_data = [
                'status' => true,
                'file' => $save_file_zip,
                '_token' => $request->session()->token()
            ];
            return response()->json($export_data);
        }
        $export_data = [
            'status' => false,
            'message' => config('scenario.import_export.message.scenario_download_zip_fail'),
        ];
        return response()->json($export_data);
    }
    
     /**
     * Download file backup data 
     * @param Request $request
     * @return mix
     */
    public function downloadZip(Request $request)
    {
        $save_file_zip = $request->get('export-zip');
        if (is_file($save_file_zip)) {
            return Response::download($save_file_zip)->deleteFileAfterSend(false);
        }
        return redirect()->route(self::ROUTE_NAME . '.editor')->withErrors(['error_message'  => config('scenario.import_export.message.scenario_download_zip_fail')]);
    }

    /**
     * Import file backup data 
     * @param Request $request
     * @return @json
     */
    public function importZip(Request $request)
    {
        if ($this->isStore($request)) {
            $key_path = $request->session()->get($this->getZipSessionKey());
            $import_file = $this->service->import($request, $key_path);
            if ($import_file) {
                return response()->json(['status' => 'success']);
            }
            return response()->json([
                'status' => 'error',
                'message' => config('scenario.import_export.message.scenario_upload_error')
            ]);
        } else {
            if (!$request->file('zip')) {
                return response()->json(['status' => 'error', 'message' => config('scenario.import_export.message.scenario_file_empty')]);
            }
            $dir_name_import = self::ROUTE_NAME . '/' . config('scenario.import_export.dir_import_name');
            $this->clearZipFile($request, $dir_name_import);
            $result = $this->confirmFileZip($request, $dir_name_import);
            if ($result['errors']) {
                return response()->json(['status' => 'error', 'message' =>  $result['errors']]);
            }
            return response()->json(['status' => 'success', 'confirm' =>  1, '_token' => $request->session()->token()]);
        }
    }

    /**
     * Process get data scenario save to file
     * @param Request $request
     * @return @json
     */
    public function getScenarioFileExport(Request $request)
    {
        $category_data = $this->service->getBotDbService()->getCategoryRepository()->getData();
        $categories_ary = array('' => 'なし');
        foreach ($category_data as $category) {
            $categories_ary[$category->id] =  $category->name;
        }
        $scenario_ary_cate = array();
        $scenario_ary_group = array();
        foreach ($categories_ary as $category_id => $cate_name) {
            $this->service->getRepository()->clearQuery();
            // Get scenario data
            $scenario_datas = $this->service->getRepository()->joinKeyword()->joinLearning()->setParams(['category_id' => $category_id])->filterByParams()->getData();
            if (!$scenario_datas) {
                $scenario_ary_cate[$cate_name] = array();
                $scenario_ary_group[$cate_name] = array();
                continue;
            }
            // Get scenario id
            $scenario_ary = array();
            foreach ($scenario_datas as $scenario) {
                $scenario_ary[object_get($scenario, 'scenario_id')] = [
                    'name' => object_get($scenario, 'name'),
                    'cate' => object_get($scenario, 'category_id'),
                    'parent' => object_get($scenario, 'parent_scenario_id')
                ];
            }
            $scenario_id_ary = array_keys($scenario_ary);
            // Get learning data
            $answer_datas = $this->db_service->getLearningRepository()->joinScenario($scenario_id_ary, $request->params, null)->getData();
            // Get learning data copy
            $answer_data_copy = $this->db_service->getLearningRepository()->joinScenario($scenario_id_ary, $request->params, 'node', $scenario_id_ary)->getData();
            $data_common_qa = array_merge($answer_datas, $answer_data_copy);
            // Sort qa and qa copy
            $qa_common_sort = collect($data_common_qa)->sortBy('order');
            $qa_ary = array();
            foreach ($qa_common_sort as $qa) {
                $qa_ary[$qa->id] = [
                    'app_id' => $qa->api_id,
                    'question' => $qa->question,
                    'answer' => $qa->answer,
                    'senario_id' => $qa->scenario_id
                ];
            }
            // Get data position
            $qa_copy_position = $this->service->getDataCopyAndPosition($scenario_datas, $qa_common_sort);
            $group_node = $this->service->groupNodeItem($qa_copy_position['arr_position'], $scenario_ary, $qa_common_sort);
            $scenario_ary_group[$cate_name] = $group_node;
            $scenario_ary_cate[$cate_name] = [
                'scenario_data' => $scenario_ary,
                'qa_data' => $qa_ary
            ];
        }
        return response()->json([
            'status' => 'success',
            'scenario_ary_group' =>  $scenario_ary_group,
            'scenario_ary_cate' =>  $scenario_ary_cate,
            '_token' => $request->session()->token()
        ]);
    }

    /**
     * Export file excel 
     * @param Request $request
     * @return mix
     */
    public function exportFile(Request $request)
    {
        $scenario_ary_group = json_decode($request->get('scenario_ary_group'), true);
        $scenario_ary_cate = json_decode($request->get('scenario_ary_cate'), true);
        if ($scenario_ary_group) {
            $export = new ScenarioExport($scenario_ary_group,  $scenario_ary_cate);
            $path_file = config('scenario.import_export.name_file');
            $conf_date = config('scenario.import_export.date_format');
            //エクスポートログ
            return $export->download(str_replace($conf_date, date('YmdHis'), $path_file));
        }
        return redirect()->route(self::ROUTE_NAME . '.editor');
    }
}
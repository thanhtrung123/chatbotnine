<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ToolsService;
use App\Services\File\CsvService;
use Illuminate\Http\Request;
use Artisan;
use Util;

/**
 * 裏ツール用コントローラー
 * FIXME: 肥大化しているが、サービスに処理を移すべきか…（裏ツールだし問題ない？）
 * Class ToolsController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ToolsController extends Controller
{
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.tools';
    /**
     * @var ToolsService
     */
    private $service;

    /**
     * ToolsController constructor.
     * @param ToolsService $service
     */
    public function __construct(ToolsService $service)
    {
        $this->service = $service;
    }

    /**
     * 裏ツールインデックス
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index');
    }

    /**
     * API情報
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function api()
    {
        $version = $this->service->getApiService()->getApi()->getVersion();
        $api_data = $this->service->getApiService()->getApi()->getLearningData();
        return view(self::ROUTE_NAME . '.api')->with('api_data', $api_data)->with('version', $version);
    }

    /**
     * ストップワード設定
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function stopWord(Request $request)
    {
        $set_word = null;
        $word = $request->get('word');
        $repository_stop_word = $this->service->getTruthService()->getDbService()->getRepositoryStopWords();
        if ($request->getMethod() == 'POST') {
            $success = $this->service->getTruthService()->getDbService()->saveStopWord($word);
            $set_word = ['word' => $word, 'success' => $success];
        } else if ($request->get('clear') == '1') {
            if (empty($word)) {
                $repository_stop_word->deleteByQuery();
            } else {
                $repository_stop_word->setParams(['word' => $word])->filterByParams()->deleteByQuery();
            }
        }
        $sw_data = $repository_stop_word->getDbResult()->getGenerator();
        return view(self::ROUTE_NAME . '.stop_word')->with('sw_data', $sw_data)->with('set_word', $set_word);
    }

    /**
     * 真理表（操作）
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function truthAction()
    {
        if (function_exists('debugbar')) {
            debugbar()->disable();
        }
        $api_ids = [];
        $ld_gen = $this->service->getLearningService()->getRepository()->getDbResult()->getGenerator();
        foreach ($ld_gen as $row) {
            $api_ids[] = $row['api_id'];
        }
        return view(self::ROUTE_NAME . '.truth_action')->with([
            'api_ids' => $api_ids,
        ]);
    }

    /**
     * 真理表（QAデータリアルタイム変換）
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function truth(Request $request)
    {
        if (function_exists('debugbar')) {
            debugbar()->disable();
        }
        $qna_morph_show = $request->get('qna', false);
        $tt_data = [];
        $ld_gen = $this->service->getLearningService()->getRepository()->getDbResult()->getGenerator();
        $truth_morph_service = $this->service->getTruthService()->getMorphService();
        foreach ($ld_gen as $row) {
            if ($qna_morph_show) {
                $truth = $this->service->getMorphService()->setMessage($row['question'])->execMorph()->getMessage();
            } else {
                $truth = $truth_morph_service->setMessage($row['question'])->execMorph()->getMessage();
            }
            $tt_data[] = [
                'api_id' => $row['api_id'],
                'question' => $row['question'],
                'truth' => $truth,
            ];
        }
        return view(self::ROUTE_NAME . '.truth')->with([
            'tt_data' => $tt_data,
        ]);
    }

    /**
     * 真理表（DB）
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function truthDb(Request $request)
    {
        if (function_exists('debugbar')) {
            debugbar()->disable();
        }
        $tt_data = [];
        $tt_gen = $this->service->getTruthService()->getDbService()->getRepositoryTruth()->filterByParams()->getDbResult()->getGenerator();
        $mode = $request->get('mode', '1');
        switch ($mode) {
            case '1'://全体
                foreach ($tt_gen as $row) {
                    $tt_data[] = $row;
                }
                break;
            case '2'://ワード
                $word_ary = $api_ary = [];
                foreach ($tt_gen as $row) {
                    $word_ary[$row['word']][] = $row;
                    $api_ary[$row['api_id']] = 1;
                }
                $api_max = count($api_ary);
                foreach ($word_ary as $word => $word_row) {
                    $cnt = count($word_row);
                    $tt_data[] = [
                        'word' => $word,
                        'cnt' => $cnt,
                        'rate' => round(($cnt / $api_max) * 100, 2) . '%',
                        'api_ids' => implode(",", array_column($word_row, 'api_id')),
                    ];
                }
                break;
            case '3'://API
                $api_ary = [];
                foreach ($tt_gen as $row) {
                    $api_ary[$row['api_id']][] = $row;
                }
                foreach ($api_ary as $api_id => $api_row) {
                    $tt_data[] = [
                        'api_id' => $api_id,
                        'cnt' => count($api_row),
                        'words' => implode(" ", array_column($api_row, 'word')),
                    ];
                }
                break;
        }
        return view(self::ROUTE_NAME . '.truth_db')->with([
            'tt_data' => $tt_data,
            'mode' => $mode,
        ]);
    }

    /**
     * 真理表同期（Ajax）
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function truthSync(Request $request)
    {
        $api_id = $request->get('api_id');
        if (empty($api_id)) throw new \Exception('require api_id');
        $ls = $this->service->getLearningService();
        $data = $ls->getRepository()->setParams(['api_id' => $api_id])->filterByParams()->getDbResult()->getOne();
        if ($data['auto_key_phrase_disabled'] == config('const.common.disabled.no.id'))
            $ls->saveTruthTable($api_id, $data['question']);
        return response([]);
    }

    /**
     * 真理表用　形態素解析　確認
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function truthMorph(Request $request)
    {
        $qna = $request->get('qna', false);
        $truth = '';
        if ($request->getMethod() == 'POST') {
            if ($qna) {
                $truth = $this->service->getMorphService()->setMessage($request->get('question'))->execMorph()->getMessage();
            } else {
                $truth = $this->service->getTruthService()->getMorphService()->setMessage($request->get('question'))->execMorph()->getMessage();
            }
        }
        return view(self::ROUTE_NAME . '.truth_morph')->with(['truth' => $truth, 'qna' => $qna]);
    }

    /**
     * シナリオ
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function scenario(Request $request)
    {
        $categories = [0 => 'カテゴリに紐づかないシナリオ'] + $this->service->getScenarioService()->getCategories();
        $scenarios = [];
        foreach ($categories as $key => $val) {
            $scenario = $this->service->getScenarioService()->getScenarioRepository()->setParams([
                'category_id' => $key
            ])->filterByParams()->getDbResult()->getPlainArray();
            foreach ($scenario as $idx => $row) {
                $scenario_relation = $this->service->getScenarioService()->getScenarioRelationRepository()->setParams([
                    'scenario_id' => $row['id']
                ])->filterByParams()->getDbResult()->getPlainArray();
                $scenario[$idx]['parent_scenario_ids'] = array_column($scenario_relation, 'parent_scenario_id', 'id');
            }
            foreach ($scenario as $idx => $row) {
                $scenario_learning_relation = $this->service->getScenarioService()->getScenarioLearningRelationRepository()->setParams([
                    'scenario_id' => $row['id']
                ])->filterByParams()->setOrder(['order' => 'asc'])->getDbResult()->getPlainArray();
                $scenario[$idx]['api_ids'] = array_column($scenario_learning_relation, 'api_id', 'id');
            }
            $scenarios[$key] = $scenario;
        }
        return view(self::ROUTE_NAME . '.scenario')->with('categories', $categories)->with('scenarios', $scenarios);
    }

    /**
     * シナリオ登録処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function scenarioPost(Request $request)
    {
        $scenario_rep = $this->service->getScenarioService()->getScenarioRepository();
        $scenario_rel_rep = $this->service->getScenarioService()->getScenarioRelationRepository();
        $scenario_lea_rel_rep = $this->service->getScenarioService()->getScenarioLearningRelationRepository();
        $scenario_rep->truncate();
        $scenario_rel_rep->truncate();
        $scenario_lea_rel_rep->truncate();
        $params = http_parse_query($request->get('data'));
        $categories = [0 => ''] + $this->service->getScenarioService()->getCategories();
        //カテゴリでループ
        foreach ($categories as $key => $val) {
            $ckey = "s_{$key}_";
            if (!isset($params["{$ckey}id"])) continue;
            $id_ary = $params["{$ckey}id"];
            $name_ary = $params["{$ckey}name"];
            $pids_ary = $params["{$ckey}pids"];
            $aid_ary = $params["{$ckey}aid"];
            $odr_ary = $params["{$ckey}odr"];
            //シナリオでループ
            foreach ($id_ary as $idx => $id) {
                $scenario = [
                    'id' => $id,
                    'name' => $name_ary[$idx],
                    'order' => $odr_ary[$idx],
                    'category_id' => ($key === 0) ? null : $key,
                ];
                $scenario_rep->create($scenario);
                //親IDでループ
                foreach (explode(',', $pids_ary[$idx]) as $pi => $pid) {
                    $scenario_rel = [
                        'scenario_id' => $id,
                        'parent_scenario_id' => empty($pid) ? null : $pid,
                    ];
                    $scenario_rel_rep->create($scenario_rel);
                }
                //api_idがある場合、ループ
                if (empty($aid_ary[$idx])) continue;
                foreach (explode(',', $aid_ary[$idx]) as $ai => $aid) {
                    $scenario_lea_rel = [
                        'scenario_id' => $id,
                        'api_id' => empty($aid) ? null : $aid,
                        'order' => $ai + 1,
                    ];
                    $scenario_lea_rel_rep->create($scenario_lea_rel);
                }
            }
        }
        return redirect()->route(self::ROUTE_NAME . '.scenario');
    }

    /**
     * 関連する回答
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function relatedAnswer(Request $request)
    {
        $learning_relation_gen = $this->service->getDbService()->getLearningRelationRepository()
            ->setOrder(['api_id' => 'asc', 'order' => 'asc'])->getDbResult()->getGenerator();
        return view(self::ROUTE_NAME . '.related_answer')->with('learning_relation', $learning_relation_gen);
    }

    /**
     * 関連する回答登録
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function relatedAnswerPost(Request $request)
    {
        $learning_rel_rep = $this->service->getDbService()->getLearningRelationRepository();
        $params = http_parse_query($request->get('data'));
        $learning_rel_rep->truncate();
        $ckey = "s_1_";
        $id_ary = $params["{$ckey}id"];
        $name_ary = $params["{$ckey}name"];
        $aid_ary = $params["{$ckey}aid"];
        $raid_ary = $params["{$ckey}raid"];
        $odr_ary = $params["{$ckey}odr"];
        foreach ($id_ary as $idx => $id) {
            $learning_relation = [
                'id' => $id,
                'name' => $name_ary[$idx],
                'order' => $odr_ary[$idx],
                'api_id' => $aid_ary[$idx],
                'relation_api_id' => $raid_ary[$idx],
            ];
            $learning_rel_rep->create($learning_relation);
        }

        return redirect()->route(self::ROUTE_NAME . '.related_answer');
    }


    /**
     * 汎用
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function common(Request $request)
    {
        $mode = $request->get('mode');
        switch ($mode) {
            case 'cache_clear':
                Artisan::call('clear-compiled');
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                Artisan::call('debugbar:clear');
                return redirect()->route(self::ROUTE_NAME . '.common');
                break;
            case 'maintenance':
                Artisan::call('down');
                return redirect()->route(self::ROUTE_NAME . '.common');
                break;
        }
        return view(self::ROUTE_NAME . '.common');
    }

}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use App\Services\Admin\KeyPhraseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\LearningService;
use App\Services\Admin\ImageInformationService;
use App\Http\Requests\Admin\LearningRequest;
use App\Http\Requests\ExcelImportRequest;
use App\Http\Requests\ZipImportRequest;
use App\Imports\Admin\LearningImport;
use App\Exports\Admin\LearningExport;
use Constant;

/**
 * 学習データ管理コントローラ
 * Class LearningController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LearningController extends Controller
{
    use FormTrait;

    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.learning';

    /**
     * @var LearningService
     */
    private $service;

    /**
     * @var KeyPhraseService
     */
    private $key_phrase_service;

    /**
     * @var ImageInformationService
     */
    private $img_info_service;

    /**
     * LearningController constructor.
     * @param LearningService $service
     * @param KeyPhraseService $key_phrase_service
     * @param ImageInformationService $img_info_service
     */
    public function __construct(LearningService $service, KeyPhraseService $key_phrase_service, ImageInformationService $img_info_service)
    {
        $this->service = $service;
        $this->key_phrase_service = $key_phrase_service;
        $this->img_info_service = $img_info_service;
        $this->autoSetPermission('learning');
    }
    //API

    /**
     * 学習データ一覧(API)
     * @param Request $request
     * @return type
     */
    public function learningList(Request $request)
    {
        $repository = $this->service->getRepository();
        if (config('bot.truth.enabled')) {
            $repository->filterAddKeyPhrase();
        }
        $datatable = $repository->setParams($request->all())->filterByParams()->datatable();
        $datatable->getConverter()
            ->setConvert(['question', 'answer', 'question_morph', 'key_phrase'], function ($val) {
                return str_omit_tooltip($val, config('bot.common.str_omit_length'));
            });
        return response($datatable->toArray());
    }
    //WEB

    /**
     * 学習データ一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $category_data = $this->service->getCategoryService()->getRepository()->getChoice('id', 'name');
        // get count data learning
        $count_learning = $this->service->getRepository()->countDataLearning();
        return view(self::ROUTE_NAME . '.index')
            ->with('category_data', $category_data)
            ->with('count_learning', $count_learning);
    }

    /**
     * 新規学習データ
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $category_data = $this->service->getCategoryService()->getRepository()->getChoice('id', 'name');
        // Get toolbar options
        $options_toolbar_enabled = $this->getConfigToolbar();
        return view(self::ROUTE_NAME . '.create')->with($this->getRedirectConfirm($request))
            ->with('category_data', $category_data)
            ->with('options_toolbar_enabled', $options_toolbar_enabled);
    }

    /**
     * 学習データ登録処理
     * @param LearningRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(LearningRequest $request)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $learning_data = $request->all();
            $learning_data['answer'] = str_replace("\r\n",'', ($learning_data['answer'] ?? ''));
            $params = $this->service->createLearning($learning_data);
            $this->key_phrase_service->autoSetKeyPhrase($params['api_id']);
            //priority
            $this->service->getCalcPriorityService()->calcAllTruthPriority()->updateAllTruthPriority();
            //新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $redirect = redirect()->route(self::ROUTE_NAME . '.create');
            if ($this->validateKeyPhrase($request->all(), $redirect)) {
                $request->request->set('confirm', 1);
            }
            return $redirect->withInput($request->all());
        }
    }

    /**
     * 学習データ詳細
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $data = $this->service->getRepository()->getOneById($id);
        return view(self::ROUTE_NAME . '.show')->with('id', $id)
            ->with('data', $data)->with($this->getRedirectConfirm($request));
    }

    /**
     * 学習データ修正(API_ID版)
     * @param $api_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editFromApiId($api_id)
    {
        $data = $this->service->getRepository()->findOneBy(['api_id' => $api_id]);
        return redirect()->route(self::ROUTE_NAME . '.edit', ['learning' => $data['id']]);
    }

    /**
     * 学習データ修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $category_data = $this->service->getCategoryService()->getRepository()->getChoice('id', 'name');
        $learning = $this->service->getRepository()->getOneById($id);
        if (old('confirm') === null) {
            $request->request->add($learning);
        }
        $key_phrase_data = $this->key_phrase_service->getKeyPhraseFromApiId($learning['api_id']);
        $truth_data = old('truth_data') ?? $key_phrase_data;
        $request->request->add(['truth_data' => $truth_data]);
        // Get toolbar options
        $options_toolbar_enabled = $this->getConfigToolbar();
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request))
            ->with('key_phrases', $truth_data)->with('category_data', $category_data)
            ->with('options_toolbar_enabled', $options_toolbar_enabled);
    }

    /**
     * 学習データ更新処理
     * @param LearningRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(LearningRequest $request, $id)
    {
        $params = $request->all();
        $learning_data = $this->service->getRepository()->getOneById($id);
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $params['answer'] = str_replace("\r\n",'', ($params['answer'] ?? ''));
            $this->service->updateLearning($id, $params);
            if ($params['auto_key_phrase_disabled'] == '1') {
                //手動キーフレーズ設定
                $params = $this->convertTruthData($params);
                $params = $this->service->calcPriority($learning_data['api_id'], $params);
                $this->key_phrase_service->updateKeyPhraseLearning($learning_data['api_id'], $params);
            } else {
                //自動キーフレーズ設定
                $this->key_phrase_service->autoSetKeyPhrase($learning_data['api_id']);
            }
            //priority
            $this->service->getCalcPriorityService()->calcAllTruthPriority()->updateAllTruthPriority();
            //修正ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $redirect = redirect()->route(self::ROUTE_NAME . '.edit', ['learning' => $id]);
            $params = $this->convertTruthData($params);
            if ($this->validateKeyPhrase($params, $redirect)) {
                $request->request->set('confirm', 1);
                $params['confirm'] = 1;
                if ($params['auto_key_phrase_disabled'] == '1') {
                    $params = $this->service->calcPriority($learning_data['api_id'], $params);
                }
            }
            return $redirect->withInput($params);
        }
    }

    /**
     * 学習データ削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        $deleted = $this->service->deleteLearning($id);
        if ($deleted) {
            $this->service->getRepository()->clearQuery()->update(['synced_at' => NULL]);
        }
        //削除ログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_destroy.id'));
        return $this->complete($request);
    }

    /**
     * 学習データインポート画面
     * @param Request $request
     * @return type
     */
    public function import(Request $request)
    {
        $this->clearFile($request);
        $this->clearZip($request);
        return view(self::ROUTE_NAME . '.import')->with($this->getRedirectConfirm($request));
    }

    /**
     * 学習データインポート登録処理
     * @param ExcelImportRequest $request
     * @param ZipImportRequest $request_zip
     * @param LearningImport $import
     * @return type
     */
    public function importStore(ExcelImportRequest $request, ZipImportRequest $request_zip, LearningImport $import)
    {
        if (function_exists('debugbar')) {
            debugbar()->disable();
        }
        if ($this->isStore($request)) {
            if ($request->get('confirmZip')) {
                // Get folder zip 
                $path_dir = $request->session()->get($this->getZipSessionKey());
                $result = $this->img_info_service->import($request, $path_dir);
                if (!$result) {
                    return redirect()->back()->with('msg_err_empty', config('message.upload_file_fail'));
                }
            }
            if ($request->get('confirmFile')) {
                //Truncate
                $this->service->getRepository()->truncate();
                $this->service->getTruthService()->getDbService()->getRepositoryTruth()->truncate();
                //Import
                $this->importFile($request, $import);
                //priority
                $this->service->getCalcPriorityService()->calcAllTruthPriority()->updateAllTruthPriority();
                //インポートログ
            }
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_import.id'));
            return $this->complete($request);
        } else {
            if (!$request->file('excel') && !$request->file('zip')) {
                return redirect()->back()->with('msg_err_empty', config('message.empty_file_upload'));
            }
            $request->request->set('confirmFile', 0);
            $request->request->set('confirmZip', 0);
            if ($request->file('excel')) {
                $result = $this->confirmFile($request, $import, self::ROUTE_NAME);
                $request->request->set('confirmFile', 1);
            }
            if ($request->file('zip')) {
                $result = $this->confirmZip($request, self::ROUTE_NAME);
                $request->request->set('confirmZip', 1);
                $request->request->set('path_name', $result['path_name']);
                $request->request->set('image_list_intersect', $result['image_list_intersect']);
            }
            $redirect = redirect()->route(self::ROUTE_NAME . '.import');
            if ($result['hasError']) {
                $redirect->with($result['errors']);
            } else {
                $request->request->set('confirm', 1);
            }
            return $redirect->withInput($request->all());
        }
    }

    /**
     * 学習データエクスポート
     * @param LearningExport $export
     * @return type
     */
    public function export(LearningExport $export)
    {
        //エクスポートログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_export.id'));
        $ext = config('excel.exports.extensions');
        return $export->download(str_replace('.', '_', self::ROUTE_NAME) . '_' . date('YmdHis') . $ext)->deleteFileAfterSend(true);
    }

    /**
     * 同期
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function sync(Request $request)
    {
        $res = $this->service->syncLearning($request->get('mode'));
        return response($res);
    }

    /**
     * 完了メッセージ出力
     * @param type $request
     * @return type
     */
    private function complete($request)
    {
        return redirect()->route(self::ROUTE_NAME . '.index')->with($this->createFlushMessage([
            '情報' => __("学習データ") . "の{$this->getModeName($request)}が完了しました。"
        ]));
    }

    /**
     * 真理表用キーフレーズデータ変換（手動用）
     * IDがある場合、キーフレーズを追加。キーフレーズがある場合、IDを追加。
     * @param $params
     * @return mixed
     */
    private function convertTruthData($params)
    {
        if (empty($params['truth_data'])) return $params;
        foreach ($params['truth_data'] as $idx => $row) {
            if (isset($row['key_phrase_id'])) {
                //変更した場合（ID）
                $key_phrase_id = $row['key_phrase_id'];
            } else {
                //入力済
                $key_phrase_id = $this->key_phrase_service->getRepository()->findOnly($row['key_phrase'], true);
                if ($key_phrase_id === null && preg_match_all('/\((.+)\)$/', $row['key_phrase'], $match)) {
                    $key_phrase_id = $this->key_phrase_service->getRepository()->findOnly($match[1], true);
                }
            }
            $params['truth_data'][$idx]['key_phrase_id'] = $key_phrase_id;
            //新規ワードはスキップ
            if ($key_phrase_id === null) continue;
            //ワードを取得して設定
            $key_phrase_row = $this->key_phrase_service->getRepository()->findOneBy(['key_phrase_id' => $key_phrase_id]);
            $params['truth_data'][$idx]['key_phrase'] = $this->key_phrase_service->keyPhraseRowToDisplayWord($key_phrase_row);
        }
        return $params;
    }

    /**
     * 特殊バリデート
     * @param $params
     * @param $redirect
     * @return bool
     */
    private function validateKeyPhrase($params, $redirect)
    {
        if (!config('bot.truth.enabled')) return true;
        //手動追加時にストップワードが使われていないかチェック
        if (isset($params['auto_key_phrase_disabled']) && $params['auto_key_phrase_disabled'] == '1') {
            $errors = $key_phrases = $key_phrase_ids = [];
            foreach ($params['truth_data'] as $idx => $row) {
                //重複チェック用
                if (!empty($row['key_phrase'])) $key_phrases[$idx] = $row['key_phrase'];
                if (!empty($row['key_phrase_id'])) $key_phrase_ids[$idx] = $row['key_phrase_id'];
                //ストップワードチェック
                if (!$this->service->getTruthService()->getDbService()->isStopWord($row['key_phrase'])) continue;
                $errors["truth_data.{$idx}.key_phrase"] = __('ストップワードが指定されています。');
            }
            //重複チェック
            $key_phrase_duplicate = array_duplicate_extract($key_phrases);
            $key_phrase_id_duplicate = array_duplicate_extract($key_phrase_ids);
            $duplicate_idxs = array_unique(array_flatten(array_merge($key_phrase_duplicate, $key_phrase_id_duplicate)));
            foreach ($duplicate_idxs as $idx) {
                $errors["truth_data.{$idx}.key_phrase"] = __('キーフレーズが重複しています。');
            }
            //エラーがある場合
            if (!empty($errors)) {
                $redirect->withErrors($errors);
                return false;
            }
            return true;
        }
        //自動キーフレーズ時に質問文にキーフレーズが含まれるかチェック
        $words = $this->key_phrase_service->getTruthMorphWordCounts($params['question']);
        if (empty($words)) {
            $redirect->withErrors(['question' => __('キーフレーズ') . __('となるワードが存在しません。')]);
            return false;
        }
        return true;
    }

}
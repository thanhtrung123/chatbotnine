<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\KeyPhraseExport;
use App\Http\Controllers\Traits\FormTrait;
use App\Http\Requests\ExcelImportRequest;
use App\Imports\Admin\KeyPhraseImport;
use App\Services\Admin\KeyPhraseService;
use App\Services\Admin\LearningService;
use App\Services\Bot\Truth\TruthDbService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KeyPhraseRequest;
use Constant;
use Illuminate\Validation\Rule;

/**
 * キーフレーズデータ管理コントローラ
 * Class KeyPhraseController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class KeyPhraseController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.key_phrase';
    /**
     * @var KeyPhraseService
     */
    private $service;

    /**
     * KeyPhraseController constructor.
     * @param KeyPhraseService $service
     */
    public function __construct(KeyPhraseService $service)
    {
        $this->service = $service;
        $this->autoSetPermission('key_phrase');
    }

    //API

    /**
     * キーフレーズデータ一覧(API)
     * @param Request $request
     * @return type
     */
    public function keyPhraseList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())
            ->filterKeyPhraseList()->filterByParams()->datatable();
        $datatable->getConverter()->setConvert(['disabled'], function ($val) {
            return Constant::getConstName($val, 'common.disabled');
        })->setConvert(['type'], function ($val) {
            return Constant::getConstName($val, 'truth.key_phrase_type');
        });
        return response($datatable->toArray());
    }

    /**
     * キーフレーズチョイス(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function keyPhraseChoice(Request $request)
    {
        $key_phrase_gen = $this->service->getRepository()->filterChoice()->setParams(['without_delete' => true])->filterByParams()->getDbResult()->getGenerator();
        $key_phrase_ary = [];
        foreach ($key_phrase_gen as $row) {
            $key_phrase_ary[$row['key_phrase_id']] = $this->service->keyPhraseRowToDisplayWord($row);
        }
        return response(['data' => $key_phrase_ary]);
    }

    //WEB

    /**
     * キーフレーズ一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $statuses = Constant::getConstArray(__('const.common.disabled'));
        return view(self::ROUTE_NAME . '.index')->with('statuses', $statuses);
    }

    /**
     * 新規キーフレーズ
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view(self::ROUTE_NAME . '.create')
            ->with($this->getRedirectConfirm($request));
    }

    /**
     * キーフレーズ登録処理
     * @param KeyPhraseRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(KeyPhraseRequest $request)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $params = $request->all();
            $this->service->getRepository()->create($params + [
                    'type' => config('const.truth.key_phrase_type.user_add.id'),
                    'original_word' => $params['word'],
                    'key_phrase_id' => $this->service->getRepository()->getNextKeyPhraseId(),
                ]);
            //新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $redirect = redirect()->route(self::ROUTE_NAME . '.create');
            if ($this->validateStopWord($request, $redirect)) {
                $request->request->set('confirm', 1);
            }
            return $redirect->withInput($request->all());
        }
    }

    /**
     * キーフレーズ修正(キーフレーズID版)
     * @param $key_phrase_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editFromKeyPhraseId($key_phrase_id)
    {
        $data = $this->service->getRepository()->findOneBy(['key_phrase_id' => $key_phrase_id]);
        return redirect()->route(self::ROUTE_NAME . '.edit', ['key_phrase' => $data['id']]);
    }

    /**
     * キーフレーズ修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $key_phrase = $this->service->getRepository()->getOneById($id);
        //FIXME:リクエストで空白があるとDBの値が出てしまう。(応急的）
        if (old('confirm') == '1' && $request->get('replace_word') == null) {
            $key_phrase['replace_word'] = null;
        }
        $request->request->add($key_phrase);
        //学習データ取得
        $learning_gen = $this->service->getLearningDataFromKeyPhraseId($key_phrase['key_phrase_id']);
        $statuses = Constant::getConstArray(__('const.common.disabled'));
        if ($this->service->checkHasOneKeyPhrase($learning_gen)) {
            unset($statuses[config('const.common.disabled.yes.id')]);
        }
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request))
            ->with('learning_data', $learning_gen)
            ->with('statuses', $statuses);
    }

    /**
     * キーフレーズ更新処理
     * @param KeyPhraseRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(KeyPhraseRequest $request, $id)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->getRepository()->update($id, $request->all());
            //修正ログ
            $this->service->saveLog(__('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $redirect = redirect()->route(self::ROUTE_NAME . '.edit', ['key_phrase' => $id]);
            if ($this->validateStopWord($request, $redirect)) {
                $request->request->set('confirm', 1);
            }
            return $redirect->withInput($request->all());
        }
    }

    /**
     * キーフレーズ削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        //物理削除
        $can_delete = $this->service->deleteKeyPhrase($id);
        if ($can_delete) {
            //削除ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_destroy.id'));
            return $this->complete($request);
        } else {
            return redirect()->route(self::ROUTE_NAME . '.index', ['r' => 1])->with($this->createFlushError([
                __('削除できません') => __('選択されたキーフレーズを削除するとマッチングしなくなるデータが存在する為、削除することは出来ません。'),
            ]));
        }
    }

    /**
     * キーフレーズインポート
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(Request $request)
    {
        $this->clearFile($request);
        return view(self::ROUTE_NAME . '.import')->with($this->getRedirectConfirm($request));
    }

    /**
     * キーフレーズインポート処理
     * @param ExcelImportRequest $request
     * @param KeyPhraseImport $import
     * @return type|\Illuminate\Http\RedirectResponse
     */
    public function importStore(ExcelImportRequest $request, KeyPhraseImport $import)
    {
        if ($this->isStore($request)) {
            //truncate
            $this->service->getRepository()->truncate();
            //import
            $this->importFile($request, $import);
            //インポートログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_import.id'));
            return $this->complete($request);
        } else {
            if (!$request->file('excel')) {
                return redirect()->back()->with('msg_err_empty', config('message.empty_file_upload'));
            }
            $result = $this->confirmFile($request, $import, self::ROUTE_NAME);
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
     * キーフレーズエクスポート
     * @param KeyPhraseExport $export
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(KeyPhraseExport $export)
    {
        //エクスポートログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_export.id'));
        $ext = config('excel.exports.extensions');
        return $export->download(str_replace('.', '_', self::ROUTE_NAME) . '_' . date('YmdHis') . $ext)->deleteFileAfterSend(true);
    }

    /**
     * 完了メッセージ出力
     * @param type $request
     * @return type
     */
    private function complete($request)
    {
        return redirect()->route(self::ROUTE_NAME . '.index')->with($this->createFlushMessage([
            '情報' => "キーフレーズデータの{$this->getModeName($request)}が完了しました。"
        ]));
    }

    /**
     * @param $request
     * @param $redirect
     * @return bool
     */
    private function validateStopWord($request, $redirect)
    {
        if ($this->service->getTruthDbService()->isStopWord($request->get('word'))) {
            $redirect->withErrors(['word' => __('キーフレーズ') . ' ' . __('ストップワードが指定されています。')]);
            return false;
        }
        return true;
    }
}
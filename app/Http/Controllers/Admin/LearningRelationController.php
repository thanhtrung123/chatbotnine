<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\LearningRelationService;
use App\Http\Requests\Admin\LearningRelationRequest;
use App\Http\Requests\ExcelImportRequest;
use App\Imports\Admin\LearningRelationImport;
use App\Exports\Admin\LearningRelationExport;

/**
 * 関連回答管理コントローラ
 * Class LearningRelationController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LearningRelationController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.learning_relation';
    /**
     * @var LearningRelationService
     */
    private $service;
    /**
     * @var \App\Services\Bot\BotDbService
     */
    private $db_service;

    /**
     * LearningRelationController constructor.
     * @param LearningRelationService $service
     */
    public function __construct(LearningRelationService $service)
    {
        $this->service = $service;
        $this->db_service = $service->getBotDbService();
        $this->autoSetPermission('learning_relation');
    }

    // API

    /**
     * 関連回答データ一覧(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function learningRelationList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->datatable();
        return response($datatable->toArray());
    }

    //WEB

    /**
     * 関連回答一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index');
    }

    /**
     * 新規関連回答
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view(self::ROUTE_NAME . '.create')->with($this->getRedirectConfirm($request));
    }

    /**
     * 関連回答登録処理
     * @param LearningRelationRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(LearningRelationRequest $request)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $insert = $this->service->getRepository()->create($request->all());
            // 新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.create')->withInput($request->all());
        }
    }

    /**
     * 関連回答修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $learning_relation = $this->service->getRepository()->getOneById($id);
        $request->request->add($learning_relation);
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request));
    }

    /**
     * 関連回答更新処理
     * @param LearningRelationRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(LearningRelationRequest $request, $id)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            // 登録
            $this->service->getRepository()->update($id, $request->all());
            // 修正ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.edit', ['variant' => $id])->withInput($request->all());
        }
    }

    /**
     * 関連回答削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        // 削除
        $this->service->getRepository()->deleteOneById($id);
        //削除ログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_destroy.id'));
        return $this->complete($request);
    }

    /**
     * 関連回答インポート画面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(Request $request)
    {
        $this->clearFile($request);
        return view(self::ROUTE_NAME . '.import')->with($this->getRedirectConfirm($request));
    }

    /**
     * 関連回答インポート登録処理
     * @param ExcelImportRequest $request
     * @param LearningRelationImport $import
     * @return type|\Illuminate\Http\RedirectResponse
     */
    public function importStore(ExcelImportRequest $request, LearningRelationImport $import)
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
     * 関連回答エクスポート
     * @param LearningRelationExport $export
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(LearningRelationExport $export)
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
            '情報' => __('関連質問データ') . "の{$this->getModeName($request)}が完了しました。"
        ]));
    }
}
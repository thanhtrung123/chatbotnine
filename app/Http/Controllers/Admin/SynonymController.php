<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use App\Services\Admin\KeyPhraseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\SynonymService;
use App\Http\Requests\Admin\SynonymRequest;
use App\Http\Requests\ExcelImportRequest;
use App\Imports\Admin\SynonymImport;
use App\Exports\Admin\SynonymExport;

/**
 * 類義語データ管理コントローラ
 * Class SynonymController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SynonymController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.synonym';
    /**
     * @var SynonymService
     */
    private $service;
    /**
     * @var KeyPhraseService
     */
    private $key_phrase_service;

    /**
     * SynonymController constructor.
     * @param SynonymService $service
     * @param KeyPhraseService $key_phrase_service
     */
    public function __construct(SynonymService $service, KeyPhraseService $key_phrase_service)
    {
        $this->service = $service;
        $this->autoSetPermission('synonym');
        $this->key_phrase_service = $key_phrase_service;
    }

    //API

    /**
     * 類義語データ一覧(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function synonymList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->datatable();
        return response($datatable->toArray());
    }

    //WEB

    /**
     * 類義語一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index');
    }

    /**
     * 新規類義語
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view(self::ROUTE_NAME . '.create')->with($this->getRedirectConfirm($request));
    }

    /**
     * 類義語登録処理
     * @param SynonymRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(SynonymRequest $request)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $insert = $this->service->getRepository()->create($request->all());
            //同期
            $this->key_phrase_service->syncTruthByChangeRelationWord(['synonym_id' => $insert->id]);
            //新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.create')->withInput($request->all());
        }
    }

    /**
     * 類義語詳細
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
     * 類義語修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $synonym = $this->service->getRepository()->getOneById($id);
        $request->request->add($synonym);
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request));
    }

    /**
     * 類義語更新処理
     * @param SynonymRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(SynonymRequest $request, $id)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->getRepository()->update($id, $request->all());
            //同期
            $this->key_phrase_service->syncTruthByChangeRelationWord(['synonym_id' => $id]);
            //修正ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.edit', ['synonym' => $id])->withInput($request->all());
        }
    }

    /**
     * 類義語削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        $this->service->getRepository()->deleteOneById($id);
        //同期
        $this->key_phrase_service->syncTruthByChangeRelationWord(['synonym_id' => $id]);
        //削除ログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_destroy.id'));
        return $this->complete($request);
    }

    /**
     * 類義語インポート画面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(Request $request)
    {
        $this->clearFile($request);
        return view(self::ROUTE_NAME . '.import')->with($this->getRedirectConfirm($request));
    }

    /**
     * 類義語インポート登録処理
     * @param ExcelImportRequest $request
     * @param SynonymImport $import
     * @return type|\Illuminate\Http\RedirectResponse
     */
    public function importStore(ExcelImportRequest $request, SynonymImport $import)
    {
        if ($this->isStore($request)) {
            //truncate
            $this->service->getRepository()->truncate();
            //import
            $this->importFile($request, $import);
            //同期
            $this->key_phrase_service->syncTruthByChangeRelationWord();
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
     * 類義語エクスポート
     * @param SynonymExport $export
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(SynonymExport $export)
    {
        //エクスポートログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_export.id'));
        $ext = config('excel.exports.extensions');
        return $export->download(str_replace('.', '_', self::ROUTE_NAME) . '_' . date('YmdHis') . $ext)->deleteFileAfterSend(true);
    }

    /**
     * 完了メッセージ出力
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function complete($request)
    {
        return redirect()->route(self::ROUTE_NAME . '.index')->with($this->createFlushMessage([
            '情報' => __('類義語データ') . "の{$this->getModeName($request)}が完了しました。"
        ]));
    }
}
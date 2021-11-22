<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\ProperNounExport;
use App\Http\Controllers\Traits\FormTrait;
use App\Http\Requests\ExcelImportRequest;
use App\Imports\Admin\ProperNounImport;
use App\Services\Admin\KeyPhraseService;
use App\Services\Admin\ProperNounService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProperNounRequest;
use Constant;

/**
 * 固有名詞データ管理コントローラ
 * Class ProperNounController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ProperNounController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.proper_noun';
    /**
     * @var ProperNounService
     */
    private $service;
    /**
     * @var KeyPhraseService
     */
    private $key_phrase_service;

    /**
     * ProperNounController constructor.
     * @param ProperNounService $service
     * @param KeyPhraseService $key_phrase_service
     */
    public function __construct(ProperNounService $service, KeyPhraseService $key_phrase_service)
    {
        $this->service = $service;
        $this->autoSetPermission('proper_noun');
        $this->key_phrase_service = $key_phrase_service;
    }

    //API

    /**
     * 固有名詞データ一覧(API)
     * @param Request $request
     * @return type
     */
    public function properNounList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->datatable();
        $datatable->getConverter()->setConvert(['disabled'], function ($val) {
            return Constant::getConstName($val, 'common.disabled');
        })->setConvert(['type'], function ($val) {
            return Constant::getConstName($val, 'truth.proper_noun_type');
        });
        return response($datatable->toArray());
    }

    //WEB

    /**
     * 固有名詞一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index');
    }

    /**
     * 新規固有名詞
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view(self::ROUTE_NAME . '.create')
            ->with($this->getRedirectConfirm($request));
    }

    /**
     * 固有名詞登録処理
     * @param ProperNounRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(ProperNounRequest $request)
    {
        $params = $request->all();
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $insert = $this->service->createProperNoun($params);
            //同期
            $this->key_phrase_service->syncTruthByChangeRelationWord(['proper_noun_id' => $insert->id]);
            //新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $redirect = redirect()->route(self::ROUTE_NAME . '.create');
            $params['confirm'] = 1;
            return $redirect->withInput($params);
        }
    }

    /**
     * 固有名詞修正(固有名詞ID版)
     * @param $proper_noun_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editFromProperNounId($proper_noun_id)
    {
        $data = $this->service->getRepository()->findOneBy(['proper_noun_id' => $proper_noun_id]);
        return redirect()->route(self::ROUTE_NAME . '.edit', ['proper_noun' => $data['id']]);
    }

    /**
     * 固有名詞修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $proper_noun = $this->service->getRepository()->getOneById($id);
        $request->request->add($proper_noun);
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request));
    }

    /**
     * 固有名詞更新処理
     * @param ProperNounRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(ProperNounRequest $request, $id)
    {
        $params = $request->all();
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->getRepository()->update($id, $params);
            //同期
            $this->key_phrase_service->syncTruthByChangeRelationWord(['proper_noun_id' => $id]);
            //修正ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $redirect = redirect()->route(self::ROUTE_NAME . '.edit', ['proper_noun' => $id]);
            $params['confirm'] = 1;
            return $redirect->withInput($params);
        }
    }

    /**
     * 固有名詞削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        //物理削除
        $this->service->getRepository()->deleteOneById($id);
        //同期
        $this->key_phrase_service->syncTruthByChangeRelationWord(['proper_noun_id' => $id]);
        return $this->complete($request);

    }

    /**
     * 固有名詞インポート
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(Request $request)
    {
        $this->clearFile($request);
        return view(self::ROUTE_NAME . '.import')->with($this->getRedirectConfirm($request));
    }

    /**
     * 固有名詞インポート処理
     * @param ExcelImportRequest $request
     * @param ProperNounImport $import
     * @return type|\Illuminate\Http\RedirectResponse
     */
    public function importStore(ExcelImportRequest $request, ProperNounImport $import)
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
     * 固有名詞エクスポート
     * @param ProperNounExport $export
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(ProperNounExport $export)
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
            '情報' => "固有名詞データの{$this->getModeName($request)}が完了しました。"
        ]));
    }

}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\CategoryService;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Requests\CsvImportRequest;
use App\Imports\Admin\CategoryImport;
use App\Exports\Admin\CategoryExport;

/**
 * カテゴリデータ管理コントローラ
 * Class CategoryController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class CategoryController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.category';
    /**
     * @var CategoryService
     */
    private $service;

    /**
     * CategoryController constructor.
     * @param CategoryService $service
     */
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
        $this->autoSetPermission('category');
    }

    //API

    /**
     * カテゴリデータ一覧(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->datatable();
        $datatable->getConverter()
            ->setConvert(['name'], function ($val) {
                return str_omit_tooltip($val, config('bot.common.str_omit_length'));
            });
        return response($datatable->toArray());
    }

    //WEB

    /**
     * カテゴリ一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index');
    }

    /**
     * 新規カテゴリ
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view(self::ROUTE_NAME . '.create')->with($this->getRedirectConfirm($request));
    }

    /**
     * カテゴリ登録処理
     * @param CategoryRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(CategoryRequest $request)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->getRepository()->create($request->all());
            //新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.create')->withInput($request->all());
        }
    }

    /**
     * カテゴリ詳細
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
     * カテゴリ修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $category = $this->service->getRepository()->getOneById($id);
        $request->request->add($category);
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request));
    }

    /**
     * カテゴリ更新処理
     * @param CategoryRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(CategoryRequest $request, $id)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->getRepository()->update($id, $request->all());
            //修正ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.edit', ['category' => $id])->withInput($request->all());
        }
    }

    /**
     * カテゴリ削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        $this->service->getRepository()->deleteOneById($id);
        //削除ログ
        $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_destroy.id'));
        return $this->complete($request);
    }

    /**
     * 完了メッセージ出力
     * @param type $request
     * @return type
     */
    private function complete($request)
    {
        return redirect()->route(self::ROUTE_NAME . '.index')->with($this->createFlushMessage([
            '情報' => __('カテゴリデータ') . "の{$this->getModeName($request)}が完了しました。"
        ]));
    }
}
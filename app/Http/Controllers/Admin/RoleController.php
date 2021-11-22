<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\RoleService;
use App\Http\Requests\Admin\RoleRequest;

/**
 * 権限情報管理コントローラ
 * Class RoleController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RoleController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.role';
    /**
     * @var RoleService
     */
    private $service;

    /**
     * RoleController constructor.
     * @param RoleService $service
     */
    public function __construct(RoleService $service)
    {
        $this->service = $service;
        $this->autoSetPermission('role');
    }

    //API

    /**
     * 権限情報一覧(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function roleList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->datatable();
        return response($datatable->toArray());
    }

    //WEB

    /**
     * 権限一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index');
    }

    /**
     * 新規権限
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $permissions = $this->service->getPermissionArray();
        return view(self::ROUTE_NAME . '.create')->with($this->getRedirectConfirm($request))
            ->with(['permissions' => $permissions]);
    }

    /**
     * 権限登録処理
     * @param RoleRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(RoleRequest $request)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->createRole($request->all());
            //新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.create')->withInput($request->all());
        }
    }

    /**
     * 権限詳細
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $data = $this->service->getRepository()->getOneById($id);
        $permissions = $this->service->getPermissionArray($data['name']);
        return view(self::ROUTE_NAME . '.show')->with('id', $id)
            ->with('data', $data)->with($this->getRedirectConfirm($request))->with(['permissions' => $permissions]);
    }

    /**
     * 権限修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $role = $this->service->getRepository()->getOneById($id);
        $permissions = $this->service->getPermissionArray($role['name']);
        $request->request->add($role);
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request))
            ->with(['permissions' => $permissions]);
    }

    /**
     * 権限更新処理
     * @param RoleRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(RoleRequest $request, $id)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->updateRole($id, $request->all());
            //修正ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.edit', ['role' => $id])->withInput($request->all());
        }
    }

    /**
     * 権限削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        //管理者権限は消せない
        $role = $this->service->getRepository()->getOneById($id);
        if ($role['name'] == config('acl.admin.role')) {
            abort(403, __('管理者権限を削除することはできません'));
        }
        //削除処理
        if ($this->isStore($request)) {
            $this->service->getRepository()->deleteOneById($id);
            //削除ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_destroy.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.show', ['role' => $id])->withInput($request->all());
        }
    }

    /**
     * 完了メッセージ出力
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function complete($request)
    {
        return redirect()->route(self::ROUTE_NAME . '.index')->with($this->createFlushMessage([
            '情報' => __('権限情報') . "の{$this->getModeName($request)}が完了しました。"
        ]));
    }
}
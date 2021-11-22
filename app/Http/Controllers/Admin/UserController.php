<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Traits\FormTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Admin\UserService;
use App\Http\Requests\Admin\UserRequest;

/**
 * アカウント管理コントローラ
 * Class UserController
 * @package App\Http\Controllers\Admin
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserController extends Controller
{
    use FormTrait;
    /**
     * ルート名
     */
    const ROUTE_NAME = 'admin.user';
    /**
     * @var UserService
     */
    private $service;

    /**
     * UserController constructor.
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
        $this->autoSetPermission('user');
    }

    //API

    /**
     * アカウント一覧(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function userList(Request $request)
    {
        $datatable = $this->service->getRepository()->setParams($request->all())->filterByParams()->datatable();
        return response($datatable->toArray());
    }

    //WEB

    /**
     * アカウント一覧
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(self::ROUTE_NAME . '.index');
    }

    /**
     * 新規アカウント
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $roles = $this->service->getRoleArray();
        return view(self::ROUTE_NAME . '.create')->with($this->getRedirectConfirm($request))
            ->with(['roles' => $roles]);
    }

    /**
     * アカウント登録処理
     * @param UserRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(UserRequest $request)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $user = $this->service->getRepository()->createUser($request->all());
            $this->service->updateRole($user->id, $request->get('roles'));
            //新規登録ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_store.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.create')->withInput($request->all());
        }
    }

    /**
     * アカウント詳細
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $data = $this->service->getRepository()->getOneById($id);
        $roles = $this->service->getRoleArray($id);
        return view(self::ROUTE_NAME . '.show')->with('id', $id)
            ->with('data', $data)->with($this->getRedirectConfirm($request))->with(['roles' => $roles]);
    }

    /**
     * アカウント修正
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $user = $this->service->getRepository()->getOneById($id);
        $request->request->add($user);
        $roles = $this->service->getRoleArray($id);
        return view(self::ROUTE_NAME . '.edit')->with('id', $id)->with($this->getRedirectConfirm($request))
            ->with(['roles' => $roles]);
    }

    /**
     * アカウント更新処理
     * @param UserRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function update(UserRequest $request, $id)
    {
        if ($this->isStore($request)) {
            $request->session()->regenerateToken();
            $this->service->updateRole($id, $request->get('roles'));
            $this->service->getRepository()->updateUser($id, $request->all());
            //修正ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_update.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.edit', ['user' => $id])->withInput($request->all());
        }
    }

    /**
     * アカウント削除処理
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function destroy(Request $request, $id)
    {
        //スーパーユーザーは消せない
        $user = $this->service->getRepository()->getOneById($id);
        if ($user['name'] == config('acl.admin.user')) {
            abort(403, __('管理者を削除することはできません'));
        }
        //削除処理
        if ($this->isStore($request)) {
            $this->service->getRepository()->deleteOneById($id);
            //削除ログ
            $this->service->saveLog(config('const.function.' . self::ROUTE_NAME . '_destroy.id'));
            return $this->complete($request);
        } else {
            $request->request->set('confirm', 1);
            return redirect()->route(self::ROUTE_NAME . '.show', ['user' => $id])->withInput($request->all());
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
            '情報' => __('アカウント情報') . "の{$this->getModeName($request)}が完了しました。"
        ]));
    }
}
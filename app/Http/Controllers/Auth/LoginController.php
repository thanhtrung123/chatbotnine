<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Services\Admin\LogService;
use DB;

/**
 * This controller handles authenticating users for the application and
 * redirecting them to your home screen. The controller uses a trait
 * to conveniently provide its functionality to your applications.
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;
    /**
     * @var LogService
     */
    private $log_service;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LogService $log_service)
    {
        $this->middleware('guest')->except('logout');
        $this->log_service = $log_service;
    }

    /**
     * ログインID名
     * @return string
     */
    public function username(): string
    {
        return 'name';
    }

    /**
     * ログイン時
     * @param Request $request
     * @param User $user
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function authenticated(Request $request, $user)
    {
        $this->log_service->saveLog(config('const.function.admin.login.id'));
        $user->update(['api_token' => str_random(60)]);
    }

    /**
     * ログアウト
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $this->log_service->saveLog(config('const.function.admin.logout.id'));
        $user = $request->user();
        $user->update(['api_token' => null]);
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect(route('admin'));
    }
}
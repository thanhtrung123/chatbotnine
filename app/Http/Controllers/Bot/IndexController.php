<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Services\Bot\BotEnqueteService;
use App\Services\Bot\BotService;
use App\Services\Bot\BotSessionService;
use Illuminate\Http\Request;
use Util;

/**
 * チャットボットインデックスコントローラ
 * Class IndexController
 * @package App\Http\Controllers\Bot
 */
class IndexController extends Controller
{
    /**
     * @var BotService
     */
    private $bot_service;
    /**
     * @var BotSessionService
     */
    private $session_service;
    /**
     * @var BotEnqueteService
     */
    private $enquete_service;

    /**
     * IndexController constructor.
     * @param BotService $bot_service
     * @param BotSessionService $session_service
     * @param BotEnqueteService $enquete_service
     */
    public function __construct(BotService $bot_service, BotSessionService $session_service, BotEnqueteService $enquete_service)
    {
        $this->bot_service = $bot_service;
        $this->session_service = $session_service;
        $this->enquete_service = $enquete_service;
    }

    /**
     * チャットボット画面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->session_service->delete()->save();
        Util::overrideConfig('bot.api.' . config('bot.api.use'), 'bot.api.default');
        $browser_info = $this->bot_service->getLogService()->getInfoBrowserAndOs();
        $browser_id = $browser_info['browser_id'];
        $browser_not_supported = [config('const.useragent.browser.ie.id')];
        $browser_support_flg = (!in_array($browser_id, $browser_not_supported)) ? TRUE : FALSE;
        return view('bot')->with([
            'browser_support_flg' => $browser_support_flg,
            'api_name' => config('bot.api.default.name'),
            'chat_id' => $this->bot_service->getBaseService()->getChatId(),
            'init_data' => $this->bot_service->setStatus(config('bot.const.bot_status_show_category'))->setMessage('')->exec()->getResultData(),
            'disp_info' => $request->get('info') ? true : false,
            //
            'route' => [
                'index' => route('api.bot.index'),
                'user_log' => route('api.bot.user_log'),
                'suggest' => config('bot.suggest.enabled') ? route('api.bot.suggest') : null,
            ],
        ]);
    }
}
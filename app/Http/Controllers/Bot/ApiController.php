<?php

namespace App\Http\Controllers\Bot;

use App\Http\Requests\Bot\ApiSnsRequest;
use App\Services\Admin\LearningService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Bot\BotService;
use App\Http\Requests\Bot\ApiRequest;

/**
 * チャットボットAPIコントローラー
 * Class ApiController
 * @package App\Http\Controllers\Bot
 */
class ApiController extends Controller
{
    /**
     * @var BotService
     */
    private $bot_service;
    /**
     * @var LearningService
     */
    private $learning_service;

    /**
     * ApiController constructor.
     * @param BotService $bot_service
     * @param LearningService $learning_service
     */
    public function __construct(BotService $bot_service, LearningService $learning_service)
    {
        $this->bot_service = $bot_service;
        $this->learning_service = $learning_service;
    }

    /**
     * 利用者情報ログ(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function userLog(Request $request)
    {
        $chat_id = $request->get('id');
        $status = $request->get('status');
        $channel = $request->get('channel');

        $is_close = in_array($status, [config('const.useragent.status.close.id'), config('const.useragent.status.enquete_close.id')]);
        //ユーザー情報作成
        $ret = $this->bot_service->getLogService()
            ->setChatId($chat_id)
            ->createUserInfo($status);
        //BIツール用ログ
        if (in_array($status, [config('const.useragent.status.load.id'), config('const.useragent.status.close.id')])) {
            $bi_log_service = $this->bot_service->getBaseService()->getBotBiLogService();
            $bi_log_service
                ->setChannel($channel)
                ->setChatId($chat_id);
            if ($is_close)
                $bi_log_service->setCloseDatetime(date(config('bot.bi_log.date_format')));
            else
                $bi_log_service->setLoadDatetime(date(config('bot.bi_log.date_format')));
            $bi_log_service->output();
        }

        return response(['status' => ($ret === false) ? false : true]);
    }

    /**
     * チャットボット用API
     * @param ApiRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(ApiRequest $request)
    {
        //ユーザーがログインしていたらログを無効
        if ($request->user()) {
            $this->bot_service->getLogService()->setEnabled(false);
            $this->bot_service->getBaseService()->setIsDebug(true);
        }
        //チャットボットサービスにメッセージとステータスをセット
        $this->bot_service->setRequestParams($request->request->all());
        //実行
        $result = $this->bot_service->exec()->getResultData();
        return response($result);
    }

    /**
     * チャットボット用API(SNS用)
     * @param ApiSnsRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function sns(ApiSnsRequest $request)
    {
        //チャットボットサービスにメッセージとステータスをセット
        $params = $request->request->all();
        $this->bot_service->setRequestParams($params);
        //初期設定
        $channel = $request->get('channel');
        $this->bot_service->getBaseService()->setChannel($channel);
        $session_id = $request->get('id');
        $session = $this->bot_service->getBaseService()->getBotSessionService()->getSession();
        $session->setId($session_id);
        $session->start();
        ///
        $chat_id = $this->bot_service->getBaseService()->getSnsUidMapService()
            ->setChannel($channel)
            ->getChatId($session_id);
        $this->bot_service->setChatId($chat_id);
        //実行
        $result = $this->bot_service->exec()->getResultData();
        return response($result);
    }

    /**
     * サジェスト表示用(API)
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function suggest(Request $request)
    {
        $message = mbTrim($request->get('message'));
        $result = ['input' => $message, 'list' => []];
        if (!empty($message)) {
            $limit = config('bot.suggest.pc_max_sentence');
            if ($request->get('device_type') == config('const.common.device.smartphone.id')) {
                $limit = config('bot.suggest.mobile_max_sentence');
            }
            $learning_gen = $this->learning_service->getRepository()->setParams([
                'suggest' => $message,
                'limit' => $limit,
            ])->filterByParams()->setGroup('answer')->getDbResult()->getGenerator();
            foreach ($learning_gen as $row) {
                $result['list'][] = ['label' => $row['question']];
            }
        } else {

        }
        return response($result);
    }

}
<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Services\Bot\BotEnqueteService;
use App\Services\Bot\BotLogService;
use App\Services\Bot\ResponseInfoService;
use App\Services\Bot\Sns\SnsUidMapService;
use Illuminate\Http\Request;

/**
 * アンケートコントローラ
 * Class EnqueteController
 * @package App\Http\Controllers\Bot
 */
class EnqueteController extends Controller
{
    /**
     * @var ResponseInfoService
     */
    private $response_info_service;
    /**
     * @var BotEnqueteService
     */
    private $enquete_service;

    /**
     * @var SnsUidMapService
     */
    private $sns_uid_map_service;

    /**
     * @var bool
     */
    private $is_web = true;

    /**
     * EnqueteController constructor.
     * @param ResponseInfoService $response_info_service
     * @param BotEnqueteService $enquete_service
     */
    public function __construct(ResponseInfoService $response_info_service, BotEnqueteService $enquete_service, SnsUidMapService $sns_uid_map_service)
    {
        $this->response_info_service = $response_info_service;
        $this->enquete_service = $enquete_service;
        $this->sns_uid_map_service = $sns_uid_map_service;
    }

    /**
     * アンケート登録画面
     * @param string $form_hash
     * @param string $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function entry($form_hash, $key = null)
    {
        $channel = null;
        $sns_chat_id = null;
        $is_stored = session('store', false);
        if (is_null($key)) {
            //WEB側
            $enquete_exp = session()->get('ENQUETE_EXP_TIME');
            if (empty($enquete_exp) && !$is_stored) {
                return $this->enqueteError(config('const.enquete.error.exp.id'));
            }
            $url_info = parse_url(url()->previous());
            $prev_url = $url_info['scheme'] . '://' . $url_info['host'] . rtrim($url_info['path'], '/');
            //リファラチェック
            if (!$is_stored && $prev_url != route('home') && empty(session()->get('errors'))) {
                return $this->enqueteError();
            }
            $diff = time() - $enquete_exp;
        } else {
            //SNS側
            $this->is_web = false;
            $hash = $this->sns_uid_map_service->findEnqueteKey($key);
            if (empty($hash)) {
                return $this->enqueteError();
            }
            $diff = time() - strtotime($hash['updated_at']);
            $sns_chat_id = $hash['chat_id'];
            $channel = $hash['channel'];
        }
        if ($diff > config('bot.enquete.expiration') && !$is_stored) {
            return $this->enqueteError(config('const.enquete.error.exp.id'));
        }

        list($form_id, $form_setting) = $this->enquete_service->getFormDataFromHash($form_hash, $key);
        if (empty($form_id)) {
            return $this->enqueteError();
        }
        return view('enquete.entry')->with([
            'form_hash' => $form_hash,
            'form_setting' => $form_setting,
            'sns_chat_id' => $sns_chat_id,
            'is_web' => $this->is_web,
            'channel' => $channel,
            'enquete_key' => $key,
            'route' => [
                'user_log' => route('api.bot.user_log'),
            ],
        ]);
    }

    /**
     * エラー画面
     * @param int $error_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    private function enqueteError($error_id = 0)
    {
        $message = \Util::getCustomErrorMessage($error_id, 'enquete.error', 'bot.enquete.error_messages');
        return view('enquete.error')->with(['message' => $message, 'is_web' => $this->is_web]);
    }

    /**
     * アンケート登録処理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        if (empty($params['key'])) $this->is_web = false;
        $params = $request->all();
        list($form_id, $form_setting) = $this->enquete_service->getFormDataFromHash($params['form_hash'], $params['key'] ?? null);
        $valid_rules = $valid_attrs = [];
        $q_no = 1;
        foreach ($form_setting['items'] as $q_cd => $row) {
            $valid_attrs["question.{$q_cd}"] = "{$form_setting['question_prefix']}{$q_no}";
            $q_no++;
            if (!isset($row['validate'])) continue;
            $valid_rules["question.{$q_cd}"] = $row['validate'];
        }
        $request->validate($valid_rules, [], $valid_attrs);
        $request->session()->regenerateToken();
        $this->enquete_service->createEnquete($params);
        return redirect()->route('enquete.entry', ['form_hash' => $params['form_hash'], 'key' => $params['key']])->with('store', true);
    }

}
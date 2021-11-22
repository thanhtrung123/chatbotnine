<?php

namespace App\Services\Bot;

use App\Repositories\ResponseInfo\ResponseInfoRepositoryInterface;
use App\Repositories\ResponseInfoUser\ResponseInfoUserRepositoryInterface;
use App\Services\Bot\BotSessionService;
use App\Services\UserAgentService;
use Request;

/**
 * チャットボット応答情報サービス
 * Class BotLogService
 * @package App\Services\Bot
 */
class BotLogService
{
    /**
     * @var bool 有効フラグ
     */
    private $enabled = true;
    /**
     * @var ResponseInfoRepositoryInterface
     */
    private $info_repository;
    /**
     * @var ResponseInfoUserRepositoryInterface
     */
    private $info_repository_user;
    /**
     * @var \App\Services\Bot\BotSessionService
     */
    private $session_service;
    /**
     * @var UserAgentService
     */
    private $ua_service;
    //共通
    /**
     * @var int チャンネル
     */
    private $channel;
    /**
     * @var string IPアドレス
     */
    private $user_ip;
    /**
     * @var integer ユーザID
     */
    private $chat_id;
    //ユーザー用
    /**
     * @var string UA
     */
    private $useragent;
    /**
     * @var string リファラ
     */
    private $referrer;
    //通常用
    /**
     * @var string $talk_id
     */
    private $talk_id;
    /**
     * @var string 処理日時
     */
    private $action_datetime;
    /**
     * @var string 入力ステータス
     */
    private $status;
    /**
     * @var string ユーザ入力文章
     */
    private $user_input;
    /**
     * @var string ユーザ入力文章（QnA用キーフレーズ）
     */
    private $user_input_morph;
    /**
     * @var integer API_ID
     */
    private $api_id;
    /**
     * @var string API回答
     */
    private $api_answer;
    /**
     * @var float APIスコア
     */
    private $api_score;
    /**
     * @var string API質問
     */
    private $api_question;
    /**
     * @var string 選択シンボル
     */
    private $selection_symbol;
    /**
     * @var integer 聞き返しフラグ
     */
    private $is_hear_back;
    /**
     * @var integer 選択フラグ
     */
    private $is_select;

    /**
     * BotLogService constructor.
     * @param ResponseInfoRepositoryInterface $info_repository
     * @param ResponseInfoUserRepositoryInterface $info_repository_user
     * @param \App\Services\Bot\BotSessionService $session_service
     * @param UserAgentService $ua_service
     */
    public function __construct(ResponseInfoRepositoryInterface $info_repository, ResponseInfoUserRepositoryInterface $info_repository_user, BotSessionService $session_service, UserAgentService $ua_service)
    {
        $this->info_repository = $info_repository;
        $this->info_repository_user = $info_repository_user;
        $this->session_service = $session_service;
        $this->ua_service = $ua_service;
    }

    /**
     * 利用者情報登録
     * @param $status
     * @return bool|mixed
     */
    public function createUserInfo($status)
    {
        $this->setMeta();
        //UAを解析
        $this->ua_service->setUserAgent($this->useragent)->analyzeUserAgent();
        //データ作成
        $data = [
//            'channel' => $this->channel,
            'chat_id' => $this->chat_id,
            'remote_ip' => $this->user_ip,
            'referrer' => $this->referrer,
            'useragent' => $this->useragent,
            'os_id' => $this->ua_service->getOsId(),
            'os_version' => $this->ua_service->getOsVersion(),
            'browser_id' => $this->ua_service->getBrowserId(),
            'browser_version' => $this->ua_service->getBrowserVersion(),
            'status' => $status,
        ];
        //
        $create_enable = !$this->info_repository_user->existsEqualsData($data);
        return ($this->enabled && $create_enable) ? $this->info_repository_user->saveLog($data) : false;
    }

    /**
     * 応答情報登録
     * @param bool $auto_meta_flg
     * @return bool
     */
    public function create($auto_meta_flg = true)
    {
        //回答無し以外でAPIIDがないものは除外
        if (empty($this->api_id) && equal_digit($this->status, config('const.bot.status.response_select.id'))) {
            return false;
        }
        if ($auto_meta_flg) {
            $this->setMeta();
        }
        $data = [
            'channel' => $this->channel,
            'user_ip' => $this->user_ip,
            'chat_id' => $this->chat_id,
            'talk_id' => $this->talk_id,
            'status' => $this->status,
            'user_input' => $this->user_input,
            'user_input_morph' => $this->user_input_morph,
            'api_id' => $this->api_id,
            'api_answer' => $this->api_answer,
            'api_question' => $this->api_question,
        ];
        if ($this->action_datetime) $data['action_datetime'] = $this->action_datetime;
        if ($this->api_score) $data['api_score'] = $this->api_score;
        if ($this->is_hear_back) $data['is_hear_back'] = $this->is_hear_back;
        if ($this->is_select) $data['is_select'] = $this->is_select;
        $this->clear();
        return $this->enabled ? $this->info_repository->saveLog($data) : false;
    }

    /**
     * 応答情報更新（質問入力に対して）
     * @return bool|mixed
     */
    public function updateQuestionInput()
    {
        $this->setMeta();
        $data = $this->getInputData();
        if (empty($data)) return false;
        return $this->enabled ? $this->info_repository->update($data['id'], [
            'status' => $this->status,
            'api_id' => $this->api_id,
            'api_answer' => $this->api_answer,
            'api_question' => $this->api_question,
//                'selection_symbol' => $this->selection_symbol,
            'api_score' => $this->api_score,
            'is_hear_back' => $this->is_hear_back,
        ]) : false;
    }

    /**
     * 質問入力情報取得
     * @return array
     */
    public function getInputData()
    {
        return $this->info_repository->setParams([
            'channel' => $this->channel,
            'user_ip' => $this->user_ip,
            'chat_id' => $this->chat_id,
            'talk_id' => $this->talk_id,
            'status' => config('const.bot.status.question_input.id'),
        ])->filterByParams()->getDbResult()->getOne();
    }

    /**
     * ステータスが存在するか
     * @param $status
     * @return array
     */
    public function existsStatus($status)
    {
        return $this->info_repository->setParams([
            'channel' => $this->channel,
            'user_ip' => $this->user_ip,
            'chat_id' => $this->chat_id,
            'talk_id' => $this->talk_id,
            'status' => $status,
        ])->filterByParams()->getDbResult()->getOne();
    }

    /**
     * クリア
     * @return $this
     */
    public function clear()
    {
        $this->setStatus(null)
            ->setUserInput(null)
            ->setUserInputMorph(null)
            ->setApiId(null)
            ->setApiScore(null)
            ->setApiAnswer(null)
            ->setSelectionSymbol(null)
            ->setApiQuestion(null)
            ->setIsSelect(0);
        return $this;
    }

    /**
     * レスポンス　セット
     * @return $this
     */
    public function setResponse(array $qa_ary)
    {
        $this->setApiId($qa_ary['id'] ?? null);
        $this->setApiScore($qa_ary['score'] ?? null);
        $this->setApiAnswer($qa_ary['answer'] ?? null);
        $this->setSelectionSymbol($qa_ary['selection_symbol'] ?? null);
        $this->setIsSelect($qa_ary['is_select'] ?? 0);
        if (isset($qa_ary['question_str'])) {
            $this->setApiQuestion($qa_ary['question_str']);
        } else if (isset($qa_ary['question'])) {
            $this->setApiQuestion(is_array($qa_ary['question']) ? $qa_ary['question'][0] : $qa_ary['question']);
        } else {
            $this->setApiQuestion(null);
        }
        return $this;
    }

    /**
     * メタ情報　セット
     * @return $this
     */
    public function setMeta()
    {
        $this->session_service->sync();
        $this->setUserIp(Request::server('REMOTE_ADDR'))
            ->setIsHearBack($this->session_service->isHearBack() ? 1 : 0)
            ->setReferrer(Request::server('HTTP_REFERER'))
            ->setUseragent(Request::server('HTTP_USER_AGENT'));
        return $this;
    }

    /**
     * チャンネルセット
     * @param $channel
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * IPセット
     * @param $user_ip
     * @return $this
     */
    public function setUserIp($user_ip)
    {
        $this->user_ip = $user_ip;
        return $this;
    }

    /**
     * chat_idセット
     * @param $chat_id
     * @return $this
     */
    public function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
        return $this;
    }

    /**
     * talk_idセット
     * @param $talk_id
     * @return $this
     */
    public function setTalkId($talk_id)
    {
        $this->talk_id = $talk_id;
        return $this;
    }

    /**
     * 処理日時セット
     * @param $action_datetime
     * @return $this
     */
    public function setActionDatetime($action_datetime)
    {
        $this->action_datetime = $action_datetime;
        return $this;
    }

    /**
     * ステータスセット
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * ユーザー入力文字セット
     * @param $user_input
     * @return $this
     */
    public function setUserInput($user_input)
    {
        $this->user_input = $user_input;
        return $this;
    }

    /**
     * ユーザー入力文字（形態素解析）セット
     * @param $user_input_morph
     * @return $this
     */
    public function setUserInputMorph($user_input_morph)
    {
        $this->user_input_morph = $user_input_morph;
        return $this;
    }

    /**
     * 回答IDセット
     * @param $api_id
     * @return $this
     */
    public function setApiId($api_id)
    {
        $this->api_id = $api_id;
        return $this;
    }

    /**
     * 回答セット
     * @param $api_answer
     * @return $this
     */
    public function setApiAnswer($api_answer)
    {
        $this->api_answer = $api_answer;
        return $this;
    }

    /**
     * スコアセット
     * @param $api_score
     * @return $this
     */
    public function setApiScore($api_score)
    {
        $this->api_score = $api_score;
        return $this;
    }

    /**
     * 質問セット
     * @param $api_question
     * @return $this
     */
    public function setApiQuestion($api_question)
    {
        $this->api_question = $api_question;
        return $this;
    }

    /**
     * シンボルセット
     * @param $selection_symbol
     * @return $this
     */
    public function setSelectionSymbol($selection_symbol)
    {
        $this->selection_symbol = $selection_symbol;
        return $this;
    }

    /**
     * 聞き返しセット
     * @param $hear_back_flg
     * @return $this
     */
    public function setIsHearBack($hear_back_flg)
    {
        $this->is_hear_back = $hear_back_flg;
        return $this;
    }

    /**
     * 選択セット
     * @param $is_select
     * @return $this
     */
    public function setIsSelect($is_select)
    {
        $this->is_select = $is_select;
        return $this;
    }

    /**
     * 有効
     * @param $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * UAセット
     * @param mixed $useragent
     * @return $this
     */
    public function setUseragent($useragent)
    {
        $this->useragent = $useragent;
        return $this;
    }

    /**
     * リファラセット
     * @param mixed $referrer
     * @return $this
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
        return $this;
    }


    /**
     * get info browser
     * 
     * @return $data
     */
    public function getInfoBrowserAndOs()
    {
        $this->setMeta();
        //UAを解析
        $this->ua_service->setUserAgent($this->useragent)->analyzeUserAgent();
        //データ作成
        $data = [
            'os_id' => $this->ua_service->getOsId(),
            'os_version' => $this->ua_service->getOsVersion(),
            'browser_id' => $this->ua_service->getBrowserId(),
            'browser_version' => $this->ua_service->getBrowserVersion(),
        ];
        return $data;
    }

}
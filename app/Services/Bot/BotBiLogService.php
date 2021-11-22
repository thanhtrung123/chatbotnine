<?php

namespace App\Services\Bot;

use App\Services\File\CsvService;

/**
 * チャットボットBIツール用ログサービス
 * Class BotBiLogService
 * @package App\Services\Bot
 */
class BotBiLogService
{
    /**
     * @var CsvService
     */
    private $csv_service;
    //
    /**
     * @var string chat_id
     */
    private $chat_id;
    /**
     * @var string talk_id
     */
    private $talk_id;
    /**
     * @var string ユーザ入力文章
     */
    private $user_input;
    /**
     * @var string 入力ステータス
     */
    private $status;
    /**
     * @var string 結果ステータス
     */
    private $result_status;
    /**
     * @var integer 選択ID
     */
    private $select_id;
    /**
     * @var string 選択メッセージ
     */
    private $select_message;
    /**
     * @var string 選択候補キーフレーズ
     */
    private $choice_key_phrase;
    /**
     * @var string 選択候補質問
     */
    private $choice_question;
    /**
     * @var integer 有人チャット使用
     */
    private $chat_used;
    /**
     * @var integer 選択したフィードバック
     */
    private $select_feedback;
    /**
     * @var integer シナリオ使用
     */
    private $scenario_used;
    /**
     * @var string 処理日時
     */
    private $action_datetime;
    /**
     * @var string ロード日時
     */
    private $load_datetime;
    /**
     * @var string クローズ日時
     */
    private $close_datetime;
    /**
     * @var integer channel
     */
    private $channel;

    /**
     * BotBiLogService constructor.
     * @param CsvService $csv_service
     */
    public function __construct(CsvService $csv_service)
    {
        $this->csv_service = $csv_service
            ->setCharCode(config('bot.bi_log.char_code'))
            ->setNewline(config('bot.bi_log.newline'));
    }

    /**
     * BIツール用ログ出力
     * @param bool $use_now_action_time 実行した時間を処理日時にする
     * @throws \Exception
     */
    public function output($use_now_action_time = true)
    {
        //無効の場合は終了
        if (!config('bot.bi_log.enabled')) return;
        //書き込み
        $data = [
            'chat_id' => $this->chat_id,
            'talk_id' => $this->talk_id,
            'user_input' => $this->user_input,
            'status' => $this->status,
            'result_status' => $this->result_status,
            'select_id' => $this->select_id,
            'select_message' => $this->select_message,
            'choice_key_phrase' => $this->choice_key_phrase,
            'choice_question' => $this->choice_question,
            'chat_used' => $this->chat_used,
            'select_feedback' => $this->select_feedback,
            'scenario_used' => $this->scenario_used,
            'channel' => $this->channel,
            'action_datetime' => $use_now_action_time ? date(config('bot.bi_log.date_format')) : $this->action_datetime,
            'load_datetime' => $this->load_datetime,
            'close_datetime' => $this->close_datetime,
        ];

        $file_path_base = preg_replace_callback('/{(.+)}/', function ($match) {
            return date($match[1]);
        }, config('bot.bi_log.path'));
        $file_path = storage_path($file_path_base);
        $this->csv_service->setHeader(config('bot.bi_log.header'))->addRow($data)
            ->append($file_path)->clear();
    }

    /**
     * chat_id セット
     * @param string $chat_id
     * @return $this
     */
    public function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
        return $this;
    }

    /**
     * talk_id セット
     * @param string $talk_id
     * @return $this
     */
    public function setTalkId($talk_id)
    {
        $this->talk_id = $talk_id;
        return $this;
    }

    /**
     * ユーザー入力文字　セット
     * @param string $user_input
     * @return $this
     */
    public function setUserInput($user_input)
    {
        $this->user_input = $user_input;
        return $this;
    }

    /**
     * 入力ステータスセット
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * 結果ステータスセット
     * @param string $result_status
     * @return $this
     */
    public function setResultStatus($result_status)
    {
        $this->result_status = $result_status;
        return $this;
    }

    /**
     * 選択IDセット
     * @param integer $select_id
     * @return $this
     */
    public function setSelectId($select_id)
    {
        $this->select_id = $select_id;
        return $this;
    }

    /**
     * 選択メッセージセット
     * @param string $select_message
     * @return $this
     */
    public function setSelectMessage($select_message)
    {
        $this->select_message = $select_message;
        return $this;
    }

    /**
     * 候補ワードセット
     * @param string $choice_key_phrase
     * @return $this
     */
    public function setChoiceKeyPhrase($choice_key_phrase)
    {
        $this->choice_key_phrase = $choice_key_phrase;
        return $this;
    }

    /**
     * 候補質問セット
     * @param string $choice_question
     * @return $this
     */
    public function setChoiceQuestion($choice_question)
    {
        $this->choice_question = $choice_question;
        return $this;
    }

    /**
     * 有人チャット使用
     * @param integer $chat_used
     * @return $this
     */
    public function setChatUsed($chat_used)
    {
        $this->chat_used = $chat_used;
        return $this;
    }

    /**
     * フィードバックセット
     * @param integer $select_feedback
     * @return $this
     */
    public function setSelectFeedback($select_feedback)
    {
        $this->select_feedback = $select_feedback;
        return $this;
    }

    /**
     * シナリオ使用
     * @param integer $scenario_used
     * @return $this
     */
    public function setScenarioUsed($scenario_used)
    {
        $this->scenario_used = $scenario_used;
        return $this;
    }

    /**
     * @param int $channel
     * @return $this
     */
    public function setChannel(int $channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * 処理日時
     * @param string $action_datetime
     * @return $this
     */
    public function setActionDatetime($action_datetime)
    {
        $this->action_datetime = $action_datetime;
        return $this;
    }

    /**
     * ロード日時
     * @param string $load_datetime
     * @return $this
     */
    public function setLoadDatetime($load_datetime)
    {
        $this->load_datetime = $load_datetime;
        return $this;
    }

    /**
     * クローズ日時
     * @param string $close_datetime
     * @return $this
     */
    public function setCloseDatetime($close_datetime)
    {
        $this->close_datetime = $close_datetime;
        return $this;
    }

}
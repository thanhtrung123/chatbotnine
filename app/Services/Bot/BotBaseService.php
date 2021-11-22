<?php


namespace App\Services\Bot;

use App\Services\Bot\Sns\SnsUidMapService;
use App\Services\DataConvertService;

/**
 * チャットボット基本サービス
 * Class BotBaseService
 * @package App\Services\Bot
 */
class BotBaseService
{
    /**
     * @var bool デバッグモード
     */
    private $is_debug = false;
    /**
     * @var int channel
     */
    private $channel;
    /**
     * @var DataConvertService
     */
    private $data_converter_service;
    /**
     * @var BotSessionService
     */
    private $bot_session_service;
    /**
     * @var BotApiService
     */
    private $bot_api_service;
    /**
     * @var BotLogService
     */
    private $bot_log_service;
    /**
     * @var BotDbService
     */
    private $bot_db_service;
    /**
     * @var BotMorphService
     */
    private $bot_morph_service;
    /**
     * @var BotAnswerService
     */
    private $bot_answer_service;
    /**
     * @var BotScenarioService
     */
    private $bot_scenario_service;
    /**
     * @var BotBiLogService
     */
    private $bot_bi_log_service;
    /**
     * @var BotEnqueteService
     */
    private $bot_enquete_service;
    /**
     * @var SnsUidMapService
     */
    private $sns_uid_map_service;
    /**
     * @var array デバッグ情報
     */
    private $bot_info_data = [];


    /**
     * BotBaseService constructor.
     * @param DataConvertService $data_converter
     * @param BotSessionService $bot_session_service
     * @param BotApiService $bot_api_service
     * @param BotLogService $bot_log_service
     * @param BotDbService $bot_db_service
     * @param BotMorphService $bot_morph_service
     * @param BotAnswerService $bot_answer_service
     * @param BotScenarioService $bot_scenario_service
     * @param BotBiLogService $bot_bi_log_service
     * @param BotEnqueteService $bot_enquete_service
     * @param SnsUidMapService $sns_uid_map_service
     */
    public function __construct(
        DataConvertService $data_converter,
        BotSessionService $bot_session_service,
        BotApiService $bot_api_service,
        BotLogService $bot_log_service,
        BotDbService $bot_db_service,
        BotMorphService $bot_morph_service,
        BotAnswerService $bot_answer_service,
        BotScenarioService $bot_scenario_service,
        BotBiLogService $bot_bi_log_service,
        BotEnqueteService $bot_enquete_service,
        SnsUidMapService $sns_uid_map_service
    )
    {
        $this->data_converter_service = $data_converter;
        $this->bot_session_service = $bot_session_service;
        $this->bot_api_service = $bot_api_service;
        $this->bot_log_service = $bot_log_service;
        $this->bot_db_service = $bot_db_service;
        $this->bot_answer_service = $bot_answer_service;
        $this->bot_morph_service = $bot_morph_service;
        $this->bot_scenario_service = $bot_scenario_service;
        $this->bot_bi_log_service = $bot_bi_log_service;
        $this->bot_enquete_service = $bot_enquete_service;
        $this->sns_uid_map_service = $sns_uid_map_service;
        $this->channel = config('const.bot.channel.web.id');
    }


    /**
     * chat_id取得
     * @return string
     */
    public function getChatId()
    {
        return $this->getBotDbService()->getResponseInfoRepository()->getUniqueChatId();
    }

    /**
     * talk_id取得
     * @return string
     */
    public function getTalkId()
    {
        return $this->getBotDbService()->getResponseInfoRepository()->getUniqueTalkId();
    }

    /**
     * データコンバータサービス取得
     * @return DataConvertService
     */
    public function getDataConverterService(): DataConvertService
    {
        return $this->data_converter_service;
    }

    /**
     * データコンバータサービスセット
     * @param DataConvertService $data_converter_service
     * @return $this
     */
    public function setDataConverterService(DataConvertService $data_converter_service)
    {
        $this->data_converter_service = $data_converter_service;
        return $this;
    }

    /**
     * デバッグモードか
     * @return bool
     */
    public function getIsDebug(): bool
    {
        return $this->is_debug;
    }

    /**
     * デバッグモードセット
     * @param bool $is_debug
     * @return $this
     */
    public function setIsDebug(bool $is_debug)
    {
        $this->is_debug = $is_debug;
        return $this;
    }

    /**
     * channel取得
     * @return int
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * channelセット
     * @param $channel
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * チャットボット内部情報種痘
     * @param null $key
     * @return mixed
     */
    public function getBotInfoData($key = null)
    {
        return ($key === null) ? $this->bot_info_data : $this->bot_info_data[$key] ?? null;
    }

    /**
     * チャットボット内部情報セット
     * @param string $key
     * @param $bot_info_data
     * @return $this
     */
    public function setBotInfoData($key = 'debug', $bot_info_data)
    {
        $this->bot_info_data[$key] = $bot_info_data;
        return $this;
    }

    /**
     * チャットボットセッションサービス取得
     * @return BotSessionService
     */
    public function getBotSessionService(): BotSessionService
    {
        return $this->bot_session_service;
    }

    /**
     * チャットボットAPIサービス取得
     * @return BotApiService
     */
    public function getBotApiService(): BotApiService
    {
        return $this->bot_api_service;
    }

    /**
     * チャットボット応答情報サービス取得
     * @return BotLogService
     */
    public function getBotLogService(): BotLogService
    {
        return $this->bot_log_service;
    }

    /**
     * チャットボットDBサービス取得
     * @return BotDbService
     */
    public function getBotDbService(): BotDbService
    {
        return $this->bot_db_service;
    }

    /**
     * チャットボット形態素解析サービス取得
     * @return BotMorphService
     */
    public function getBotMorphService(): BotMorphService
    {
        return $this->bot_morph_service;
    }

    /**
     * チャットボット回答サービス取得
     * @return BotAnswerService
     */
    public function getBotAnswerService(): BotAnswerService
    {
        return $this->bot_answer_service;
    }

    /**
     * チャットボットシナリオサービス取得
     * @return BotScenarioService
     */
    public function getBotScenarioService(): BotScenarioService
    {
        return $this->bot_scenario_service;
    }

    /**
     * チャットボットBIツール用ログサービス取得
     * @return BotBiLogService
     */
    public function getBotBiLogService(): BotBiLogService
    {
        return $this->bot_bi_log_service;
    }

    /**
     * チャットボットアンケートサービス取得
     * @return BotEnqueteService
     */
    public function getBotEnqueteService(): BotEnqueteService
    {
        return $this->bot_enquete_service;
    }

    /**
     * @return SnsUidMapService
     */
    public function getSnsUidMapService(): SnsUidMapService
    {
        return $this->sns_uid_map_service;
    }

}
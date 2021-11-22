<?php


namespace App\Services\Admin;


use App\Services\Bot\BotApiService;
use App\Services\Bot\BotBaseService;
use App\Services\Bot\BotMorphService;
use App\Services\Bot\BotScenarioService;
use App\Services\Bot\BotTruthService;

/**
 * 裏ツールサービス
 * Class ToolsService
 * @package App\Services\Admin
 */
class ToolsService
{
    /**
     * @var LearningService
     */
    private $learning_service;
    /**
     * @var BotTruthService
     */
    private $truth_service;
    /**
     * @var BotApiService
     */
    private $api_service;
    /**
     * @var BotMorphService
     */
    private $morph_service;
    /**
     * @var BotScenarioService
     */
    private $scenario_service;
    /**
     * @var \App\Services\Bot\BotDbService
     */
    private $db_service;

    /**
     * ToolsService constructor.
     * @param LearningService $learning_service
     * @param BotTruthService $truth_service
     * @param BotBaseService $bot_base_service
     * @param BotScenarioService $scenario_service
     */
    public function __construct(LearningService $learning_service, BotTruthService $truth_service, BotBaseService $bot_base_service, BotScenarioService $scenario_service)
    {
        $this->learning_service = $learning_service;
        $this->truth_service = $truth_service;
        $this->api_service = $bot_base_service->getBotApiService();
        $this->morph_service = $bot_base_service->getBotMorphService();
        $this->scenario_service = $scenario_service;
        $this->db_service = $bot_base_service->getBotDbService();
    }


    /**
     * 学習データサービス取得
     * @return LearningService
     */
    public function getLearningService(): LearningService
    {
        return $this->learning_service;
    }

    /**
     * 真理表サービス取得
     * @return BotTruthService
     */
    public function getTruthService(): BotTruthService
    {
        return $this->truth_service;
    }

    /**
     * APIサービス取得
     * @return BotApiService
     */
    public function getApiService(): BotApiService
    {
        return $this->api_service;
    }

    /**
     * 形態素解析サービス取得
     * @return BotMorphService
     */
    public function getMorphService(): BotMorphService
    {
        return $this->morph_service;
    }

    /**
     * シナリオサービス取得
     * @return BotScenarioService
     */
    public function getScenarioService(): BotScenarioService
    {
        return $this->scenario_service;
    }

    /**
     * チャットボットDBサービス取得
     * @return \App\Services\Bot\BotDbService
     */
    public function getDbService(): \App\Services\Bot\BotDbService
    {
        return $this->db_service;
    }


}
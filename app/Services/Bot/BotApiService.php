<?php

namespace App\Services\Bot;

use App\Services\Bot\Api\ApiService;
use Util;

/**
 * チャットボット用 APIサービス
 * Class BotApiService
 * @package App\Services\Bot
 */
class BotApiService
{
    //プロパティ
    /**
     * @var string 設定値接頭辞
     */
    private $config_prefix_api;
    /**
     * @var ApiService
     */
    private $api_service;
    /** @var Api\ApiInterface */
    private $api;

    /**
     * BotApiService constructor.
     * @param ApiService $api_service
     * @throws \Exception
     */
    public function __construct(ApiService $api_service)
    {
        $this->api_service = $api_service;
        $this->setConfig(config('bot.api.use'));
        $this->api = $this->api_service->getService(config('bot.api.default.service'));
    }

    /**
     * API取得
     * @return Api\ApiInterface|\App\Services\type
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * 設定
     * @param string $use_api 使用するAPI名
     * @return $this
     */
    public function setConfig($use_api)
    {
        $this->config_prefix_api = 'bot.api.' . $use_api;
        //設定を上書き
        Util::overrideConfig($this->config_prefix_api, 'bot.api.default');
        return $this;
    }

}
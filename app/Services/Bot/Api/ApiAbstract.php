<?php

namespace App\Services\Bot\Api;

use App\Services\DataConvertService;
use GuzzleHttp\Client;

/**
 * チャットボットAPIサービス抽象クラス
 * Class ApiAbstract
 * @package App\Services\Bot\Api
 */
abstract class ApiAbstract
{
    /* @var $client Client */
    protected $client;
    /**
     * @var array パラメータ
     */
    protected $params;
    /**
     * @var 結果配列
     */
    protected $result;
    /* @var DataConvertService */
    protected $converter;

    /**
     * ApiAbstract constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->params = [];
    }

    /**
     * データコンバータセット
     * @param DataConvertService $converter
     * @return ApiInterface
     */
    public function setConverter(DataConvertService $converter): ApiInterface
    {
        $this->converter = $converter;
        return $this;
    }

    /**
     * データコンバータ取得
     * @return DataConvertService
     */
    public function getConverter(): DataConvertService
    {
        return $this->converter;
    }

}
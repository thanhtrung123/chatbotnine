<?php

namespace App\Services\Bot\Api;

use App\Services\DataConvertService;

/**
 * チャットボットAPIサービスインターフェース
 * Interface ApiInterface
 * @package App\Services\Bot\Api
 */
interface ApiInterface
{

    /**
     * API問い合わせ
     * @param array $options
     * @return ApiInterface
     */
    public function inquiry(array $options = []): self;

    /**
     * パラメータセット
     * @param array $params
     * @param null $content
     * @return ApiInterface
     */
    public function setParams(array $params, $content = null): self;

    /**
     * APIバージョン取得
     * @return array
     */
    public function getVersion();

    /**
     * 問い合わせ結果
     * @param array $options
     * @return App\Services\Bot\Api\ApiResult[]
     */
    public function getResult(array $options = []): array;

    /**
     * 学習データ取得
     * @return \Generator
     */
    public function getLearningData(): \Generator;

    /**
     * 学習データ追加
     * @param $learning_datas
     * @return mixed
     */
    public function addLearningData($learning_datas);

    /**
     * 学習データ削除
     * @return mixed
     */
    public function deleteLearningData();

    /**
     * 学習データ公開
     * @return mixed
     */
    public function publishLearningData();

    /**
     * データコンバータセット
     * @param DataConvertService $converter
     * @return $this
     */
    public function setConverter(DataConvertService $converter): self;

    /**
     * データコンバータ取得
     * @return DataConvertService
     */
    public function getConverter(): DataConvertService;

}
<?php

namespace App\Repositories\ResponseInfo;

use App\Repositories\RepositoryInterface;

/**
 * 応答情報リポジトリインターフェース
 * Interface ResponseInfoRepositoryInterface
 * @package App\Repositories\ResponseInfo
 */
interface ResponseInfoRepositoryInterface extends RepositoryInterface
{

    /**
     * 応答情報追加
     * @param array $data
     * @return bool
     */
    public function saveLog(array $data): bool;

    /**
     * 利用者数
     * @return mixed
     */
    public function getUserCount();

    /**
     * 質問数
     * @return mixed
     */
    public function getQuestionCount();

    /**
     * 年月日フィルタ
     * @return $this
     */
    public function filterDate(): self;

    /**
     * 集計フィルタ
     * @param $base
     * @param $type
     * @param $date
     * @return $this
     */
    public function filterAggregate($base, $type, $date): self;

    /**
     * パラメータでフィルタリング実行（情報一覧用）
     * @return $this
     */
    public function filterByParamsForInfoList(): self;

    /**
     * 情報一覧用フィルタ
     * @return $this
     */
    public function filterInfoList(): self;

    /**
     * エクスポートフィルタ
     * @return $this
     */
    public function filterExport(): self;
    
    /**
     * ユニークなchat_idを取得
     * @return string
     */
    public function getUniqueChatId();

    /**
     * ユニークなtalk_idを取得
     * @return string
     */
    public function getUniqueTalkId();
    
    /**
     * Get data chat id folow date
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $ip
     * @param $format
     * @param $channel
     * 
     * @return mixed
     */
    public function getChatIdDate($start_date_data, $end_date_data, $ip, $format, $channel);
    
    /**
     * Get data talk id
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $ip
     * @param $format
     * @param $channel
     * 
     * @return mixed
     */
    public function getTalkId($start_date_data, $end_date_data, $ip, $format, $channel);

    /**
     * Get number total
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $ip
     * @param $channel
     * 
     * @return int
     */
    public function getTotalDist($start_date_data, $end_date_data, $ip, $channel);

    /**
     * Get data Answer
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataAnswer($start_date_data, $end_date_data, $config, $ip, $channel);
    
    /**
     * Get data No Answer
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataNoAnswer($start_date_data, $end_date_data, $config, $ip, $channel);

    /**
     * Get number Answer
     * 
     * @param $dataAnswer
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return int
     */
    public function getDataAnswerHandle($start_date_data, $end_date_data, $config, $ip, $channel);

    /**
     * Get data Answer Limit
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataAnswerLimit($start_date_data, $end_date_data, $config, $ip, $channel);

    /**
     * Get data No Answer Limit
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataNoAnswerLimit($start_date_data, $end_date_data, $config, $ip, $channel);
}
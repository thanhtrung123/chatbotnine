<?php

namespace App\Repositories\ResponseAggregate;

use App\Repositories\RepositoryInterface;

/**
 * 応答情報集計リポジトリインターフェース
 * Interface ResponseAggregateRepositoryInterface
 * @package App\Repositories\ResponseAggregate
 */
interface ResponseAggregateRepositoryInterface extends RepositoryInterface
{
    /**
     * 集計用フィルタ
     * @param $type
     * @return $this
     */
    public function filterAggregate($type);

    /**
     * 概要用フィルタ
     * @param $type
     * @return $this
     */
    public function filterOverview($type);

    /**
     * エラーフィルタ
     * @return $this
     */
    public function filterError();

}
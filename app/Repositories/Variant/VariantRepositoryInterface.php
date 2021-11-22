<?php

namespace App\Repositories\Variant;

use App\Repositories\DbResultInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\type;

/**
 * 異表記リポジトリインターフェース
 * Interface VariantRepositoryInterface
 * @package App\Repositories\Variant
 */
interface VariantRepositoryInterface extends RepositoryInterface
{

    /**
     * キーワードで絞込み
     * @param $keyword
     * @return mixed
     */
    public function findByKeyword($keyword);

    /**
     * メッセージから絞り込み
     * @param $message
     * @return DbResultInterface
     */
    public function findByMessage($message): DbResultInterface;
}
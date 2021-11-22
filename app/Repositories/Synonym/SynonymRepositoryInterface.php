<?php

namespace App\Repositories\Synonym;

use App\Repositories\DbResultInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\type;

/**
 * 類義語リポジトリインターフェース
 * Interface SynonymRepositoryInterface
 * @package App\Repositories\Synonym
 */
interface SynonymRepositoryInterface extends RepositoryInterface
{
    /**
     * キーワードで絞り込み
     * @param string $keyword
     */
    public function findByKeyword($keyword);

    /**
     * メッセージから絞り込み
     * @param $message
     * @param null $original_message
     * @return DbResultInterface
     */
    public function findByMessage($message, $original_message = null): DbResultInterface;
}
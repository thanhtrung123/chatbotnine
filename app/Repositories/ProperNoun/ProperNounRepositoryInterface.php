<?php

namespace App\Repositories\ProperNoun;

use App\Repositories\DbResultInterface;
use App\Repositories\RepositoryInterface;

/**
 * 固有名詞リポジトリインターフェース
 */
interface ProperNounRepositoryInterface extends RepositoryInterface
{
    /**
     * メッセージから検索
     * @param $message
     * @return DbResultInterface
     */
    public function findByMessage($message): DbResultInterface;

    /**
     * 次の固有名詞IDを取得
     * @return mixed
     */
    public function getNextProperNounId();
}
<?php

namespace App\Repositories\StopWords;

use App\Repositories\RepositoryInterface;
use App\Repositories\type;

/**
 * ストップワードリポジトリインターフェース
 * Interface StopWordsRepositoryInterface
 * @package App\Repositories\StopWords
 */
interface StopWordsRepositoryInterface extends RepositoryInterface
{

    /**
     * 存在するか
     * @param $word
     * @return bool
     */
    public function exists($word): bool;
}
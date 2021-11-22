<?php

namespace App\Repositories\Log;

use App\Repositories\RepositoryInterface;

/**
 * ログリポジトリインターフェース
 * Interface LogRepositoryInterface
 * @package App\Repositories\Log
 */
interface LogRepositoryInterface extends RepositoryInterface
{
    /**
     * ログ追加
     * @param $data
     * @return mixed
     */
    public function insertLog($data);
}
<?php

namespace App\Services\Admin;

/**
 * 管理者サービスインターフェース
 * Interface AdminServiceInterface
 * @package App\Services\Admin
 */
interface AdminServiceInterface
{

    /**
     * ログ記録
     * @param $processing
     * @return mixed
     */
    public function saveLog($processing);
}
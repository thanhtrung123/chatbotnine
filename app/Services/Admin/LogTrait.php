<?php

namespace App\Services\Admin;

use Request;
use App\Repositories\Log\LogRepositoryInterface;

/**
 * ログ用トレイト
 * Trait LogTrait
 * @package App\Services\Admin
 */
trait LogTrait
{

    /**
     * ログ保存
     * @param $processing
     */
    public function saveLog($processing)
    {
        /* @var $user \App\Models\User */
        $user = Request::user();
        $roles = implode(',', $user->getRoleNames()->toArray());
        app(LogRepositoryInterface::class)->insertLog([
            'user_id' => $user->id,
            'user_name' => $user->display_name,
            'user_role' => $roles,
            'session_id' => Request::session()->getId(),
            'processing' => $processing,
        ]);
    }
}
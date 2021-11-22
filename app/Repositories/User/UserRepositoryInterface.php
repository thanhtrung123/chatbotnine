<?php

namespace App\Repositories\User;

use App\Repositories\RepositoryInterface;

/**
 * アカウントリポジトリインターフェース
 * Interface UserRepositoryInterface
 * @package App\Repositories\User
 */
interface UserRepositoryInterface extends RepositoryInterface
{

    /**
     * アカウント登録
     * @param $data
     * @return mixed
     */
    public function createUser($data);

    /**
     * アカウント更新
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateUser($id, $data);

    /**
     * モデル取得
     * @param $id
     * @return \App\Models\User
     */
    public function getUserModel($id): \App\Models\User;

}
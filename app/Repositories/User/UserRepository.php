<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use App\Repositories\type;
use App\Repositories\User\UserRepositoryInterface;
use DB;

/**
 * アカウントリポジトリ
 * Class UserRepository
 * @package App\Repositories\User
 */
class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return User::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|void
     */
    public function filterByParams(): RepositoryInterface
    {
        if (isset($this->params['search']['value'])) {
            $this->byKeyword($this->params['search']['value'], ['email', 'name', 'display_name']);
        }
        return $this;
    }

    /**
     * アカウント追加
     * @param $data
     * @return mixed
     */
    public function createUser($data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'remember_token' => str_random(10),
        ]);
    }

    /**
     * アカウント更新
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateUser($id, $data)
    {
        if (empty($data['password'])) unset($data['password']);
        else $data['password'] = bcrypt($data['password']);
        return $this->model->find($id)->update($data);
    }

    /**
     * モデル取得
     * @param $id
     * @return User
     */
    public function getUserModel($id): User
    {
        return $this->model->find($id);
    }
}
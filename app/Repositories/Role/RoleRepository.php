<?php

namespace App\Repositories\Role;

use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\type;
use Spatie\Permission\Models\Role;
use App\Repositories\Traits\ByKeyword;
use DB;

/**
 * 権限リポジトリ
 * Class RoleRepository
 * @package App\Repositories\Role
 */
class RoleRepository extends AbstractRepository implements RoleRepositoryInterface
{
    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return Role::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return RepositoryInterface
     */
    public function filterByParams(): RepositoryInterface
    {
        return $this;
    }


}
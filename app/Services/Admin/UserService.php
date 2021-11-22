<?php

namespace App\Services\Admin;

use App\Repositories\RepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\RepositoryServiceInterface;
use App\Services\AclService;

/**
 * アカウントサービス
 * Class UserService
 * @package App\Services\Admin
 */
class UserService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var UserRepositoryInterface
     */
    private $repository;
    /**
     * @var AclService
     */
    private $acl_service;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $repository
     * @param AclService $acl_service
     */
    public function __construct(UserRepositoryInterface $repository, AclService $acl_service)
    {
        $this->repository = $repository;
        $this->acl_service = $acl_service;
    }

    /**
     * リポジトリ取得
     * @return UserRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * ロール名取得
     * @param integer $user_id ユーザID
     * @return array
     */
    public function getRoleNames($user_id)
    {
        $roles = $this->repository->getUserModel($user_id)->getRoleNames()->toArray();
        return $roles;
    }

    /**
     * ロール配列取得
     * @param integer|null $id ユーザID
     * @return array
     */
    public function getRoleArray($id = null)
    {
        $result = [];
        $has_roles = ($id === null) ? [] : $this->getRoleNames($id);
        $roles = $this->acl_service->getRoleNames();
        foreach ($roles as $role => $role_name) {
            $result[$role] = [
                'display_name' => $role_name,
                'checked' => in_array($role, $has_roles),
            ];
        }
        return $result;
    }

    /**
     * ロール更新
     * @param integer $id ユーザID
     * @param array $roles ロール配列
     */
    public function updateRole($id, $roles)
    {
        $user = $this->repository->getUserModel($id);
        $enable_roles = [];
        foreach ($roles as $role => $flag) {
            if ($flag == '0') continue;
            $enable_roles[] = $role;
        }
        $user->syncRoles($enable_roles);
    }
}
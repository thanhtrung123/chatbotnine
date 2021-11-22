<?php

namespace App\Services\Admin;

use App\Repositories\RepositoryInterface;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Services\RepositoryServiceInterface;
use App\Services\AclService;

/**
 * 権限サービス
 * Class RoleService
 * @package App\Services\Admin
 */
class RoleService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;

    /**
     * @var RoleRepositoryInterface
     */
    private $repository;
    /**
     * @var AclService
     */
    private $acl_service;

    /**
     * RoleService constructor.
     * @param RoleRepositoryInterface $repository
     * @param AclService $acl_service
     */
    public function __construct(RoleRepositoryInterface $repository, AclService $acl_service)
    {
        $this->repository = $repository;
        $this->acl_service = $acl_service;
    }

    /**
     * リポジトリを取得
     * @return RoleRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * ACLサービスを取得
     * @return AclService
     */
    public function getAclService()
    {
        return $this->acl_service;
    }

    /**
     * 許可設定フォーム用配列を取得
     * @param null|string $role ロール名
     * @return array
     */
    public function getPermissionArray($role = null)
    {
        $result = [];
        if ($role !== null) {
            $now_permissions = $this->acl_service->getPermissionNames($role);
        }
        $all_permissions = $this->acl_service->getSplitPermissions();
        foreach ($all_permissions['resources'] as $resource => $resourceName) {
            $result[$resource] = [
                'display_name' => $resourceName,
                'privileges' => [],
            ];
            foreach ($all_permissions['privileges'] as $privilege => $privilegeName) {
                $result[$resource]['privileges'][$privilege] = [
                    'display_name' => $privilegeName,
                    'checked' => ($role === null) ? false : (isset($now_permissions["{$resource} {$privilege}"]) ? true : false),
                ];
            }
        }
        return $result;
    }

    /**
     * ロール作成
     * @param array $params パラメータ
     */
    public function createRole($params)
    {
        //create role
        $this->acl_service->createRole($params['name'], $params['display_name']);
        $this->updatePermission($params);
    }

    /**
     * ロール更新
     * @param integer $id ロールID
     * @param array $params パラメータ
     */
    public function updateRole($id, $params)
    {
        //update role
        $this->acl_service->updateRole($id, ['name' => $params['name'], 'display_name' => $params['display_name']]);
        $this->updatePermission($params);
    }

    /**
     * 許可更新
     * @param array $params パラメータ
     */
    private function updatePermission($params)
    {
        $role = $this->acl_service->getRoleByName($params['name']);
        $this->acl_service->clearPermission($params['name']);
        foreach ($params['permission'] as $permission => $enabled) {
            if ($enabled == "0") continue;
            $permission = str_replace(':', ' ', $permission);
            $role->givePermissionTo($permission);
        }
    }
}
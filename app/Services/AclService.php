<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * ACLサービス（権限）
 * Class AclService
 * @package App\Services
 */
class AclService
{
    //FIXME:guardに未対応（webのみ）
    /**
     * @var string
     */
    private $guard = 'web';

    /**
     * リソース名の配列を取得
     * @param null|string $role ロール名
     * @return mixed
     */
    public function getResources($role = null)
    {
        return $this->getSplitPermissions($role)['resources'];
    }

    /**
     * 特権名の配列を取得
     * @return array
     */
    public function getPrivileges()
    {
        return $this->getSplitPermissions()['privileges'];
    }

    /**
     * 許可の配列をリソースと特権に分離
     * @param null|string $role ロール名
     * @return array
     */
    public function getSplitPermissions($role = null)
    {
        $permissions = $this->getPermissionNames($role);
        $privileges = $resources = [];
        foreach ($permissions as $permission => $permissionName) {
            list($resource, $privilege) = explode(' ', $permission);
            list($resourceName, $privilegeName) = explode(' ', $permissionName);
            $resources[$resource] = $resourceName;
            $privileges[$privilege] = $privilegeName;
        }
        return [
            'resources' => $resources,
            'privileges' => $privileges,
        ];
    }

    /**
     * 許可名の配列を取得
     * @param null|string $role ロール名
     * @return array
     */
    public function getPermissionNames($role = null)
    {
        if ($role === null) {
            $permissions = Permission::query()->where('guard_name', $this->guard)->get()->toArray();
        } else {
            $permissions = Role::findByName($role)->permissions()->get()->toArray();
        }
        return $this->convertArray($permissions);
    }

    /**
     * 役割名の配列を取得
     * @return array
     */
    public function getRoleNames()
    {
        $roles = Role::query()->where('guard_name', $this->guard)->get()->toArray();
        return $this->convertArray($roles);
    }

    /**
     * 役割モデルの配列を取得
     * @return array
     */
    public function getRoles()
    {
        return Role::all()->toArray();
    }

    /**
     * 役割モデルを取得（名前から）
     * @param string $role ロール名
     * @return \Spatie\Permission\Contracts\Role|Role
     */
    public function getRoleByName($role)
    {
        return Role::findByName($role);
    }

    /**
     * 役割モデルを取得（IDから）
     * @param integer $id ロールID
     * @return \Spatie\Permission\Contracts\Role
     */
    public function getRoleById($id)
    {
        return Role::findById($id);
    }

    /**
     * 許可モデルを取得（名前から）
     * @param string $permission パーミッション名
     * @return \Spatie\Permission\Contracts\Permission
     */
    public function getPermissionByName($permission)
    {
        return Permission::findByName($permission);
    }

    /**
     * DB配列を変換
     * @param array $dbArray
     * @return array
     */
    private function convertArray($dbArray)
    {
        $result = [];
        foreach ($dbArray as $row) {
            $result[$row['name']] = $row['display_name'];
        }
        return $result;
    }

    /**
     * リソースを作成
     * @param string $resource リソース名
     * @param string $resourceName リソース表示名
     */
    public function createResource($resource, $resourceName)
    {
        $privileges = $this->getPrivileges();
        foreach ($privileges as $privilege => $privilegeName) {
            $this->createPermission("{$resource} {$privilege}", "{$resourceName} {$privilegeName}");
        }
    }

    /**
     * 特権を作成
     * @param string $privilege 特権名
     * @param string $privilegeName 特権表示名
     */
    public function createPrivilege($privilege, $privilegeName)
    {
        $resources = $this->getResources();
        foreach ($resources as $resource => $resourceName) {
            $this->createPermission("{$resource} {$privilege}", "{$resourceName} {$privilegeName}");
        }
    }

    /**
     * 許可を作成
     * @param string $name パーミッション名
     * @param string $display_name パーミッション表示名
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function createPermission($name, $display_name)
    {
        return Permission::create(['name' => $name, 'display_name' => $display_name]);
    }

    /**
     * 役割を作成
     * @param string $role ロール名
     * @param string $roleName ロール表示名
     */
    public function createRole($role, $roleName)
    {
        Role::create(['name' => $role, 'display_name' => $roleName]);
    }

    /**
     * 役割を更新
     * @param integer $id ロールID
     * @param array $params 更新パラメータ
     */
    public function updateRole($id, $params)
    {
        Role::findById($id)->update($params);
    }

    /**
     * リソースを削除
     * @param string $resource リソース名
     */
    public function deleteResource($resource)
    {
        Permission::query()->where('name', 'like', "{$resource} %")->delete();
    }

    /**
     * 特権を削除
     * @param string $privilege 特権名
     */
    public function deletePrivilege($privilege)
    {
        Permission::query()->where('name', 'like', "% {$privilege}")->delete();
    }

    /**
     * 許可を削除
     * @param string $name パーミッション名
     */
    public function deletePermission($name)
    {
        Permission::query()->where('name', 'like', $name)->delete();
    }

    /**
     * 役割を削除
     * @param string $role ロール名
     * @throws \Exception
     */
    public function deleteRole($role)
    {
        Role::findByName($role)->delete();
    }

    /**
     * 役割に紐づいた許可をクリア
     * @param string $role ロール名
     */
    public function clearPermission($role)
    {
        $this->getRoleByName($role)->revokePermissionTo(array_keys($this->getPermissionNames()));
    }
}
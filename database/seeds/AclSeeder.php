<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AclSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Schema::disableForeignKeyConstraints();
        foreach (config('permission.table_names') as $key => $tbl) {
            DB::table($tbl)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $resources = config('acl.resources');
        $privileges = config('acl.privileges');

        $permissions = [];
        foreach ($resources as $resource => $resourceName) {
            foreach ($privileges as $privilege => $privilegeName) {
                $permissions["{$resource} {$privilege}"] = "{$resourceName} {$privilegeName}";
            }
        }
        foreach ($permissions as $permission => $dispName) {
            Permission::create(['name' => $permission, 'display_name' => $dispName]);
        }

        $roles = config('acl.roles');
        foreach ($roles as $role => $dispName) {
            Role::create(['name' => $role, 'display_name' => $dispName]);
        }

        $role = Role::findByName('admin');
        $role->givePermissionTo(array_keys($permissions));

        if (env('APP_DEBUG')) {
            Role::create(['name' => 'test', 'display_name' => 'テスト']);
        }

    }
}
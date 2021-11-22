<?php

namespace App\Console\Commands\Common;

use App\Services\AclService;
use Illuminate\Console\Command;
use Artisan;

/**
 * ACL用コマンド
 * Class AclCommand
 * @package App\Console\Commands\Common
 */
class AclCommand extends Command
{
    /**
     * モード：再適用
     */
    const MODE_FRESH = 'fresh';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'common:acl {mode=fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ACL control command';

    /**
     * @var AclService
     */
    private $acl_service;

    /**
     * AclCommand constructor.
     * @param AclService $acl_service
     */
    public function __construct(AclService $acl_service)
    {
        $this->acl_service = $acl_service;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $mode = $this->argument('mode');
        switch ($mode) {
            case self::MODE_FRESH:
                $this->freshAcl();
                break;
            default:
                $this->error('NOT SUPPORT COMMAND!');
                break;
        }
    }

    /**
     * 権限差分適用（設定値に合わせる）
     */
    private function freshAcl()
    {
        //キャッシュ削除
        Artisan::call('permission:cache-reset');
        //設定ファイルからACLの差分を適用
        $db_permissions = array_keys($this->acl_service->getPermissionNames());
        $resources = config('acl.resources');
        $privileges = config('acl.privileges');
        $admin_role = $this->acl_service->getRoleByName('admin');
        foreach ($resources as $resource => $resourceName) {
            foreach ($privileges as $privilege => $privilegeName) {
                $conf_key = "{$resource} {$privilege}";
                $conf_name = "{$resourceName} {$privilegeName}";
                if (in_array($conf_key, $db_permissions)) {
                    unset($db_permissions[array_search($conf_key, $db_permissions)]);
                    continue;
                }
                $this->info("Add Permission [{$conf_key}]");
                //パーミッション追加
                $this->acl_service->createPermission($conf_key, $conf_name);
                $admin_role->givePermissionTo($conf_key);
            }
        }
        foreach ($db_permissions as $key) {
            $this->alert("Remove Permission [{$key}]");
            //パーミッション削除
            $this->acl_service->deletePermission($key);
        }
    }

}

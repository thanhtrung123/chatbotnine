<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UtilService;

/**
 * ユーティリティサービスプロバイダー
 * Class UtilServiceProvider
 * @package App\Providers
 */
class UtilServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('util', function () {
            return app(UtilService::class);
        });
    }
}
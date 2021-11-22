<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ConstService;

/**
 * 定数サービスプロバイダー
 * Class ConstServiceProvider
 * @package App\Providers
 */
class ConstServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('constant', function () {
            return app(ConstService::class);
        });
    }
}
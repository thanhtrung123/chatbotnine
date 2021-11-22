<?php

namespace App\Providers;

use App\Services\Admin\LearningService;
use App\Services\Bot\Api\DemoService;
use Illuminate\Support\ServiceProvider;
use App\Services\Bot\Morph\MorphService;
use App\Services\Bot\Morph\MecabService;
use App\Services\Bot\Api\ApiService;
use App\Services\Bot\Api\QnaService;
use GuzzleHttp\ClientInterface;

/**
 * チャットボットサービスプロバイダー
 * Class BotServiceProvider
 * @package App\Providers
 */
class BotServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('bot.api.use') == 'demo') {
            app(DemoService::class)->setLearningService(app(LearningService::class));
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Morph
        $morph_tags = [];
        //MeCab
        $this->app->singleton(MecabService::class, function ($app) {
            return new MecabService(config('bot.morph.mecab'));
        });
        $morph_tags[] = MecabService::class;
        //タグをセット
        $this->app->tag($morph_tags, 'morph');
        //サービスマネージャーに登録
        $this->app->singleton(MorphService::class, function ($app) {
            return new MorphService($app->tagged('morph'));
        });

        //Api
        $api_tags = [];
        //QnaMaker
        $this->app->singleton(QnaService::class, function ($app) {
            return new QnaService($app->make(ClientInterface::class));
        });
        $api_tags[] = QnaService::class;
        //Demo
        if (config('bot.api.use') == 'demo') {
            $this->app->singleton(DemoService::class, function ($app) {
                return new DemoService($app->make(ClientInterface::class));
            });
            $api_tags[] = DemoService::class;
        }
        //タグをセット
        $this->app->tag($api_tags, 'api');
        //サービスマネージャーに登録
        $this->app->singleton(ApiService::class, function ($app) {
            return new ApiService($app->tagged('api'));
        });
    }
}
<?php

namespace App\Providers;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\EnqueteAnswer\EnqueteAnswerRepository;
use App\Repositories\EnqueteAnswer\EnqueteAnswerRepositoryInterface;
use App\Repositories\LearningRelation\LearningRelationRepository;
use App\Repositories\LearningRelation\LearningRelationRepositoryInterface;
use App\Repositories\ProperNoun\ProperNounRepository;
use App\Repositories\ProperNoun\ProperNounRepositoryInterface;
use App\Repositories\ResponseAggregate\ResponseAggregateRepository;
use App\Repositories\ResponseAggregate\ResponseAggregateRepositoryInterface;
use App\Repositories\ResponseInfo\ResponseInfoRepository;
use App\Repositories\ResponseInfo\ResponseInfoRepositoryInterface;
use App\Repositories\Learning\LearningRepository;
use App\Repositories\Learning\LearningRepositoryInterface;
use App\Repositories\ResponseInfoUser\ResponseInfoUserRepository;
use App\Repositories\ResponseInfoUser\ResponseInfoUserRepositoryInterface;
use App\Repositories\Scenario\ScenarioRepository;
use App\Repositories\Scenario\ScenarioRepositoryInterface;
use App\Repositories\ScenarioKeyword\ScenarioKeywordRepository;
use App\Repositories\ScenarioKeyword\ScenarioKeywordRepositoryInterface;
use App\Repositories\ScenarioKeywordRelation\ScenarioKeywordRelationRepository;
use App\Repositories\ScenarioKeywordRelation\ScenarioKeywordRelationRepositoryInterface;
use App\Repositories\ScenarioLearningRelation\ScenarioLearningRelationRepository;
use App\Repositories\ScenarioLearningRelation\ScenarioLearningRelationRepositoryInterface;
use App\Repositories\ScenarioRelation\ScenarioRelationRepository;
use App\Repositories\ScenarioRelation\ScenarioRelationRepositoryInterface;
use App\Repositories\Synonym\SynonymRepository;
use App\Repositories\Synonym\SynonymRepositoryInterface;
use App\Repositories\Variant\VariantRepository;
use App\Repositories\Variant\VariantRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Log\LogRepositoryInterface;
use App\Repositories\Log\LogRepository;
use App\Repositories\Truth\TruthRepositoryInterface;
use App\Repositories\Truth\TruthRepository;
use App\Repositories\StopWords\StopWordsRepositoryInterface;
use App\Repositories\StopWords\StopWordsRepository;
use App\Repositories\ResponseInfoTruth\ResponseInfoTruthRepositoryInterface;
use App\Repositories\ResponseInfoTruth\ResponseInfoTruthRepository;
use App\Repositories\KeyPhrase\KeyPhraseRepositoryInterface;
use App\Repositories\KeyPhrase\KeyPhraseRepository;
use App\Repositories\SnsUidMap\SnsUidMapRepository;
use App\Repositories\SnsUidMap\SnsUidMapRepositoryInterface;
use App\Repositories\ImageInformation\ImageInformationRepositoryInterface;
use App\Repositories\ImageInformation\ImageInformationRepository;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use LINE\LINEBot;

/**
 * 基本サービスプロバイダ
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \URL::forceScheme('https');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * リポジトリ登録
         */
        //ユーザ
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        //レスポンス情報
        $this->app->bind(ResponseInfoRepositoryInterface::class, ResponseInfoRepository::class);
        //質問
        $this->app->bind(LearningRepositoryInterface::class, LearningRepository::class);
        //類語
        $this->app->bind(SynonymRepositoryInterface::class, SynonymRepository::class);
        //異表記
        $this->app->bind(VariantRepositoryInterface::class, VariantRepository::class);
        //固有名詞
        $this->app->bind(ProperNounRepositoryInterface::class, ProperNounRepository::class);
        //ロール
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        //管理ログ
        $this->app->bind(LogRepositoryInterface::class, LogRepository::class);
        //レスポンス集計
        $this->app->bind(ResponseAggregateRepositoryInterface::class, ResponseAggregateRepository::class);
        //真理表
        $this->app->bind(TruthRepositoryInterface::class, TruthRepository::class);
        //ストップワード
        $this->app->bind(StopWordsRepositoryInterface::class, StopWordsRepository::class);
        //レスポンス情報(真理表)
        $this->app->bind(ResponseInfoTruthRepositoryInterface::class, ResponseInfoTruthRepository::class);
        //レスポンス情報(ユーザー)
        $this->app->bind(ResponseInfoUserRepositoryInterface::class, ResponseInfoUserRepository::class);
        //キーフレーズリスト
        $this->app->bind(KeyPhraseRepositoryInterface::class, KeyPhraseRepository::class);
        //カテゴリ
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        //シナリオ
        $this->app->bind(ScenarioRepositoryInterface::class, ScenarioRepository::class);
        //シナリオ(親子関係)
        $this->app->bind(ScenarioRelationRepositoryInterface::class, ScenarioRelationRepository::class);
        //シナリオ学習データ(関係テーブル)
        $this->app->bind(ScenarioLearningRelationRepositoryInterface::class, ScenarioLearningRelationRepository::class);
        //シナリオキーワード
        $this->app->bind(ScenarioKeywordRepositoryInterface::class, ScenarioKeywordRepository::class);
        //シナリオキーワード(関係テーブル)
        $this->app->bind(ScenarioKeywordRelationRepositoryInterface::class, ScenarioKeywordRelationRepository::class);
        //学習データ(関連テーブル)
        $this->app->bind(LearningRelationRepositoryInterface::class, LearningRelationRepository::class);
        //アンケート
        $this->app->bind(EnqueteAnswerRepositoryInterface::class, EnqueteAnswerRepository::class);
        //Webhook Hash
        $this->app->bind(SnsUidMapRepositoryInterface::class, SnsUidMapRepository::class);
        // Image
        $this->app->bind(ImageInformationRepositoryInterface::class, ImageInformationRepository::class);


        /**
         * ライブラリ登録
         */
        //GuzzleHttpサービス
        $this->app->bind(ClientInterface::class, Client::class);

    }
}
<?php

namespace App\Services\Bot;

use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Learning\LearningRepositoryInterface;
use App\Repositories\LearningRelation\LearningRelationRepositoryInterface;
use App\Repositories\ProperNoun\ProperNounRepositoryInterface;
use App\Repositories\ResponseInfo\ResponseInfoRepositoryInterface;
use App\Repositories\Scenario\ScenarioRepositoryInterface;
use App\Repositories\ScenarioKeyword\ScenarioKeywordRepositoryInterface;
use App\Repositories\ScenarioKeywordRelation\ScenarioKeywordRelationRepositoryInterface;
use App\Repositories\ScenarioLearningRelation\ScenarioLearningRelationRepositoryInterface;
use App\Repositories\ScenarioRelation\ScenarioRelationRepositoryInterface;
use App\Repositories\Synonym\SynonymRepositoryInterface;
use App\Repositories\Variant\VariantRepositoryInterface;

/**
 * チャットボットDBサービス
 * Class BotDbService
 * @package App\Services\Bot
 */
class BotDbService
{
    /**
     * @var VariantRepositoryInterface
     */
    private $variant_repository;
    /**
     * @var SynonymRepositoryInterface
     */
    private $synonym_repository;
    /**
     * @var LearningRepositoryInterface
     */
    private $learning_repository;
    /**
     * @var ProperNounRepositoryInterface
     */
    private $proper_noun_repository;
    /**
     * @var CategoryRepositoryInterface
     */
    private $category_repository;
    /**
     * @var ScenarioRepositoryInterface
     */
    private $scenario_repository;
    /**
     * @var ScenarioRelationRepositoryInterface
     */
    private $scenario_relation_repository;
    /**
     * @var ScenarioLearningRelationRepositoryInterface
     */
    private $scenario_learning_relation_repository;
    /**
     * @var ScenarioKeywordRepositoryInterface
     */
    private $scenario_keyword_repository;
    /**
     * @var ScenarioKeywordRelationRepositoryInterface
     */
    private $scenario_keyword_relation_repository;
    /**
     * @var LearningRelationRepositoryInterface
     */
    private $learning_relation_repository;
    /**
     * @var ResponseInfoRepositoryInterface
     */
    private $response_info_repository;

    /**
     * BotDbService constructor.
     * @param VariantRepositoryInterface $variant_repository
     * @param SynonymRepositoryInterface $synonym_repository
     * @param LearningRepositoryInterface $learning_repository
     * @param CategoryRepositoryInterface $category_repository
     * @param ScenarioRepositoryInterface $scenario_repository
     * @param ScenarioRelationRepositoryInterface $scenario_relation_repository
     * @param ScenarioLearningRelationRepositoryInterface $scenario_learning_relation_repository
     * @param ScenarioKeywordRepositoryInterface $scenario_keyword_repository
     * @param ScenarioKeywordRelationRepositoryInterface $scenario_keyword_relation_repository
     * @param LearningRelationRepositoryInterface $learning_relation_repository
     * @param ResponseInfoRepositoryInterface $response_info_repository
     * @param ProperNounRepositoryInterface $proper_noun_repository
     */
    public function __construct(
        VariantRepositoryInterface $variant_repository, SynonymRepositoryInterface $synonym_repository, LearningRepositoryInterface $learning_repository, CategoryRepositoryInterface $category_repository, ScenarioRepositoryInterface $scenario_repository, ScenarioRelationRepositoryInterface $scenario_relation_repository, ScenarioLearningRelationRepositoryInterface $scenario_learning_relation_repository, ScenarioKeywordRepositoryInterface $scenario_keyword_repository, ScenarioKeywordRelationRepositoryInterface $scenario_keyword_relation_repository, LearningRelationRepositoryInterface $learning_relation_repository, ResponseInfoRepositoryInterface $response_info_repository, ProperNounRepositoryInterface $proper_noun_repository
    )
    {
        $this->variant_repository = $variant_repository;
        $this->synonym_repository = $synonym_repository;
        $this->learning_repository = $learning_repository;
        $this->category_repository = $category_repository;
        $this->scenario_repository = $scenario_repository;
        $this->scenario_relation_repository = $scenario_relation_repository;
        $this->scenario_learning_relation_repository = $scenario_learning_relation_repository;
        $this->scenario_keyword_repository = $scenario_keyword_repository;
        $this->scenario_keyword_relation_repository = $scenario_keyword_relation_repository;
        $this->learning_relation_repository = $learning_relation_repository;
        $this->response_info_repository = $response_info_repository;
        $this->proper_noun_repository = $proper_noun_repository;
    }

    /**
     * 回答文取得
     * @param integer $api_id API_ID
     * @return bool
     */
    public function findAnswer($api_id)
    {
        $data = $this->learning_repository->findOneBy(['api_id' => $api_id]);
        return empty($data) ? false : $data['answer'];
    }

    /**
     * 質問文取得
     * @param string $question 質問文
     * @return bool|mixed 取得できなかった場合はFalse、そうでない場合は
     */
    public function findLearningDataFromQuestion($question)
    {
        $data = $this->learning_repository->findOneBy(['question' => $question]);
        return empty($data) ? false : $data;
    }

    /**
     * 学習データ取得
     * @param integer $api_id API_ID
     * @return bool|mixed
     */
    public function findLearningData($api_id)
    {
        $data = $this->learning_repository->findOneBy(['api_id' => $api_id]);
        return empty($data) ? false : $data;
    }

    /**
     * 異表記リポジトリ取得
     * @return VariantRepositoryInterface
     */
    public function getVariantRepository(): VariantRepositoryInterface
    {
        return $this->variant_repository;
    }

    /**
     * 類義語リポジトリ取得
     * @return SynonymRepositoryInterface
     */
    public function getSynonymRepository(): SynonymRepositoryInterface
    {
        return $this->synonym_repository;
    }

    /**
     * 学習データリポジトリ取得
     * @return LearningRepositoryInterface
     */
    public function getLearningRepository(): LearningRepositoryInterface
    {
        return $this->learning_repository;
    }

    /**
     * カテゴリリポジトリ取得
     * @return CategoryRepositoryInterface
     */
    public function getCategoryRepository(): CategoryRepositoryInterface
    {
        return $this->category_repository;
    }

    /**
     * シナリオリポジトリ取得
     * @return ScenarioRepositoryInterface
     */
    public function getScenarioRepository(): ScenarioRepositoryInterface
    {
        return $this->scenario_repository;
    }

    /**
     * シナリオ紐づけリポジトリ取得
     * @return ScenarioRelationRepositoryInterface
     */
    public function getScenarioRelationRepository(): ScenarioRelationRepositoryInterface
    {
        return $this->scenario_relation_repository;
    }

    /**
     * シナリオ学習データ紐づけリポジトリ取得
     * @return ScenarioLearningRelationRepositoryInterface
     */
    public function getScenarioLearningRelationRepository(): ScenarioLearningRelationRepositoryInterface
    {
        return $this->scenario_learning_relation_repository;
    }

    /**
     * シナリオキーワード紐づけリポジトリ取得
     * @return ScenarioKeywordRepositoryInterface
     */
    public function getScenarioKeywordRepository(): ScenarioKeywordRepositoryInterface
    {
        return $this->scenario_keyword_repository;
    }

    /**
     * シナリオキーワードリポジトリ取得
     * @return ScenarioKeywordRelationRepositoryInterface
     */
    public function getScenarioKeywordRelationRepository(): ScenarioKeywordRelationRepositoryInterface
    {
        return $this->scenario_keyword_relation_repository;
    }

    /**
     * 学習データ紐づけリポジトリ取得
     * @return LearningRelationRepositoryInterface
     */
    public function getLearningRelationRepository(): LearningRelationRepositoryInterface
    {
        return $this->learning_relation_repository;
    }

    /**
     * 応答情報リポジトリ取得
     * @return ResponseInfoRepositoryInterface
     */
    public function getResponseInfoRepository(): ResponseInfoRepositoryInterface
    {
        return $this->response_info_repository;
    }

    /**
     * @return ProperNounRepositoryInterface
     */
    public function getProperNounRepository(): ProperNounRepositoryInterface
    {
        return $this->proper_noun_repository;
    }

}
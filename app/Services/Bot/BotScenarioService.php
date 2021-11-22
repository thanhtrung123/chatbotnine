<?php

namespace App\Services\Bot;

use App\Repositories\ScenarioKeyword\ScenarioKeywordRepositoryInterface;
use App\Repositories\ScenarioKeywordRelation\ScenarioKeywordRelationRepositoryInterface;
use Util;

/**
 * チャットボットシナリオサービス
 * Class BotScenarioService
 * @package App\Services\Bot
 */
class BotScenarioService
{
    /**
     * @var MorphBaseService
     */
    private $morph_base_service;
    /**
     * @var BotDbService
     */
    private $db_service;
    /**
     * @var \App\Repositories\Category\CategoryRepositoryInterface
     */
    private $category_repository;
    /**
     * @var \App\Repositories\Scenario\ScenarioRepositoryInterface
     */
    private $scenario_repository;
    /**
     * @var \App\Repositories\ScenarioRelation\ScenarioRelationRepositoryInterface
     */
    private $scenario_relation_repository;
    /**
     * @var \App\Repositories\ScenarioLearningRelation\ScenarioLearningRelationRepositoryInterface
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
     * @var string ステータス
     */
    private $status;
    /**
     * @var string ID
     */
    private $id;
    /**
     * @var array IDs
     */
    private $ids;
    /**
     * @var string 文章
     */
    private $message;
    /**
     * @var array 結果配列
     */
    private $result = [];

    /**
     * BotScenarioService constructor.
     * @param BotDbService $db_service
     * @param MorphBaseService $morph_base_service
     */
    public function __construct(BotDbService $db_service, MorphBaseService $morph_base_service)
    {
        $this->db_service = $db_service;
        $this->category_repository = $db_service->getCategoryRepository();
        $this->scenario_repository = $db_service->getScenarioRepository();
        $this->scenario_relation_repository = $db_service->getScenarioRelationRepository();
        $this->scenario_learning_relation_repository = $db_service->getScenarioLearningRelationRepository();
        $this->scenario_keyword_repository = $db_service->getScenarioKeywordRepository();
        $this->scenario_keyword_relation_repository = $db_service->getScenarioKeywordRelationRepository();
        $this->morph_base_service = $morph_base_service;
    }

    /**
     * 処理実行
     * @return $this
     */
    public function exec()
    {
        $scenario = [];
        if ($this->status == config('bot.const.bot_status_show_category')) {
            //カテゴリ表示
            $this->result['category'] = $this->getCategories();
        } else if ($this->status == config('bot.const.bot_status_select_category')) {
            //カテゴリ id:category_id
            $scenario_gen = $this->searchFirstScenario($this->id);
            foreach ($scenario_gen as $row) {
                $scenario[] = $row;
            }
        } else if ($this->status == config('bot.const.bot_status_select_scenario')) {
            if (!empty($this->ids)) {
                //FIXME:回答とシナリオが混ざるけどどうする？
                $scenario_gen = $this->scenario_repository->joinRelation()
                    ->setParams(['scenario_ids' => $this->ids])->filterByParams()->getDbResult()->getGenerator();
            } else {
                //シナリオ id:scenario_id
                $scenario_gen = $this->searchScenario($this->id);
            }
            foreach ($scenario_gen as $row) {
                $scenario[] = $row;
            }
        } else if ($this->status == config('bot.const.bot_status_select_answer')) {
            //回答 id:api_id
            $data = $this->db_service->findLearningData($this->id);
            if (!empty($data)) {
                $this->result['hear_back_flg'] = false;
                $this->result['num'] = 1;
                $this->result['qa'] = Util::learningDataToQaAry([$data]);
            }
        } else {
            //回答なし（この中にない）
            $this->result['result_status'] = config('bot.const.bot_result_status_no_answer');
        }
        if (!empty($scenario)) {
            //回答が重複するので、排除
            $exists_scenario_ids = $exists_api_ids = $new_scenario = [];
            foreach ($scenario as $row) {
                if (empty($row['api_id'])) {
                    if (!isset($exists_scenario_ids[$row['scenario_id']])) {
                        $exists_scenario_ids[$row['scenario_id']] = 1;
                        $new_scenario[] = $row;
                    }
                    continue;
                }
                $api_id = $row['api_id'];
                if (!isset($exists_api_ids[$api_id])) {
                    $exists_api_ids[$api_id] = 1;
                    $new_scenario[] = $row;
                }
            }
            $this->result['scenario'] = $new_scenario;
        }
        return $this;
    }

    /**
     * カテゴリIDから最初のシナリオを検索
     * @param integer $category_id カテゴリID
     * @return \Generator
     */
    public function searchFirstScenario($category_id)
    {
        $scenario_gen = $this->scenario_repository->joinRelation()
            ->setParams(['category_id' => $category_id, 'is_first' => 1])->filterByParams()->getDbResult()->getGenerator();
        return $scenario_gen;
    }

    /**
     * 親シナリオIDからシナリオを検索
     * @param integer $parent_id 親シナリオID
     * @return \Generator
     */
    public function searchScenario($parent_id)
    {
        $scenario_gen = $this->scenario_repository->joinRelation()
            ->setParams(['parent_scenario_id' => $parent_id])->filterByParams()->getDbResult()->getGenerator();
        return $scenario_gen;
    }

    /**
     * カテゴリ取得
     * @return array
     */
    public function getCategories()
    {
        return array_column($this->category_repository->getAll(), 'name', 'id');
    }

    /**
     * カテゴリ一致
     * @return integer|null
     */
    public function matchCategoryId()
    {
        $data = $this->category_repository->findOneBy(['name' => $this->message]);
        return $data['id'] ?? null;
    }

    /**
     * シナリオ一致
     * @return integer|null
     */
    public function matchScenarioId()
    {
        $data = $this->scenario_repository->findOneBy(['name' => $this->message]);
        return $data['id'] ?? null;
    }

    /**
     * シナリオ　関連キーワード検索
     * @return array|null
     */
    public function matchRelationKeywordScenarioIds()
    {
        //入力メッセージをキーフレーズに分解
        $morph_keywords = $this->morph_base_service->setMessage($this->message)
            ->replaceMessageUseDictionary(true, true)
            ->makeKeyPhrase();
        $morph_keyword_count = count($morph_keywords);
        if (empty($morph_keywords)) return null;
        //入力キーフレーズとシナリオキーフレーズの紐づけ
        $keyword_ary = $this->scenario_keyword_repository
            ->setEnableJoin(false)
            ->setParams(['keywords' => array_keys($morph_keywords)])
            ->filterByParams()->getDbResult()->getPlainArray();
        $keywords = array_column($keyword_ary, 'keyword', 'id');
        $keyword_count = count($keywords);
        if ($morph_keyword_count != $keyword_count) return null;
        //対象のキーフレーズを含んでグループ内のキーワード数が入力キーワード数と等しいものを抽出（多いグループも含む）
        $scenario_keyword_relation_gen = $this->scenario_keyword_relation_repository
            ->setParams(['scenario_keyword_ids' => array_keys($keywords)])
            ->filterCountByGroup($keyword_count)
            ->filterByParams()->getDbResult()->getGenerator();
        $scenario_keyword_groups = [];
        foreach ($scenario_keyword_relation_gen as $row) {
            $scenario_keyword_groups[$row['scenario_id']][$row['group_no']] = $row['keyword_cnt'];
        }
        //上記抽出シナリオ内で入力キーワード数よりもキーワード数が多いグループを抽出
        $scenario_keyword_relation_gen = $this->scenario_keyword_relation_repository
            ->setParams(['scenario_ids' => array_keys($scenario_keyword_groups)])
            ->filterCountByGroup($keyword_count, '>')
            ->filterByParams()->getDbResult()->getGenerator();
        foreach ($scenario_keyword_relation_gen as $row) {
            if (!isset($scenario_keyword_groups[$row['scenario_id']][$row['group_no']])) continue;
            //多いグループを排除
            unset($scenario_keyword_groups[$row['scenario_id']][$row['group_no']]);
            if (empty($scenario_keyword_groups[$row['scenario_id']]))
                unset($scenario_keyword_groups[$row['scenario_id']]);
        }
        //シナリオIDを返却
        if (!empty($scenario_keyword_groups))
            return array_slice(array_keys($scenario_keyword_groups), 0, config('bot.scenario.no_one_refine_max_scenario'));
        return null;
    }

    /**
     * ステータス取得
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * ステータスセット
     * @param string $status 入力ステータス
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * ID取得
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * IDセット
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function setIds(array $ids)
    {
        $this->ids = $ids;
        return $this;
    }

    /**
     * 結果取得
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * メッセージ取得
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * メッセージセット
     * @param mixed $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = empty($message) ? '' : $message;
        return $this;
    }

    /**
     * カテゴリリポジトリ取得
     * @return \App\Repositories\Category\CategoryRepositoryInterface
     */
    public function getCategoryRepository(): \App\Repositories\Category\CategoryRepositoryInterface
    {
        return $this->category_repository;
    }

    /**
     * シナリオリポジトリ取得
     * @return \App\Repositories\Scenario\ScenarioRepositoryInterface
     */
    public function getScenarioRepository(): \App\Repositories\Scenario\ScenarioRepositoryInterface
    {
        return $this->scenario_repository;
    }

    /**
     * シナリオ紐づけリポジトリ取得
     * @return \App\Repositories\ScenarioRelation\ScenarioRelationRepositoryInterface
     */
    public function getScenarioRelationRepository(): \App\Repositories\ScenarioRelation\ScenarioRelationRepositoryInterface
    {
        return $this->scenario_relation_repository;
    }

    /**
     * シナリオ学習データ紐づけリポジトリ取得
     * @return \App\Repositories\ScenarioLearningRelation\ScenarioLearningRelationRepositoryInterface
     */
    public function getScenarioLearningRelationRepository(): \App\Repositories\ScenarioLearningRelation\ScenarioLearningRelationRepositoryInterface
    {
        return $this->scenario_learning_relation_repository;
    }

}
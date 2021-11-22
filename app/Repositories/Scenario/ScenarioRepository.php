<?php

namespace App\Repositories\Scenario;

use App\Models\Scenario;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use DB;

/**
 * シナリオリポジトリ
 * Class ScenarioRepository
 * @package App\Repositories\Scenario
 */
class ScenarioRepository extends AbstractRepository implements ScenarioRepositoryInterface
{
    use ByKeyword;

    private $join_api_id = false;

    /**
     * モデルクラス名取得
     * @return mixed|string
     */
    public function getModelClass()
    {
        return Scenario::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|void
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        if (isset($this->params['category_id'])) {
            if (empty(($this->params['category_id'] ?? NULL))) {
                $query->whereNull("{$this->model->getTable()}.category_id");
            } else {
                $query->where("{$this->model->getTable()}.category_id", $this->params['category_id']);
            }
        }
        if (!empty($this->params['is_first'])) {
            $query->whereNull('parent_scenario_id');
        }
        if (!empty($this->params['parent_scenario_id'])) {
            $query->where('parent_scenario_id', $this->params['parent_scenario_id']);
        }
        if (!empty($this->params['scenario_ids'])) {
            $query->whereIn("{$this->model->getTable()}.id", $this->params['scenario_ids']);
        }
        if (!empty($this->params['name'])) {
            $query->where('name', $this->params['name']);
        }
        return $this;
    }

    /**
     * 紐づけ情報を結合
     * @param bool $use_order
     * @return $this
     */
    public function joinRelation($use_order = true): ScenarioRepositoryInterface
    {
        $this->join_api_id = true;
        $query = $this->getQuery();
        $query->select("{$this->model->getTable()}.*", 'tbl_scenario_relation.*', 'tbl_scenario_learning_relation.api_id');
        $query->join('tbl_scenario_relation', 'tbl_scenario_relation.scenario_id', '=', "{$this->model->getTable()}.id");
        $query->leftJoin('tbl_scenario_learning_relation', 'tbl_scenario_learning_relation.scenario_id', '=', "{$this->model->getTable()}.id");
        if ($use_order)
            $query->orderBy("{$this->model->getTable()}.order")->orderBy('tbl_scenario_learning_relation.order');
        return $this;
    }

    /**
     * Join learning
     * @return $this
     */
    public function joinLearning(): ScenarioRepositoryInterface
    {
        $query = $this->getQuery();
        $query->select("{$this->model->getTable()}.id",
            "{$this->model->getTable()}.name",
            "{$this->model->getTable()}.id as scenario_id",
            'tbl_learning.id as learning_id',
            "{$this->model->getTable()}.category_id as category_id",
            "{$this->model->getTable()}.order as order",
            DB::raw('GROUP_CONCAT(tbl_scenario_keyword_relation.scenario_keyword_id) as keyword_id'),
            DB::raw('GROUP_CONCAT(tbl_scenario_keyword_relation.group_no) as keyword_groupno'),
            DB::raw('GROUP_CONCAT(tbl_scenario_keyword_relation.order) as keyword_order'),
            DB::raw('GROUP_CONCAT(tbl_scenario_keyword.keyword) as keyword'),
            DB::raw('GROUP_CONCAT(tbl_scenario_relation.parent_scenario_id) as parent_scenario_id'));
        $query->leftJoin('tbl_scenario_relation', 'tbl_scenario_relation.scenario_id', '=', "{$this->model->getTable()}.id");
        $query->leftJoin('tbl_scenario_learning_relation', 'tbl_scenario_learning_relation.scenario_id', '=', "{$this->model->getTable()}.id");
        $query->leftJoin('tbl_learning', 'tbl_scenario_learning_relation.api_id', '=', "tbl_learning.id");
        $query->groupBy("{$this->model->getTable()}.id");
        $query->orderByRaw("CASE WHEN {$this->model->getTable()}.order = 0 THEN 9999999 ELSE {$this->model->getTable()}.order END, {$this->model->getTable()}.order ASC");
        return $this;
    }

    /**
     * キーワード結合
     * @return $this
     */
    public function joinKeyword(): ScenarioRepositoryInterface
    {
        $query = $this->getQuery();
        $query->leftJoin('tbl_category', 'tbl_category.id', '=', "{$this->model->getTable()}.category_id");
        $query->leftJoin('tbl_scenario_keyword_relation', 'tbl_scenario_keyword_relation.scenario_id', '=', "{$this->model->getTable()}.id");
        $query->leftJoin('tbl_scenario_keyword', 'tbl_scenario_keyword.id', '=', "tbl_scenario_keyword_relation.scenario_keyword_id");
        return $this;
    }

}
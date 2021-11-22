<?php

namespace App\Repositories\ScenarioRelation;

use App\Models\ScenarioRelation;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use DB;

/**
 * シナリオ紐づけリポジトリ
 * Class ScenarioRelationRepository
 * @package App\Repositories\ScenarioRelation
 */
class ScenarioRelationRepository extends AbstractRepository implements ScenarioRelationRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return mixed|string
     */
    public function getModelClass()
    {
        return ScenarioRelation::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|void
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        if (!empty($this->params['scenario_id'])) {
            $query->where('scenario_id', $this->params['scenario_id']);
        }
        if (!empty($this->params['parent_scenario_id'])) {
            $query->where('parent_scenario_id', $this->params['parent_scenario_id']);
        }
        return $this;
    }

    /**
     * Remove connection
     * @param $scenario_id, $api_id
     * @return $this
     */
    public function removeConnection($child_id, $parent_id): ScenarioRelationRepositoryInterface
    {
        $query = $this->getQuery();
        $query->select("{$this->model->getTable()}.id");
        $query->where("{$this->model->getTable()}.scenario_id", $child_id);
        $query->where("{$this->model->getTable()}.parent_scenario_id", $parent_id);
        return $this;
    }

    /**
     * Delete data ScenarioLearningRelation by scenario_id or $parent_scenario_id
     * @param $id
     * @return $this
     */
    public function deleteDataDismisById($id): ScenarioRelationRepositoryInterface
    {
        $query = $this->clearQuery();
        $query->select("{$this->model->getTable()}.*");
        if (is_array($id)) {
            $query->whereIn("{$this->model->getTable()}.scenario_id", $id);
            $query->orWhereIn("{$this->model->getTable()}.parent_scenario_id", $id);
        } else {
            $query->where("{$this->model->getTable()}.scenario_id", $id);
            $query->orWhere("{$this->model->getTable()}.parent_scenario_id", $id);
        }
        $query->delete();
        return $this;
    }

    /**
     * Delete data where scenario_id not null & parent_scenario_id is null
     * @param $scenario_id
     * @return $this
     */
    public function deleteParentIdIsNull($scenario_id): ScenarioRelationRepositoryInterface
    {
        $query = $this->clearQuery();
        if (is_array($scenario_id)) {
            $query->whereIn("{$this->model->getTable()}.scenario_id", $scenario_id);
        } else {
            $query->where("{$this->model->getTable()}.scenario_id", $scenario_id);
        }
        $query->whereNull("{$this->model->getTable()}.parent_scenario_id");
        $query->delete();
        return $this;
    }
}
<?php

namespace App\Repositories\ScenarioLearningRelation;

use App\Models\ScenarioLearningRelation;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use DB;

/**
 * シナリオ学習データ紐づけリポジトリ
 * Class ScenarioLearningRelationRepository
 * @package App\Repositories\ScenarioLearningRelation
 */
class ScenarioLearningRelationRepository extends AbstractRepository implements ScenarioLearningRelationRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return mixed|string
     */
    public function getModelClass()
    {
        return ScenarioLearningRelation::class;
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
        return $this;
    }

    /**
     * Remove connection
     * @param $scenario_id, $api_id
     * @return $this
     */
    public function removeConnection($scenario_id, $api_id): ScenarioLearningRelationRepositoryInterface
    {
        $query = $this->getQuery();
        $query->select("{$this->model->getTable()}.id");
        $query->where("{$this->model->getTable()}.scenario_id", $scenario_id);
        $query->where("{$this->model->getTable()}.api_id", $api_id);
        return $this;
    }

    /**
     * Delete data ScenarioLearningRelation by scenario_id
     * @param $id
     * @return $this
     */
    public function deleteDataDismisById($id): ScenarioLearningRelationRepositoryInterface
    {
        $query = $this->clearQuery();
        $query->select("{$this->model->getTable()}.*");
        if (is_array($id)) {
            $query->whereIn("{$this->model->getTable()}.scenario_id", $id);
        } else {
            $query->where("{$this->model->getTable()}.scenario_id", $id);
        }
        $query->delete();
        return $this;
    }

    /**
     * Delete data ScenarioLearningRelation by api_id
     * @param $id
     * @return $this
     */
    public function deleteByApiId($api_id): ScenarioLearningRelationRepositoryInterface
    {
        $query = $this->getQuery();
        $query->where("{$this->model->getTable()}.api_id", $api_id);
        $query->delete();
        return $this;
    }

    /**
     * Get Data learning copy 
     * @param $apiId
     * @return $this
     */
    public function getDataCopy($apiId): ScenarioLearningRelationRepositoryInterface
    {
        $query = $this->clearQuery()
            ->where('node_id', '!=', 0);
        if ($apiId) {
            $query = $query->whereIn('api_id', $apiId);
        }
        return $this;
    }

    /**
     * 
     * Get max Node id
     * @return $this
     */
    public function getMaxNodeId(): ScenarioLearningRelationRepositoryInterface
    {
        $query = $this->clearQuery()
            ->select(\DB::raw('max(node_id) as node_id'));
        return $this;
    }
}
<?php

namespace App\Repositories\ScenarioLearningRelation;

use App\Repositories\RepositoryInterface;

/**
 * シナリオ学習データ紐づけリポジトリインターフェース
 * Interface ScenarioLearningRelationRepositoryInterface
 * @package App\Repositories\ScenarioLearningRelation
 */
interface ScenarioLearningRelationRepositoryInterface extends RepositoryInterface
{
    /**
     * Remove connection
     * @param $scenario_id, $api_id
     * @return $this
     */

    public function removeConnection($scenario_id, $api_id): self;

    /**
     * Delete data ScenarioLearningRelation by scenario_id
     * @param $id
     * @return $this
     */
    public function deleteDataDismisById($id): self;

    /**
     * Delete data ScenarioLearningRelation by api_id
     * @param $api_id
     * @return $this
     */
    public function deleteByApiId($api_id): self;

    /**
     * Get Data learning copy 
     * @param $apiId
     * @return $this
     */
    public function getDataCopy($apiId): self;

    /**
     * 
     * Get max Node id
     * @return $this
     */
    public function getMaxNodeId(): self;
}
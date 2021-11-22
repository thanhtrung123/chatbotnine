<?php

namespace App\Repositories\ScenarioRelation;

use App\Repositories\RepositoryInterface;

/**
 * シナリオ紐づけリポジトリインターフェース
 * Interface ScenarioRelationRepositoryInterface
 * @package App\Repositories\ScenarioRelation
 */
interface ScenarioRelationRepositoryInterface extends RepositoryInterface
{
    /**
     * Remove connection
     * @param $child_id, $parent_id
     * @return $this
     */
    public function removeConnection($child_id, $parent_id): self;

    /**
     * Delete data ScenarioLearningRelation by scenario_id or $parent_scenario_id
     * @param $id
     * @return $this
     */
    public function deleteDataDismisById($id): self;

    /**
     * Delete data where scenario_id not null & parent_scenario_id is null
     * @param $scenario_id
     * @return $this
     */
    public function deleteParentIdIsNull($scenario_id): self;
}
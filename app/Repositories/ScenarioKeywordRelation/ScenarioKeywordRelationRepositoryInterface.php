<?php

namespace App\Repositories\ScenarioKeywordRelation;

use App\Repositories\RepositoryInterface;

/**
 * シナリオキーワード紐づけリポジトリインターフェース
 * Interface ScenarioRelationRepositoryInterface
 * @package App\Repositories\ScenarioRelation
 */
interface ScenarioKeywordRelationRepositoryInterface extends RepositoryInterface
{
    /**
     * Delete data keyword relation by scenario id
     * @param $id
     * @return $this
     */
    public function deleteDataDismisById($id): self;

    /**
     * @param null $keyword_cnt
     * @param string $ope
     * @return $this
     */
    public function filterCountByGroup($keyword_cnt = null, $ope = '=');
}
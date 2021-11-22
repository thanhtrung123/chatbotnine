<?php

namespace App\Repositories\Scenario;

use App\Repositories\RepositoryInterface;

/**
 * シナリオリポジトリインターフェース
 * Interface ScenarioRepositoryInterface
 * @package App\Repositories\Scenario
 */
interface ScenarioRepositoryInterface extends RepositoryInterface
{
    /**
     * 紐づけ情報結合
     * @param bool $use_order
     * @return $this
     */
    public function joinRelation($use_order = true): self;

    /**
     * Join learning
     * @return $this
     */
    public function joinLearning(): self;
    /**
     * キーワード結合
     * @return $this
     */
    public function joinKeyword(): self;

}
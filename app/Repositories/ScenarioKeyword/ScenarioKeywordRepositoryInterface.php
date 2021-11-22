<?php

namespace App\Repositories\ScenarioKeyword;

use App\Repositories\RepositoryInterface;

/**
 * シナリオキーワードリポジトリインターフェース
 * Interface ScenarioRepositoryInterface
 * @package App\Repositories\Scenario
 */
interface ScenarioKeywordRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $word
     * @return mixed
     */
    public function findOnly($word);

    /**
     * @param $word
     * @return mixed
     */
    public function findOrSave($word);

    /**
     * @param bool $enable_join
     * @return $this
     */
    public function setEnableJoin(bool $enable_join);
}
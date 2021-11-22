<?php

namespace App\Repositories\ScenarioKeywordRelation;

use App\Models\ScenarioKeywordRelation;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use DB;

/**
 * シナリオキーワード紐づけリポジトリ
 * Class ScenarioRelationRepository
 * @package App\Repositories\ScenarioRelation
 */
class ScenarioKeywordRelationRepository extends AbstractRepository implements ScenarioKeywordRelationRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return mixed|string
     */
    public function getModelClass()
    {
        return ScenarioKeywordRelation::class;
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
        if (!empty($this->params['scenario_ids'])) {
            $query->whereIn('scenario_id', $this->params['scenario_ids']);
        }
        if (!empty($this->params['scenario_keyword_ids'])) {
            $query->whereIn('scenario_keyword_id', $this->params['scenario_keyword_ids']);
        }
        return $this;
    }

    /**
     * @param null $keyword_cnt
     * @param string $ope
     * @return $this
     */
    public function filterCountByGroup($keyword_cnt = null, $ope = '=')
    {
        $query = $this->getQuery();
        $query->addSelect('*')
            ->addSelect(DB::raw('COUNT(scenario_keyword_id) as keyword_cnt'))
            ->groupBy(['scenario_id', 'group_no'])
            ->orderBy('scenario_id')->orderBy('group_no')->orderBy('order');
        if ($keyword_cnt !== null) {
            $query->having('keyword_cnt', $ope, $keyword_cnt);
        }

        return $this;
    }

    /**
     * Delete data keyword relation by scenario id
     * @param $id
     * @return $this
     */
    public function deleteDataDismisById($id): ScenarioKeywordRelationRepositoryInterface
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

}
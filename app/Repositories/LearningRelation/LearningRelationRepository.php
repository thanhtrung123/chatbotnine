<?php

namespace App\Repositories\LearningRelation;

use App\Models\LearningRelation;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use DB;

/**
 * 学習データ紐づけリポジトリ
 * Class LearningRelationRepository
 * @package App\Repositories\LearningRelation
 */
class LearningRelationRepository extends AbstractRepository implements LearningRelationRepositoryInterface
{

    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return LearningRelation::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|void
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        if (!empty($this->params['api_id'])) {
            $query->where('api_id', $this->params['api_id']);
        }
        if (!empty($this->dt_params['api_id'])) {
            $query->where('api_id', $this->dt_params['api_id']);
        }
        if (!empty($this->dt_params['keyword'])) {
            $this->byKeyword($this->dt_params['keyword'], ['name']);
        }
        if (!empty($this->dt_params['relation_api_id'])) {
            $query->where('relation_api_id', $this->dt_params['relation_api_id']);
        }
        return $this;
    }

}
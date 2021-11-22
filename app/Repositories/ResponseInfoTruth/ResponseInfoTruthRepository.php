<?php

namespace App\Repositories\ResponseInfoTruth;

use App\Models\ResponseInfoTruth;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\ResponseInfoTruth\ResponseInfoTruthRepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use App\Repositories\type;
use DB;
use App\Services\Bot\BotLogService;

/**
 * 応答情報（真理表）リポジトリ
 * Class ResponseInfoTruthRepository
 * @package App\Repositories\ResponseInfoTruth
 */
class ResponseInfoTruthRepository extends AbstractRepository implements ResponseInfoTruthRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return ResponseInfoTruth::class;
    }

    /**
     * ログ保存
     * @param array $data
     * @return bool
     */
    public function saveLog(array $data): bool
    {
        return $this->model->insert($data);
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|AbstractRepository
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        if (isset($this->params['info_id'])) {
            $query->where('info_id', $this->params['info_id']);
        }
        return $this;
    }
}
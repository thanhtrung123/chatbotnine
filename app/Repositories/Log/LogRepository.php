<?php

namespace App\Repositories\Log;

use App\Models\Log;
use App\Repositories\AbstractRepository;
use App\Repositories\Log\LogRepositoryInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\type;
use DB;

/**
 * ログリポジトリ
 * Class LogRepository
 * @package App\Repositories\Log
 */
class LogRepository extends AbstractRepository implements LogRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return type
     */
    public function getModelClass()
    {
        return Log::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return RepositoryInterface
     * @throws \Exception
     */
    public function filterByParams(): RepositoryInterface
    {
        if (!empty($this->dt_params['date'])) {
            $date = new \DateTime($this->dt_params['date']);
            $this->query->where('action_datetime', '>', $date->format('Ymd'))
                ->where('action_datetime', '<', $date->modify('+1 day')->format('Ymd'));
        }
        if (!empty($this->dt_params['processing'])) {
            $this->query->where('processing', $this->dt_params['processing']);
        }
        return $this;
    }

    /**
     * ログ追加
     * @param $data
     * @return mixed
     */
    public function insertLog($data)
    {
        return $this->model->insert($data);
    }
}
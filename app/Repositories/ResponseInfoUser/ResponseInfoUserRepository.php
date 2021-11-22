<?php

namespace App\Repositories\ResponseInfoUser;

use App\Models\ResponseInfoUser;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use App\Repositories\type;
use DB;

/**
 * 応答情報（利用者）リポジトリ
 * Class ResponseInfoUserRepository
 * @package App\Repositories\ResponseInfoUser
 */
class ResponseInfoUserRepository extends AbstractRepository implements ResponseInfoUserRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return ResponseInfoUser::class;
    }

    /**
     * 同じ情報が存在するか
     * @param array $data
     * @return bool|mixed
     */
    public function existsEqualsData(array $data)
    {
        $query = $this->getQuery();
        $query
//            ->where('channel', $data['channel'])
            ->where('chat_id', $data['chat_id'])
//            ->where('referrer', $data['referrer'])
            ->where('useragent', $data['useragent'])
            ->where('remote_ip', $data['remote_ip'])
            ->where('status', $data['status']);
        $data = $this->getDbResult()->getOne();
        return !empty($data);
    }

    /**
     * ログ保存
     * @param array $data
     * @return mixed
     */
    public function saveLog(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|AbstractRepository
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        return $this;
    }
}
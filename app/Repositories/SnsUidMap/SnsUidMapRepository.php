<?php

namespace App\Repositories\SnsUidMap;

use App\Models\SnsUidMap;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use DB;

/**
 * Class SnsUidMapRepository
 * @package App\Repositories\SnsUidMap
 */
class SnsUidMapRepository extends AbstractRepository implements SnsUidMapRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return SnsUidMap::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return RepositoryInterface
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueEnqueteKey()
    {
        do {
            $key = str_random();
        } while (!empty($this->findOneBy(['enquete_key' => $key])));
        return $key;
    }

}
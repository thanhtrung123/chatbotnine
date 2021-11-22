<?php

namespace App\Repositories\Variant;

use App\Models\Variant;
use App\Repositories\AbstractRepository;
use App\Repositories\DbResultInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use App\Repositories\type;
use App\Repositories\Variant\VariantRepositoryInterface;
use DB;

/**
 * 異表記リポジトリ
 * Class VariantRepository
 * @package App\Repositories\Variant
 */
class VariantRepository extends AbstractRepository implements VariantRepositoryInterface
{
    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return Variant::class;
    }

    /**
     * キーワードで絞り込み
     * @param string $keyword
     * @return array|mixed
     */
    public function findByKeyword($keyword)
    {
        return $this->findOneBy(['noun_variant_text' => $keyword]);
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|void
     */
    public function filterByParams(): RepositoryInterface
    {
        if (!empty($this->dt_params['keyword'])) {
            $this->byKeyword($this->dt_params['keyword'], ['noun_variant_text', 'noun_text']);
        }
        return $this;
    }

    /**
     * メッセージから絞り込み
     * @param $message
     * @return DbResultInterface
     */
    public function findByMessage($message): DbResultInterface
    {
        $query = $this->getQuery();
        $query->whereRaw(':message like CONCAT("%",noun_variant_text,"%")', ['message' => $message]);
        $query->orderBy(DB::raw('CHAR_LENGTH(noun_variant_text)'), 'desc');
        return $this->getDbResult();
    }


}
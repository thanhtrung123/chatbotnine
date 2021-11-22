<?php

namespace App\Repositories\ProperNoun;

use App\Models\ProperNoun;
use App\Repositories\AbstractRepository;
use App\Repositories\DbResultInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use DB;

/**
 * 固有名詞リポジトリ
 * Class ProperNounRepository
 * @package App\Repositories\ProperNoun
 */
class ProperNounRepository extends AbstractRepository implements ProperNounRepositoryInterface
{
    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return ProperNoun::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return RepositoryInterface
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        if (!empty($this->dt_params['keyword'])) {
            $this->byKeyword($this->dt_params['keyword'], ['word']);
        }
        return $this;
    }

    /**
     * 次の固有名詞ID取得
     * @return int
     */
    public function getNextProperNounId()
    {
        return parent::getNextId('proper_noun_id');
    }

    /**
     * メッセージから絞り込み
     * @param $message
     * @return DbResultInterface
     */
    public function findByMessage($message): DbResultInterface
    {
        $query = $this->getQuery();
        $query->whereRaw(':message like CONCAT("%",word,"%")', ['message' => $message]);
        $query->orderBy(DB::raw('CHAR_LENGTH(word)'), 'desc');
        return $this->getDbResult();
    }

}
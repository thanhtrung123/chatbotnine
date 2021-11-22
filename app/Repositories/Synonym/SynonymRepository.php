<?php

namespace App\Repositories\Synonym;

use App\Models\Synonym;
use App\Repositories\AbstractRepository;
use App\Repositories\DbResultInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\Synonym\SynonymRepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use App\Repositories\type;
use DB;

/**
 * 類義語リポジトリ
 * Class SynonymRepository
 * @package App\Repositories\Synonym
 */
class SynonymRepository extends AbstractRepository implements SynonymRepositoryInterface
{
    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return Synonym::class;
    }

    /**
     * キーワードで絞り込み
     * @param string $keyword
     * @return array|mixed
     */
    public function findByKeyword($keyword)
    {
        return $this->findOneBy(['keyword' => $keyword]);
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|void
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        if (!empty($this->dt_params['keyword'])) {
            $this->byKeyword($this->dt_params['keyword'], ['keyword', 'synonym']);
        }
        if (!empty($this->params['keywords'])) {
            $query->whereIn('keyword', $this->params['keywords']);
        }
        return $this;
    }

    /**+
     * メッセージから絞り込み
     * @param $message
     * @param null $original_message
     * @return DbResultInterface
     */
    public function findByMessage($message, $original_message = null): DbResultInterface
    {
        $query = $this->getQuery();
        $query->whereRaw(':message1 like CONCAT("%",keyword,"%")', ['message1' => $message])
            ->orWhereRaw(':message2 like CONCAT("%",keyword,"%")', ['message2' => str_replace(' ', '', $message)]);
        if ($original_message !== null) {
            $query->orWhereRaw(':message3 like CONCAT("%",keyword,"%")', ['message3' => $original_message])
                ->orWhereRaw(':message4 like CONCAT("%",keyword,"%")', ['message4' => str_replace(' ', '', $original_message)]);
        }
        $query->orderBy(DB::raw('CHAR_LENGTH(keyword)'), 'desc');
        return $this->getDbResult();
    }

}
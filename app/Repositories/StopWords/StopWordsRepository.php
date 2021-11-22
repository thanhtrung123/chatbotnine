<?php

namespace App\Repositories\StopWords;

use App\Models\StopWords;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\StopWords\StopWordsRepositoryInterface;
use App\Repositories\type;

/**
 * ストップワードリポジトリ
 * Class StopWordsRepository
 * @package App\Repositories\StopWords
 */
class StopWordsRepository extends AbstractRepository implements StopWordsRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return StopWords::class;
    }

    /**
     * 存在するか
     * @param type $word
     * @return bool
     */
    public function exists($word): bool
    {
        $data = $this->findOneBy(['word' => $word]);
        return !empty($data);
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();

        if (isset($this->params['word'])) {
            $query->where('word', $this->params['word']);
        }

        return $this;
    }
}
<?php

namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use DB;

/**
 * カテゴリリポジトリ
 * Class CategoryRepository
 * @package App\Repositories\Category
 */
class CategoryRepository extends AbstractRepository implements CategoryRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return Category::class;
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

}
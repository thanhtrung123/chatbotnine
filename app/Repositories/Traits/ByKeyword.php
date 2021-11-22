<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * キーワード検索トレイト
 * Trait ByKeyword
 * @package App\Repositories\Traits
 */
trait ByKeyword
{
    /**
     * キーワード検索(AND)
     * @param $keyword
     * @param $columns
     * @return Builder
     */
    private function byKeyword($keyword, $columns): Builder
    {
        /** @var Builder $query */
        $query = $this->query;
        $keywords = preg_split('/( |　)+/', $keyword);
        foreach ($keywords as $word) {
            $query->where(function ($query) use ($word, $columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%{$word}%");
                }
            });
        }
        return $query;
    }

    /**
     * キーワード検索(OR)
     * @param $keyword
     * @param $columns
     * @return Builder
     */
    private function byOrKeyword($keyword, $columns): Builder
    {
        /** @var Builder $query */
        $query = $this->query;
        $keywords = preg_split('/( |　)+/', $keyword);
        foreach ($keywords as $word) {
            $query->orWhere(function ($query) use ($word, $columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%{$word}%");
                }
            });
        }
        return $query;
    }
}

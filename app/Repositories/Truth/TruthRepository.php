<?php

namespace App\Repositories\Truth;

use App\Models\Truth;
use App\Repositories\AbstractRepository;
use App\Repositories\Truth\TruthRepositoryInterface;
use App\Repositories\type;
use DB;

/**
 * 真理表リポジトリ
 * Class TruthRepository
 * @package App\Repositories\Truth
 */
class TruthRepository extends AbstractRepository implements TruthRepositoryInterface
{
    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return Truth::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this
     */
    public function filterByParams(): \App\Repositories\RepositoryInterface
    {
        $query = $this->getQuery();
        if (isset($this->params['id'])) {
            $query->where('id', $this->params['id']);
        }
        if (isset($this->params['api_id'])) {
            $query->where('api_id', $this->params['api_id']);
        }
        if (isset($this->params['api_ids'])) {
            $query->whereIn('api_id', $this->params['api_ids']);
        }
        if (isset($this->params['ignore_api_ids'])) {
            $query->whereNotIn('api_id', $this->params['ignore_api_ids']);
        }
        if (isset($this->params['word'])) {
            $query->where('word', $this->params['word']);
        }
        if (isset($this->params['words'])) {
            $query->whereIn('word', $this->params['words']);
        }
        if (isset($this->params['key_phrases'])) {
            $query->whereIn('word', $this->params['key_phrases'])
                ->orWhereIn('replace_word', $this->params['key_phrases']);
        }
        if (isset($this->params['ignore_words'])) {
            $query->whereNotIn('word', $this->params['ignore_words']);
        }
        if (isset($this->params['without_delete'])) {
            $query->where('disabled', '=', config('const.common.disabled.no.id'));
        }
        if (isset($this->params['key_phrase_id'])) {
            $query->where('tbl_truth.key_phrase_id', $this->params['key_phrase_id']);
        }
        if (isset($this->params['key_phrase_ids'])) {
            $query->whereIn('tbl_truth.key_phrase_id', $this->params['key_phrase_ids']);
        }
        if (isset($this->params['has_count'])) {
            $query->where('count', '>', 0);
        }
        if (isset($this->params['ids'])) {
            $query->whereIn('tbl_truth.id', $this->params['ids']);
        }
        return $this;
    }

    /**
     * ワードカウント用フィルタ
     * @return $this;
     */
    public function filterWordCount(): \App\Repositories\Truth\TruthRepositoryInterface
    {
        $query = $this->getQuery();
        $query->select(DB::raw('count(*) as cnt'), 'word')
            ->groupBy('word')
            ->orderBy('cnt', 'desc');
        return $this;
    }

    /**
     * API_ID毎ワードカウント用フィルタ
     * @return $this;
     */
    public function filterWordCountPerApiId(): \App\Repositories\Truth\TruthRepositoryInterface
    {
        $query = $this->getQuery();
        $query->select(DB::raw('count(*) as cnt'), 'api_id')
            ->groupBy('api_id')
            ->orderBy('cnt', 'desc');
        return $this;
    }

    /**
     * DB結果クラス取得前処理
     * @param \Illuminate\Database\Query\Builder|mixed $query
     */
    public function beforeDbResult($query)
    {
        $this->joinKeyPhrase();
    }

    /**
     * キーフレーズと結合
     */
    private function joinKeyPhrase()
    {
        $query = $this->getQuery();
        $query
            ->addSelect([
                "{$this->model->getTable()}.*",
                'tbl_key_phrase.*',
                "{$this->model->getTable()}.id as truth_id",
                DB::raw('COALESCE(replace_word,word) as key_phrase'),
            ])
            ->join('tbl_key_phrase', 'tbl_key_phrase.key_phrase_id', '=', "{$this->model->getTable()}.key_phrase_id");
    }

}
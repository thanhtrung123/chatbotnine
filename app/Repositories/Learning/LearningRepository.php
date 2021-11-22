<?php

namespace App\Repositories\Learning;

use App\Models\Learning;
use App\Repositories\AbstractRepository;
use App\Repositories\DbResult;
use App\Repositories\DbResultInterface;
use App\Repositories\Learning\LearningRepositoryInterface;
use App\Repositories\RepositoryInterface;
use App\Repositories\Traits;
use DB;

/**
 * 学習データリポジトリ
 * Class LearningRepository
 * @package App\Repositories\Learning
 */
class LearningRepository extends AbstractRepository implements LearningRepositoryInterface
{
    use Traits\ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return Learning::class;
    }

    /**
     * キーワードで絞り込み
     * @param string $keyword
     * @return array|mixed
     */
    public function findByKeyword($keyword)
    {
        return $this->findOneBy(['question_morph' => $keyword]);
    }

    /**
     * 回答を検索
     * @param $question
     * @return array|null
     */
    public function searchAnswer($question)
    {
        $this->byOrKeyword($question, ['question_morph']);
        $find = $this->query->get();
        return $find ? $find->toArray() : null;
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this|void
     */
    public function filterByParams(): RepositoryInterface
    {
        $base_tbl = $this->model->getTable();
        $query = $this->getQuery();
        //DataTables
        if (!empty($this->dt_params['keyword'])) {
            $this->byKeyword($this->dt_params['keyword'], ['question', 'answer', "{$base_tbl}.api_id"]);
        }
        if (!empty($this->dt_params['keyword_meta'])) {
            $this->byKeyword($this->dt_params['keyword_meta'], ['metadata']);
        }
        if (!empty($this->dt_params['keyword_key_phrase'])) {
            $query->having($this->getJoinKeyPhraseColumns(''), 'like', '%' . $this->dt_params['keyword_key_phrase'] . '%');
        }
        if (!empty($this->dt_params['category_id'])) {
            $query->where('category_id', $this->dt_params['category_id']);
        }
        if (!empty($this->params['suggest'])) {
            $this->byKeyword($this->params['suggest'], ['question']);
        }

        //Params
        if (!empty($this->params['api_ids'])) {
            $query->whereIn($base_tbl . '.api_id', $this->params['api_ids']);
        }
        if (!empty($this->params['api_id'])) {
            $query->where($base_tbl . '.api_id', $this->params['api_id']);
        }
        if (!empty($this->params['auto_key_phrase'])) {
            $query->where('auto_key_phrase_disabled', config('const.common.on_off.off.id'));
        }
        if (isset($this->params['without_delete'])) {
            $query->where('disabled', '=', config('const.common.disabled.no.id'));
        }
        if (isset($this->params['limit'])) {
            $query->limit($this->params['limit']);
        }

        return $this;
    }

    /**
     * 学習データ取得
     * @return DbResultInterface
     */
    public function getLearningData(): DbResultInterface
    {
        return new DbResult($this->query);
    }

    /**
     * キーフレーズ一覧用フィルタ
     * @return $this
     */
    public function filterKeyPhraseList(): LearningRepositoryInterface
    {
        $query = $this->getQuery();
        $query
            ->join('tbl_truth', 'tbl_truth.api_id', '=', "{$this->model->getTable()}.api_id")
            ->join('tbl_key_phrase', 'tbl_key_phrase.key_phrase_id', '=', 'tbl_truth.key_phrase_id');
        return $this;
    }

    /**
     * キーフレーズ追加フィルタ
     * @return $this
     */
    public function filterAddKeyPhrase(): LearningRepositoryInterface
    {
        //MEMO:MySQLに依存するので注意※
        //FIXME:グループコンキャット使ってキーフレーズとくっつける
        $query = $this->getQuery();
        $this->filterKeyPhraseList();
        $query->select("{$this->model->getTable()}.*", $this->getJoinKeyPhraseColumns())
            ->groupBy("{$this->model->getTable()}.api_id");
        return $this;
    }

    /**
     * キーフレーズ結合用カラム取得
     * @param string $as
     * @return \Illuminate\Database\Query\Expression
     */
    private function getJoinKeyPhraseColumns($as = ' as key_phrase')
    {
        return DB::raw('GROUP_CONCAT(case when replace_word is null then word else replace_word end separator \' \')' . $as);
    }

    /**
     * Get join key phrase columns not separator
     * @param string $as
     * @return \Illuminate\Database\Query\Expression
     */
    private function getJoinKeyPhraseColumnsNotSeparator($as = ' as key_phrase')
    {
        return DB::raw('GROUP_CONCAT(case when replace_word is null then word else replace_word end )' . $as);
    }

    /**
     * 次のAPI_IDを取得
     * @return int
     */
    public function getNextApiId(): int
    {
        return parent::getNextId('api_id');
    }

    /**
     * Search learning
     * @param array $filter
     * @return $this
     */
    public function findBy(array $filter)
    {
        $base_tbl = $this->model->getTable();
        $query = $this->getQuery();
        $query->select('tbl_learning.*', $this->getJoinKeyPhraseColumnsNotSeparator());
        $query->join('tbl_truth', 'tbl_truth.api_id', '=', 'tbl_learning.api_id');
        $query->join('tbl_key_phrase', 'tbl_key_phrase.key_phrase_id', '=', 'tbl_truth.key_phrase_id');
        //Params
        if (!empty($filter['apiId'])) {
            if ($filter['type'] == 'add') {
                $query->where($base_tbl . '.api_id', $filter['apiId']);
            }
            if ($filter['type'] == 'search') {
                $query->where($base_tbl . '.api_id', 'like', '%' . $filter['apiId'] . '%');
            }
        }
        if (!empty($filter['category_id'])) {
            $query->where($base_tbl . '.category_id', $filter['category_id']);
        }
        if (!empty($filter['keyword'])) {
            $query->where(function ($q) use ($filter, $base_tbl) {
                $q->where($base_tbl . '.question', 'like', '%' . $filter['keyword'] . '%');
                $q->orWhere($base_tbl . '.answer', 'like', '%' . $filter['keyword'] . '%');
            });
        }
        $query->groupBy('tbl_learning.api_id');
        $find = $query->get();
        return $find ? $find->toArray() : null;
    }

    /**
     * Join table scenario by table learning.category_id
     * @param $category_id
     * @return $this
     */
    public function joinScenario($scenario_id, $category_id = null, $node = null): LearningRepositoryInterface
    {
        $query = $this->clearQuery();
        $query->select("{$this->model->getTable()}.*", "{$this->model->getTable()}.id as answer_id", $this->getJoinScenarioRelationColumns(), $this->getJoinKeyPhraseColumnsNotSeparator());
        $query->join('tbl_scenario_learning_relation', 'tbl_scenario_learning_relation.api_id', '=', $this->model->getTable().'.api_id');
        $this->filterKeyPhraseList();
        if ($node != null) {
            $query->where('tbl_scenario_learning_relation.node_id', '!=', 0)
                ->groupBy("tbl_scenario_learning_relation.node_id");
        } else {
            $query->where('tbl_scenario_learning_relation.node_id', '=', 0);
        }
        $query->whereIn('tbl_scenario_learning_relation.scenario_id', $scenario_id)
            ->orderBy('order', 'ASC')
            ->groupBy("{$this->model->getTable()}.api_id");
        return $this;
    }

    /**
     * Select col table join
     * 
     * @param string $as
     * @return \Illuminate\Database\Query\Expression
     */
    public function getJoinScenarioRelationColumns($as = 'scenario_id')
    {
        return DB::raw('GROUP_CONCAT(tbl_scenario_learning_relation.scenario_id) AS '. $as .', tbl_scenario_learning_relation.order, tbl_scenario_learning_relation.node_id');
    }
    
    /**
     * Count data learning
     * @return int number
     */
    public function countDataLearning()
    {
        $query = $this->clearQuery();
        $query->whereRaw('synced_at is NULL')
            ->orWhere(DB::raw('DATE_FORMAT(update_at,\'%Y-%m-%d %H:%i:%S\')'), '>', DB::raw('DATE_FORMAT(synced_at,\'%Y-%m-%d %H:%i:%S\')'));
        return $query->count();
    }
}
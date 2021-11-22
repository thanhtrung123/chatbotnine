<?php

namespace App\Repositories\ResponseAggregate;

use App\Models\ResponseAggregate;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\ResponseAggregate\ResponseAggregateRepositoryInterface;
use DB;

/**
 * 応答情報集計リポジトリ
 * Class ResponseAggregateRepository
 * @package App\Repositories\ResponseAggregate
 */
class ResponseAggregateRepository extends AbstractRepository implements ResponseAggregateRepositoryInterface
{

    /**
     * モデルクラス名取得
     * @return mixed|string
     */
    public function getModelClass()
    {
        return ResponseAggregate::class;
    }

    /**
     * パラメータでフィルタリング実行
     * @return RepositoryInterface
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        if (isset($this->dt_params['mode'])) {
            switch ($this->dt_params['mode']) {
                case config('const.aggregate.overview.week.id'):
                    $query->whereIn('aggregate_date', $this->dt_params['week']);
                    break;
                case config('const.aggregate.overview.month.id'):
                    $query->where(DB::raw('DATE_FORMAT(aggregate_date,\'%Y%m\')'), $this->dt_params['month']);
                    break;
                default:
                    if (!empty($this->dt_params['date_s'])) {
                        $query->where('aggregate_date', '>=', $this->dt_params['date_s']);
                    }
                    if (!empty($this->dt_params['date_e'])) {
                        $query->where('aggregate_date', '<=', $this->dt_params['date_e']);
                    }
                    break;
            }
        }

        if (isset($this->params['rotate_date'])) {
            $query->where('aggregate_date', '<', $this->params['rotate_date']);
        }

        if (isset($this->params['aggregate_date'])) {
            $query->where('aggregate_date', $this->params['aggregate_date']);
        }

        return $this;
    }

    /**
     * 集計フィルタ
     * @param $type
     * @return $this|\App\Repositories\ResponseAggregate\ResponseAggregateRepositoryInterface
     */
    public function filterAggregate($type)
    {
        $query = $this->getQuery();
        switch ($type) {
            case config('const.aggregate.tab.many_question.id'):
                $this->buildQuestionRankingQuery(config('const.aggregate.type.question.id'));
                break;
            case config('const.aggregate.tab.feedback_no_question.id'):
                $this->buildQuestionRankingQuery(config('const.aggregate.type.feedback_no.id'));
                break;
            case config('const.aggregate.tab.feedback_yes_question.id'):
                $this->buildQuestionRankingQuery(config('const.aggregate.type.feedback_yes.id'));
                break;
            case config('const.aggregate.tab.no_answer_question.id'):
                $this->buildNoAnswerRankingQuery(config('const.aggregate.type.no_one_time_answer.id'));
                break;
        }
        return $this;
    }

    /**
     * 質問ランキング用クエリ
     * @param $order_id
     */
    private function buildQuestionRankingQuery($order_id)
    {
        $query = $this->getQuery();
        $id1 = config('const.aggregate.type.question.id');
        $id2 = config('const.aggregate.type.feedback_no.id');
        $id3 = config('const.aggregate.type.feedback_yes.id');
        $query->select('group_id',
            DB::raw("sum(case when aggregate_type={$id1} then total_value else 0 end) as aggregate_{$id1}"),
            DB::raw("sum(case when aggregate_type={$id2} then total_value else 0 end) as aggregate_{$id2}"),
            DB::raw("sum(case when aggregate_type={$id3} then total_value else 0 end) as aggregate_{$id3}"),
            'tl.question',
            'tl.answer',
            'tl.id as learning_id'
        )
            ->where('aggregate_base', config('const.aggregate.base.learning.id'))
            ->whereIn('aggregate_type', [$id1, $id2, $id3])
            ->groupBy('group_id')
            ->orderBy("aggregate_{$order_id}", 'desc')
            ->leftJoin('tbl_learning as tl', 'tbl_response_aggregate.group_id', '=', 'tl.api_id');
    }

    /**
     * 回答なしランキング用クエリ
     * @param $order_id
     */
    private function buildNoAnswerRankingQuery($order_id)
    {
        $query = $this->getQuery();
        $id1 = config('const.aggregate.type.no_one_time_answer.id');
        $query->select('group_string',
            DB::raw("sum(case when aggregate_type={$id1} then total_value else 0 end) as aggregate_{$id1}")
        )
            ->where('aggregate_base', config('const.aggregate.base.question.id'))
            ->whereIn('aggregate_type', [$id1])
            ->groupBy('group_string')
            ->orderBy("aggregate_{$order_id}", 'desc');
    }

    /**
     * 概要用フィルタ
     * @param $type
     * @return $this|ResponseAggregateRepositoryInterface
     */
    public function filterOverview($type)
    {
        $query = $this->getQuery();
        $id1 = config('const.aggregate.type.user.id');
        $id2 = config('const.aggregate.type.question.id');
        $id3 = config('const.aggregate.type.feedback_yes.id');
        $id4 = config('const.aggregate.type.feedback_no.id');

        switch ($type) {
            case config('const.aggregate.overview.week.id'):
                $query->whereIn('aggregate_date', $this->params['weeks']);
                break;
            case config('const.aggregate.overview.month.id'):
                $query->where(DB::raw('DATE_FORMAT(aggregate_date,\'%Y%m\')'), $this->params['ym']);
                break;
        }
        $query->select(
            DB::raw("sum(case when aggregate_type={$id1} then total_value else 0 end) as aggregate_{$id1}"),
            DB::raw("sum(case when aggregate_type={$id2} then total_value else 0 end) as aggregate_{$id2}"),
            DB::raw("sum(case when aggregate_type={$id3} then total_value else 0 end) as aggregate_{$id3}"),
            DB::raw("sum(case when aggregate_type={$id4} then total_value else 0 end) as aggregate_{$id4}")
        )
            ->where('aggregate_base', config('const.aggregate.base.all.id'))
            ->orderBy('aggregate_date', 'desc');

        return $this;
    }

    /**
     * エラー用フィルタ
     * @return $this|ResponseAggregateRepositoryInterface
     */
    public function filterError()
    {
        $query = $this->getQuery();
        $query->select('aggregate_date')->distinct()
            ->where('aggregate_type', config('const.aggregate.type.error.id'))
            ->orderBy('aggregate_date', 'asc');
        return $this;
    }


}
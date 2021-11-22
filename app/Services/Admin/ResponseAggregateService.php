<?php

namespace App\Services\Admin;

use App\Repositories\DatatablesResult;
use App\Services\RepositoryServiceInterface;
use App\Repositories\ResponseAggregate\ResponseAggregateRepositoryInterface;
use Util;
use Carbon\Carbon;

/**
 * 応答情報集計サービス
 * Class ResponseAggregateService
 * @package App\Services\Admin
 */
class ResponseAggregateService implements RepositoryServiceInterface
{
    /**
     * @var ResponseAggregateRepositoryInterface
     */
    private $repository;

    /**
     * ResponseAggregateService constructor.
     * @param ResponseAggregateRepositoryInterface $repository
     */
    public function __construct(ResponseAggregateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * リポジトリ取得
     * @return ResponseAggregateRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * ランキングデータ取得
     * @param $tab
     * @param $params
     * @return DatatablesResult
     */
    public function getRankingData($tab, $params)
    {
        $dt_params = $this->repository->setParams($params)->getParams(true);
        $period = $this->getOverviewPeriod();
        switch ($dt_params['mode']) {
            case config('const.aggregate.overview.week.id'):
                $dt_params['week'] = $period['week']['data'][$dt_params['weeks']];
                break;
            case config('const.aggregate.overview.month.id'):
                $dt_params['month'] = $period['month']['data'][$dt_params['months']];
                break;
            default:
                break;
        }

        $params['params'] = $dt_params;
        $datatable = $this->repository->setParams($params)->filterByParams()->filterAggregate($tab)->datatable();
        switch ($tab) {
            case config('const.aggregate.tab.many_question.id'):
            case config('const.aggregate.tab.feedback_yes_question.id'):
            case config('const.aggregate.tab.feedback_no_question.id'):
                $datatable->getConverter()->setConvert(['question', 'answer'], function ($val) {
                    return str_omit_tooltip($val, config('bot.common.str_omit_length'));
                });
                break;
            case config('const.aggregate.tab.no_answer_question.id'):
                $datatable->getConverter()->setConvert(['group_string'], function ($val) {
                    return str_omit_tooltip($val, config('bot.common.str_omit_length'));
                });
                break;
        }
        return $datatable;
    }

    /**
     * 概要データ取得
     * @param $type
     * @param $params
     * @return DatatablesResult
     */
    public function getOverviewData($type, $params)
    {
        $data = [];
        $count = 0;
        $period = $this->getOverviewPeriod();
        switch ($type) {
            case config('const.aggregate.overview.week.id'):
                $week_arys = $period['week']['data'];
                foreach ($week_arys as $idx => $week_ary) {
                    $row = $this->repository->setParams(['weeks' => $week_ary])->filterOverview($type)->getDbResult()->getOne();
                    $row['period'] = $period['week']['names'][$idx];
                    $row['feedback_yes_rate'] = calc_rate($row['aggregate_' . config('const.aggregate.type.feedback_yes.id')], $row['aggregate_' . config('const.aggregate.type.question.id')]);
                    $row['feedback_no_rate'] = calc_rate($row['aggregate_' . config('const.aggregate.type.feedback_no.id')], $row['aggregate_' . config('const.aggregate.type.question.id')]);
                    $data[] = $row;
                }
                break;
            case config('const.aggregate.overview.month.id'):
                $month_ary = $period['month']['data'];
                foreach ($month_ary as $idx => $ym) {
                    $row = $this->repository->setParams(['ym' => $ym])->filterOverview($type)->getDbResult()->getOne();
                    $row['period'] = $period['month']['names'][$idx];
                    $row['feedback_yes_rate'] = calc_rate($row['aggregate_' . config('const.aggregate.type.feedback_yes.id')], $row['aggregate_' . config('const.aggregate.type.question.id')]);
                    $row['feedback_no_rate'] = calc_rate($row['aggregate_' . config('const.aggregate.type.feedback_no.id')], $row['aggregate_' . config('const.aggregate.type.question.id')]);
                    $data[] = $row;
                }
                break;
        }
        return new DatatablesResult($data, $count, $params['draw'] + 1);
    }

    /**
     * 集計期間取得
     * @return array
     */
    public function getOverviewPeriod()
    {
        $weeks = $months = [];
        $weeks_name = $months_name = [];
        $week_arys = Util::getWeekAry(config('bot.aggregate.overview.base_week_day'), config('bot.aggregate.overview.display_week_num'));
        foreach ($week_arys as $idx => $week_ary) {
            $weeks_name[$idx] = "{$idx}週間前";
            $weeks[$idx] = $week_ary;
        }
        $today = Carbon::today();
        for ($idx = 0; $idx < config('bot.aggregate.overview.display_month_num'); $idx++) {
            $months_name[$idx] = "{$idx}ヶ月前";
            $months[$idx] = $today->format('Ym');
            $today->subMonth(1);
        }
        return [
            'week' => [
                'names' => $weeks_name,
                'data' => $weeks,
            ],
            'month' => [
                'names' => $months_name,
                'data' => $months,
            ],
        ];
    }

    /**
     * エラー取得
     * @return array
     */
    public function getAggregateErrors()
    {
        $errors = [];
        $gen = $this->repository->filterError()->getDbResult()->getGenerator();
        foreach ($gen as $idx => $row) {
            $errors["集計エラー" . ($idx + 1)] = "{$row['aggregate_date']} " . __('集計に失敗しました。');
        }
        return $errors;
    }
}
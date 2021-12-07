<?php

namespace App\Repositories\ResponseInfo;

use App\Models\ResponseInfo;
use App\Repositories\AbstractRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\ResponseInfo\ResponseInfoRepositoryInterface;
use App\Repositories\Traits\ByKeyword;
use App\Repositories\type;
use DB;
use Carbon\Carbon;
use App\Services\Bot\BotLogService;

/**
 * 応答情報リポジトリ
 * Class ResponseInfoRepository
 * @package App\Repositories\ResponseInfo
 */
class ResponseInfoRepository extends AbstractRepository implements ResponseInfoRepositoryInterface
{
    use ByKeyword;

    /**
     * モデルクラス名取得
     * @return string
     */
    public function getModelClass()
    {
        return ResponseInfo::class;
    }

    /**
     * 応答情報追加
     * @param array $data
     * @return bool
     */
    public function saveLog(array $data): bool
    {
        return $this->model->insert($data);
    }

    /**
     * 質問数
     * @return int|mixed
     */
    public function getQuestionCount()
    {
        return $this->query
            //聞き返しステータスID未満は問い合わせ
            ->where('status', '<', config('const.bot.status.response_select.id'))
            ->count(DB::raw('distinct chat_id,action_datetime'));
    }

    /**
     * 利用者数
     * @return int|mixed
     */
    public function getUserCount()
    {
        return $this->query->count(DB::raw('distinct chat_id'));
    }

    /**
     * 年月日フィルタ
     * @return $this
     */
    public function filterDate(): ResponseInfoRepositoryInterface
    {
        $query = $this->getQuery();
        $query->select(DB::raw('DATE_FORMAT(action_datetime,\'%Y%m%d\') as date'))->distinct();
        $query->orderBy('action_datetime', 'desc');
        return $this;
    }

    /**
     * 集計用フィルタ
     * @param $base
     * @param $type
     * @param $date
     * @return ResponseInfoRepositoryInterface
     */
    public function filterAggregate($base, $type, $date): ResponseInfoRepositoryInterface
    {
        $query = $this->getQuery();
        $exists = false;
        switch ($base) {
            case config('const.aggregate.base.all.id'):
                $exists = $this->filterAggregateAll($type);
                break;
            case config('const.aggregate.base.learning.id'):
                $exists = $this->filterAggregateLearning($type);
                break;
            case config('const.aggregate.base.question.id'):
                $exists = $this->filterAggregateQuestion($type);
                break;
        }
        $query->where(DB::raw('DATE_FORMAT(action_datetime,\'%Y%m%d\')'), '=', $date);
        if (!$exists) {
            $query->where('id', -1);
        }

        return $this;
    }

    /**
     * 集計基準：すべて用
     * @param $type
     * @return bool
     */
    private function filterAggregateAll($type)
    {
        $query = $this->getQuery();
        switch ($type) {
            case config('const.aggregate.type.user.id'):
                $query->select(DB::raw('count(distinct chat_id) as cnt'));
                break;
            case config('const.aggregate.type.question.id'):
                $query->select(DB::raw('count(distinct chat_id,talk_id) as cnt'));
                break;
            case config('const.aggregate.type.one_time_answer.id'):
                $query->select(DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.question_answer.id'))
                    ->where('is_hear_back', config('const.common.on_off.off.id'));
                break;
            case config('const.aggregate.type.no_one_time_answer.id'):
                $query->select(DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.no_answer.id'))
                    ->where('is_hear_back', config('const.common.on_off.off.id'));
                break;
            case config('const.aggregate.type.hear_back.id'):
                $query->select(DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.question_answer.id'))
                    ->where('is_hear_back', config('const.common.on_off.on.id'));
                break;
            case config('const.aggregate.type.hear_back_no_answer.id'):
                $query->select(DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.no_answer.id'))
                    ->where('is_hear_back', config('const.common.on_off.on.id'));
                break;
            case config('const.aggregate.type.feedback_yes.id'):
                $query->select(DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.feedback_yes.id'));
                break;
            case config('const.aggregate.type.feedback_no.id'):
                $query->select(DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.feedback_no.id'));
                break;
            default :
                return false;
        }
        return true;
    }

    /**
     * 集計基準：学習ID用
     * @param $type
     * @return bool
     */
    private function filterAggregateLearning($type)
    {
        $query = $this->getQuery();
        switch ($type) {
            case config('const.aggregate.type.question.id'):
                $query->select('api_id as group_id', 'api_question as group_string', DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', '<', config('const.bot.status.response_select.id'))
                    ->whereNotNull('api_id')
                    ->groupBy('api_id');
                break;
            case config('const.aggregate.type.feedback_no.id'):
                $query->select('api_id as group_id', DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.feedback_no.id'))
                    ->groupBy('api_id');
                break;
            case config('const.aggregate.type.feedback_yes.id'):
                $query->select('api_id as group_id', DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.feedback_yes.id'))
                    ->groupBy('api_id');
                break;
            default :
                return false;
        }
        return true;
    }

    /**
     * 集計基準：質問文用
     * @param $type
     * @return bool
     */
    private function filterAggregateQuestion($type)
    {
        $query = $this->getQuery();
        switch ($type) {
            case config('const.aggregate.type.no_one_time_answer.id'):
                $query->select('user_input as group_string', DB::raw('count(distinct chat_id,talk_id) as cnt'))
                    ->where('status', config('const.bot.status.no_answer.id'))
                    ->where('is_hear_back', config('const.common.on_off.off.id'))
                    ->groupBy('user_input');
                break;
            default :
                return false;
        }
        return true;
    }

    /**
     * パラメータでフィルタリング実行
     * @return $this
     */
    public function filterByParams(): RepositoryInterface
    {
        $query = $this->getQuery();
        //通常パラメータ指定
        if (!empty($this->params['id'])) {
            $query->where('id', '<>', $this->params['id']);
        }
        if (!empty($this->params['channel'])) {
            $query->where('channel', $this->params['channel']);
        }
        if (!empty($this->params['user_ip'])) {
            $query->where('user_ip', $this->params['user_ip']);
        }
        if (!empty($this->params['talk_id'])) {
            $query->where('talk_id', $this->params['talk_id']);
        }
        if (!empty($this->params['chat_id'])) {
            $query->where('chat_id', $this->params['chat_id']);
        }
        if (!empty($this->params['status'])) {
            if (is_array($this->params['status']))
                $query->whereIn('status', $this->params['status']);
            else
                $query->where('status', $this->params['status']);
        }
        if (!empty($this->params['not_status'])) {
            $query->where('status', '<>', $this->params['not_status']);
        }
        if (!empty($this->params['action_datetime'])) {
            $query->where('action_datetime', $this->params['action_datetime']);
        }

        //日付
        if (!empty($this->params['rotate_date'])) {
            $query->where(DB::raw("DATE_FORMAT(action_datetime,'%Y%m%d')"), '<', $this->params['rotate_date']);
        }

        return $this;
    }

    /**
     * パラメータでフィルタリング実行（情報一覧用）
     * @return $this
     */
    public function filterByParamsForInfoList(): ResponseInfoRepositoryInterface
    {
        $query = $this->getQuery();
        $base_tbl = $this->model->getTable();
        //datatablesパラメータ指定
        if (!empty($this->dt_params['keyword'])) {
            $keyword_columns = ["user_input", "api_question", "api_answer"];
            if (!empty($this->dt_params['keyword_columns'])) {
                $keyword_columns = $this->dt_params['keyword_columns'];
            }
            $keyword_columns = array_map(function ($val) use ($base_tbl) {
                return "{$base_tbl}.{$val}";
            }, $keyword_columns);
            $this->byKeyword($this->dt_params['keyword'], $keyword_columns);
        }
        if (!empty($this->dt_params['date_s'])) {
            $query->whereColumn(DB::raw("DATE_FORMAT({$base_tbl}.action_datetime,'%Y%m%d')"), '>=', DB::raw("DATE_FORMAT('{$this->dt_params['date_s']}','%Y%m%d')"));
        }
        if (!empty($this->dt_params['date_e'])) {
            $query->whereColumn(DB::raw("DATE_FORMAT({$base_tbl}.action_datetime,'%Y%m%d')"), '<=', DB::raw("DATE_FORMAT('{$this->dt_params['date_e']}','%Y%m%d')"));
        }
        if (!empty($this->dt_params['feedback'])) {
            $query->whereIn("{$base_tbl}.status", $this->dt_params['feedback']);
        }
        if (!empty($this->dt_params['status'])) {
            $query->whereIn("tri2.status", $this->dt_params['status']);
        }
        if (!empty($this->dt_params['score_s'])) {
            $query->where("{$base_tbl}.api_score", '>=', $this->dt_params['score_s']);
        }
        if (!empty($this->dt_params['score_e'])) {
            $query->where("{$base_tbl}.api_score", '<=', $this->dt_params['score_e']);
        }
        return $this;
    }

    /**
     * 情報一覧用フィルタ
     * @return $this
     */
    public function filterInfoList(): ResponseInfoRepositoryInterface
    {
        $base_tbl = $this->model->getTable();
        $query = $this->query->getQuery();
        $query->groupBy("{$base_tbl}.chat_id", "{$base_tbl}.talk_id");
        $query->select('tri2.*')
            ->join("{$base_tbl} as tri2", function ($join) use ($base_tbl) {
                $join->on("{$base_tbl}.chat_id", '=', 'tri2.chat_id')
                    ->on("{$base_tbl}.talk_id", '=', 'tri2.talk_id')
                    ->where('tri2.status', '<', config('const.bot.status.response_select.id'));
            });
        return $this;
    }

    /**
     * エクスポート用フィルタ
     * @return $this
     */
    public function filterExport(): ResponseInfoRepositoryInterface
    {
        $base_tbl = $this->model->getTable();
        $query = $this->getQuery();
        $query->select("{$base_tbl}.*", 'tl.question', 'tl.metadata')
            ->leftJoin('tbl_learning as tl', "{$base_tbl}.api_id", '=', 'tl.api_id');
        $query->orderBy("{$base_tbl}.id");
        return $this;
    }

    /**
     * ユニークなchat_idを取得
     * @return string|void
     */
    public function getUniqueChatId()
    {
        do {
            $chat_id = str_random();
        } while (!empty($this->findOneBy(['chat_id' => $chat_id])));
        return $chat_id;
    }

    /**
     * ユニークなtalk_idを取得
     * @return string|void
     */
    public function getUniqueTalkId()
    {
        do {
            $talk_id = str_random();
        } while (!empty($this->findOneBy(['talk_id' => $talk_id])));
        return $talk_id;
    }
    
    /**
     * Get data chat id folow date
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $ip
     * @param $format
     * @param $channel
     * 
     * @return mixed
     */
    public function getChatIdDate($start_date_data, $end_date_data, $ip, $format = 'date', $channel)
    {
        $query = $this->clearQuery()
            ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59');
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        switch ($format) {
            case 'date':
                $query = $query->select(DB::raw("count(distinct  chat_id) as chat_id, DATE_FORMAT(action_datetime,  '%Y-%m-%d') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%Y-%m-%d')"));
                break;
            case 'hour':
                $query = $query->select(DB::raw("count(distinct  chat_id) as chat_id, DATE_FORMAT(action_datetime,  '%k') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%k')"));
                break;
            case 'day_of_week':
                $query = $query->select(DB::raw("count(distinct chat_id) as chat_id, DATE_FORMAT(action_datetime,  '%w') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%w')"));
                break;
            case 'weeekly':
                $query = $query->select(DB::raw("chat_id, DATE_FORMAT(action_datetime,  '%Y-%m-%d') as time"));
                break;
            case 'month':
                $query = $query->select(DB::raw("count(distinct  chat_id) as chat_id, DATE_FORMAT(action_datetime,  '%Y-%m') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%Y-%m')"));
                break;
            case 'year':
                $query = $query->select(DB::raw("count(distinct  chat_id) as chat_id, DATE_FORMAT(action_datetime,  '%Y') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%Y')"));
                break;
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        if ($format == 'weeekly') {
            $query = $query->get();
        } else {
            $query = $query->pluck('chat_id', 'time')->toArray();
        }
        return $query;
    }

    /**
     * Get data talk id
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $ip
     * @param $format
     * @param $channel
     * 
     * @return mixed
     */
    public function getTalkId($start_date_data, $end_date_data, $ip, $format, $channel)
    {
        $query = $this->clearQuery()
            //聞き返しステータスID未満は問い合わせ
            ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59');
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        switch ($format) {
            case 'date' :
                $query = $query->select(DB::raw("count(distinct  talk_id) as talk_id, DATE_FORMAT(action_datetime,  '%Y-%m-%d') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%Y-%m-%d')"));
                break;
            case 'hour' :
                $query = $query->select(DB::raw("count(distinct  talk_id) as talk_id, DATE_FORMAT(action_datetime,  '%k') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%k')"));
                break;
            case 'day_of_week' :
                $query = $query->select(DB::raw("count(distinct talk_id) as talk_id, DATE_FORMAT(action_datetime,  '%w') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%w')"));
                break;
            case 'weeekly' :
                $query = $query->select(DB::raw("talk_id, DATE_FORMAT(action_datetime,  '%Y-%m-%d') as time"));
                break;
            case 'month' :
                $query = $query->select(DB::raw("count(distinct  talk_id) as talk_id, DATE_FORMAT(action_datetime,  '%Y-%m') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%Y-%m')"));
                break;
            case 'year' :
                $query = $query->select(DB::raw("count(distinct  talk_id) as talk_id, DATE_FORMAT(action_datetime,  '%Y') as time"))
                    ->groupBy(DB::raw("DATE_FORMAT(action_datetime, '%Y')"));
                break;
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        if ($format == 'weeekly') {
            $query = $query->get();
        } else {
            $query = $query->pluck('talk_id', 'time')->toArray();
        }
        return $query;
    }

    /**
     * Get number total
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $ip
     * @param $channel
     * 
     * @return int
     */
    public function getTotalDist($start_date_data, $end_date_data, $ip, $channel)
    {
        $query = $this->clearQuery()
            ->where('action_datetime', '>=', $start_date_data. ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59');
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        $query = $query->count(DB::raw('distinct chat_id'));
        return $query;
    }

    /**
     * Get data Answer
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataAnswer($start_date_data, $end_date_data, $config, $ip, $channel)
    {
        $query = $this->clearQuery()
            ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59')
            ->whereIn('status', $config);
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        $query = $query->count(DB::raw('distinct talk_id'));
        return $query;
    }
    
    /**
     * Get data no Answer
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataNoAnswer($start_date_data, $end_date_data, $config, $ip, $channel)
    {
        $query = $this->clearQuery()
            ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59')
            ->where(function ($qr) use ($start_date_data, $end_date_data, $config, $ip, $channel) {
                $qr->whereIn('status', $config)
                    ->orWhere(function ($qr1) use ($start_date_data, $end_date_data, $ip, $channel) {
                        $qr1->whereIn('talk_id', function ($qr2) use ($start_date_data, $end_date_data, $ip, $channel) {
                            $qr2->selectRaw('distinct talk_id')
                                ->from('tbl_response_info')
                                ->where('status', config('const.bot.status.question_input.id'))
                                ->whereNotIn('talk_id', function ($qr3) use ($start_date_data, $end_date_data, $ip, $channel) {
                                    $qr3->selectRaw('distinct talk_id')
                                        ->from('tbl_response_info')
                                        ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
                                        ->where('action_datetime', '<=', $end_date_data . ' 23:59:59')
                                        ->where('status', '=', config('const.bot.status.scenario_answer.id'));
                                    if (is_array($ip) && count($ip) > 0) {
                                        $qr3 = $qr3->whereNotIn('user_ip', $ip);
                                    }
                                    if ($channel) {
                                        $qr3 = $qr3->where('channel', $channel);
                                    }
                                });
                        });
                    });
            });
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        $query = $query->count(DB::raw('distinct talk_id'));
        return $query;
    }

    /**
     * Get number Answer
     * 
     * @param $dataAnswer
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return int
     */
    public function getDataAnswerHandle($start_date_data, $end_date_data, $config, $ip, $channel)
    {
        $query = $this->clearQuery()
            ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59')
            ->where('status', $config);
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        $query = $query->count(DB::raw('distinct talk_id'));
        return $query;
    }

    /**
     * Get data Answer Limit
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataAnswerLimit($start_date_data, $end_date_data, $config, $ip, $channel)
    {
        $query = $this->clearQuery()
            ->select(DB::raw("count(tbl_response_info.api_id) as api, tbl_learning.id, tbl_learning.question, tbl_learning.answer, tbl_category.name"))
            ->join('tbl_learning', 'tbl_learning.api_id', '=', $this->model->getTable().'.api_id')
            ->leftjoin('tbl_category', 'tbl_category.id', '=', 'tbl_learning.category_id')
            ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59')
            ->whereIn('status', $config)
            ->groupBy($this->model->getTable() . '.api_id')
            ->orderBy('api', 'desc')
            ->orderBy('action_datetime', 'desc');
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        $query = $query->limit(config('const.dashboard.limit_answers'))->get();
        return $query;
    }

    /**
     * Get data No Answer Limit
     * 
     * @param $start_date_data
     * @param $end_date_data
     * @param $config
     * @param $ip
     * @param $channel
     * 
     * @return mix
     */
    public function getDataNoAnswerLimit($start_date_data, $end_date_data, $config, $ip, $channel)
    {
        $query = $this->clearQuery()
            ->select(DB::raw("distinct user_input, api_score"))
            ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
            ->where('action_datetime', '<=', $end_date_data . ' 23:59:59')
            ->where(function ($qr) use ($start_date_data, $end_date_data, $config, $ip, $channel) {
                $qr->whereIn('status', $config)
                    ->orWhere(function ($qr1) use ($start_date_data, $end_date_data, $ip, $channel) {
                        $qr1->whereIn('talk_id', function ($qr2) use ($start_date_data, $end_date_data, $ip, $channel) {
                            $qr2->selectRaw('distinct talk_id')
                                ->from('tbl_response_info')
                                ->where('status', config('const.bot.status.question_input.id'))
                                ->whereNotIn('talk_id', function ($qr3) use ($start_date_data, $end_date_data, $ip, $channel) {
                                    $qr3->selectRaw('distinct talk_id')
                                        ->from('tbl_response_info')
                                        ->where('action_datetime', '>=', $start_date_data . ' 00:00:00')
                                        ->where('action_datetime', '<=', $end_date_data . ' 23:59:59')
                                        ->where('status', '=', config('const.bot.status.scenario_answer.id'));
                                    if (is_array($ip) && count($ip) > 0) {
                                        $qr3 = $qr3->whereNotIn('user_ip', $ip);
                                    }
                                    if ($channel) {
                                        $qr3 = $qr3->where('channel', $channel);
                                    }
                                });
                        });
                    });
            })
            ->orderBy('action_datetime', 'desc');
        if (is_array($ip) && count($ip) > 0) {
            $query = $query->whereNotIn('user_ip', $ip);
        }
        if ($channel) {
            $query = $query->where('channel', $channel);
        }
        $query = $query->limit(config('const.dashboard.limit_answers'))->get();
        return $query;
    }
}
<?php

namespace App\Services\Admin;

use App\Services\RepositoryServiceInterface;
use App\Repositories\EnqueteAnswer\EnqueteAnswerRepositoryInterface;
use App\Repositories\ResponseInfo\ResponseInfoRepositoryInterface;
use Carbon\Carbon;
use Storage;
use File;

/**
 * Class DashboardService
 * @package App\Services\Admin
 */
class DashboardService
{
    use LogTrait;

    /**
     * @var ResponseInfoRepositoryInterface
     */
    private $response_info;
    
    /**
     * @var EnqueteAnswerRepositoryInterface
     */
    private $enquete_answer;
    // Save range weekly
    private $week_range = array();
    // Start Date
    private $start_date = '';
    // End Date
    private $end_date = '';
    
     /**
     * DashboardService constructor
     * @param ResponseInfoRepositoryInterface $response_info
     * @param EnqueteAnswerRepositoryInterface $enquete_answer
     */
    public function __construct(ResponseInfoRepositoryInterface $response_info, EnqueteAnswerRepositoryInterface $enquete_answer) {
        $this->response_info = $response_info;
        $this->enquete_answer = $enquete_answer;
    }

    /**
     * Get data Statistical
     * 
     * @param filter
     * 
     * @return array
     */
    public function getDataStatistical($filter) {
        $this->start_date = '';
        $this->end_date = '';
        $start_date_data = ($filter['date_s'] ?? NULL) ? date('Y-m-d', strtotime($filter['date_s'])) : Carbon::now()->startOfMonth();
        $end_date_data = ($filter['date_e'] ?? NULL) ? date('Y-m-d', strtotime($filter['date_e'])) : Carbon::now()->endOfMonth();
        if ($start_date_data > $end_date_data) {
            $start_date_data = $end_date_data;
            $end_date_data =  ($filter['date_s'] ?? NULL) ? date('Y-m-d', strtotime($filter['date_s'])) : Carbon::now()->startOfMonth();
        }
        $this->start_date = $start_date_data;
        $this->end_date = $end_date_data;
        // Get params ip
        $ip_adress = ($filter['ip'] ?? NULL);
        // Convert string params ip to array
        $ip_adress_ary = array_filter(preg_split("/,| |　|\n|\r|\n\r/", $ip_adress));
        // Get params channel
        $channel = ($filter['channel'] ?? NULL);
        // $days_between = ceil(abs(strtotime($start_date_data) - strtotime($end_date_data)) / 86400);
        $total_user = array();
        $total = 0;
        $start_date_data_time = strtotime($start_date_data);
        $end_date_data_time = strtotime($end_date_data);
        $date_diff = (round(($end_date_data_time - $start_date_data_time) / (60 * 60 * 24))) ? round(($end_date_data_time - $start_date_data_time) / (60 * 60 * 24)) : 1;
        // Process chat
        switch ($date_diff) {
            // Date
            case ($date_diff <= config('const.dashboard.date_limit')):
                 // Get data chat ID
                $data_chat_id = $this->response_info->getChatIdDate($start_date_data, $end_date_data, $ip_adress_ary, 'date', $channel);
                $list_date = $this->getListDate($start_date_data, $end_date_data);
                $list_date_export =  $list_date;
                $data_result = array();
                foreach($list_date as $date) {
                    $count = $data_result[] = ($data_chat_id[$date] ?? NULL) ? $data_chat_id[$date] : 0;
                    $total = $total + $count;
                }
                $total_user['user_date'] = $total;
                break;
            // Week
            case ($date_diff > config('const.dashboard.date_limit') AND $date_diff < config('const.dashboard.week_limit')):
                $data_chat_id = $this->response_info->getChatIdDate($start_date_data, $end_date_data, $ip_adress_ary, 'weeekly', $channel);
                $this->rangeWeek($start_date_data, $end_date_data);
                $data_result = array();
                $data_temp = array();
                $list_date = array();
                $list_date_export = array();
                foreach($this->week_range as $weekly) {
                    //WeekyOfMonth
                    $week_month = weekOfMonth(strtotime($weekly['start']));
                    $month = Carbon::parse($weekly['start'])->format("m");
                    $data_temp[$week_month.$month] = [];
                    $list_date[] = '第'.$week_month.'週('.$month.'月)';
                    //$list_date_export[] = '第'.$week_month.'週('.Carbon::parse($weekly['start'])->format("m月d日").'～'.Carbon::parse($weekly['end'])->format("m月d日").')';
                }
                $list_date_export = $list_date;
                foreach ($data_chat_id as $key => $obj_date) {
                    $week_month = weekOfMonth(strtotime(object_get($obj_date, 'time')));
                    $month = Carbon::parse(object_get($obj_date, 'time'))->format("m");
                    $data_temp[$week_month.$month][] = object_get($obj_date, 'chat_id');
                }
                foreach($data_temp as $data) {
                    $data_result[] = count(array_unique($data));
                }
                $total_user['user_date'] = array_sum($data_result);
                break;
            // Month
            case ($date_diff >= config('const.dashboard.week_limit') AND $date_diff < config('const.dashboard.month_limit')):
                // Get chat ID
                $data_chat_id = $this->response_info->getChatIdDate($start_date_data, $end_date_data, $ip_adress_ary, 'month', $channel);
                $month_ary = rangeMonth($start_date_data, $end_date_data);
                $data_result = array();
                $list_date = array();
                $list_date_export = array();
                foreach($month_ary as $date) {
                    $list_date[] = date('Y年m月', strtotime($date));
                    $count = $data_result[] = ($data_chat_id[$date] ?? NULL) ? $data_chat_id[$date] : 0;
                    $total = $total + $count;
                }
                $list_date_export = $list_date;
                $total_user['user_date'] = $total;
                break;
            // Year
            case ($date_diff >= config('const.dashboard.month_limit')):
                // Get chat ID
                $data_chat_id = $this->response_info->getChatIdDate($start_date_data, $end_date_data, $ip_adress_ary, 'year', $channel);
                $year_ary = rangeYear($start_date_data, $end_date_data);
                $list_date = array();
                $list_date_export = array();
                foreach($year_ary as $date) {
                    $list_date[] = $date .'年';
                    // $list_date_export[] =  $date .'年';
                    $count = $data_result[] = ($data_chat_id[$date] ?? NULL) ? $data_chat_id[$date] : 0;
                    $total = $total + $count;
                }
                $list_date_export = $list_date;
                $total_user['user_date'] = $total;
                break;
        }
        // Get data chat Id Hour
        $list_hour = $this->getListHour();
        $data_chat_hour = $this->response_info->getChatIdDate($start_date_data, $end_date_data, $ip_adress_ary, 'hour', $channel);
        $data_result_hour = array();
        $total = 0;
        foreach($list_hour as $hour) {
            $count = $data_result_hour[] = ($data_chat_hour[$hour] ?? NULL) ? $data_chat_hour[$hour] : 0;
            $total = $total + $count;
        }
        $total_user['user_hour'] = $total;
        // Get data chat day of week
        $list_day_of_week = $this->getListDayOfWeek();
        $data_chat_day_of_week = $this->response_info->getChatIdDate($start_date_data, $end_date_data, $ip_adress_ary, 'day_of_week', $channel);
        $data_result_day_of_week = array();
        $total = 0;
        foreach($list_day_of_week as $id_day => $day) {
            $count = $data_result_day_of_week[] = ($data_chat_day_of_week[$id_day] ?? NULL) ? $data_chat_day_of_week[$id_day] : 0;
            $total = $total + $count;
        }
        $total_user['user_day_of_week'] = $total;
        $total = 0;
        // Process talk
        switch ($date_diff) {
            case ($date_diff <= config('const.dashboard.date_limit')):
                 // Get talk ID
                $data_talk_list = $this->response_info->getTalkId($start_date_data, $end_date_data, $ip_adress_ary, 'date', $channel);
                foreach($list_date as $date) {
                    $count = $data_talk[] = ($data_talk_list[$date] ?? NULL) ? $data_talk_list[$date] : 0;
                    $total = $total + $count;
                }
                $total_user['user_talk'] = $total;
                ($total > 0) ? $total_user['user_talk_avg'] = round($total/count($list_date), 2) : $total_user['user_talk_avg'] = 0;
                break;
            case ($date_diff > config('const.dashboard.date_limit') AND $date_diff < config('const.dashboard.week_limit')):
                $data_talk = array();
                $data_temp = array();
                $data_talk_list = $this->response_info->getTalkId($start_date_data, $end_date_data, $ip_adress_ary, 'weeekly', $channel);
                foreach($this->week_range as $weekly) {
                    //WeekyOfMonth
                    $week_month = weekOfMonth(strtotime($weekly['end']));
                    $month = Carbon::parse($weekly['start'])->format("m");
                    $data_temp[$week_month.$month] = array();
                }
                foreach ($data_talk_list as $date => $objTalk) {
                    $week_month = weekOfMonth(strtotime(object_get($objTalk, 'time')));
                    $month = Carbon::parse(object_get($objTalk, 'time'))->format("m");
                    $data_temp[$week_month.$month][] = object_get($objTalk, 'talk_id');
                }
                foreach($data_temp as $data) {
                    $data_talk[] = count(array_unique($data));
                }
                $total_user['user_talk'] = array_sum($data_talk);
                $this->week_range = array();
                break;
            case ($date_diff >= config('const.dashboard.week_limit') AND $date_diff < config('const.dashboard.month_limit')):
                 // Get talk ID
                $data_talk_list = $this->response_info->getTalkId($start_date_data, $end_date_data, $ip_adress_ary, 'month', $channel);
                $data_talk = array();
                foreach($month_ary as $date) {
                    $count = $data_talk[] = ($data_talk_list[$date] ?? NULL) ? $data_talk_list[$date] : 0;
                    $total = $total + $count;
                }
                $total_user['user_talk'] = $total;
                break;
            case ($date_diff >= config('const.dashboard.month_limit')):
                // Get talk ID
                $data_talk_list = $this->response_info->getTalkId($start_date_data, $end_date_data, $ip_adress_ary, 'year', $channel);
                $data_talk = array();
                foreach($year_ary as $date) {
                    $count = $data_talk[] = ($data_talk_list[$date] ?? NULL) ? $data_talk_list[$date] : 0;
                    $total = $total + $count;
                }
                $total_user['user_talk'] = $total;
                break;
        }
        $data_talk_hour_list = $this->response_info->getTalkId($start_date_data, $end_date_data, $ip_adress_ary, 'hour', $channel);
        $data_talk_hour = array();
        $total = 0;
        foreach($list_hour as $hour) {
            $count = $data_talk_hour[] = ($data_talk_hour_list[$hour] ?? NULL) ? $data_talk_hour_list[$hour] : 0;
            $total = $total + $count;
        }
        $total_user['user_talk_hour'] = $total;
        ($total > 0) ? $total_user['user_talk_hour_avg'] = round($total/12, 2) : $total_user['user_talk_hour_avg'] = 0;

        $data_total_day_list = $this->response_info->getTalkId($start_date_data, $end_date_data, $ip_adress_ary, 'day_of_week', $channel);
        $data_talk_day_of_week = array();
        $total = 0;
        foreach($list_day_of_week as $id_day => $day) {
            $count = $data_talk_day_of_week[] = ($data_total_day_list[$id_day] ?? NULL) ? $data_total_day_list[$id_day] : 0;
            $total = $total + $count;
        }
        $total_user['user_talk_day_of_week'] = $total;
        ($total > 0) ? $total_user['user_talk_day_avg'] = round($total/6, 2) : $total_user['user_talk_day_avg'] = 0;
        // Get Total
        $total_user['user_unique'] = $this->response_info->getTotalDist($start_date_data, $end_date_data, $ip_adress_ary, $channel);
        array_walk($list_hour, function(&$value, $key) { $value .= '時'; } );
        return [
            'date' => $list_date,
            'date_export' => $list_date_export,
            'data_result' => $data_result,
            'hour' => $list_hour,
            'data_hour' =>$data_result_hour,
            'day_of_week' => $list_day_of_week,
            'data_day_of_week' => $data_result_day_of_week,
            'data_talk' =>$data_talk,
            'data_talk_hour' => $data_talk_hour,
            'data_talk_day_of_week' => $data_talk_day_of_week,
            'total_user' => $total_user
        ];
    }
    
    /**
     * Get data answer Statistical
     * 
     * @param filter
     * 
     * @return array
     */
    public function getDataAnswerStatistical($filter) {
        // Get start date
        $start_date_data = $this->start_date;
        // Get end date
        $end_date_data = $this->end_date;
        // Get ip address
        $ip_adress = ($filter['ip'] ?? NULL);
        // Convert ip string become array
        $ip_adress_ary = array_filter(preg_split("/,| |　|\n|\r|\n\r/", $ip_adress));
        // Get channel
        $channel = ($filter['channel'] ?? NULL);
        // Get the question answers
        $config_anwers = [
            config('const.bot.status.question_answer.id'),
            config('const.bot.status.scenario_answer.id'),
            config('const.bot.status.related_answer.id'),
        ];
        // Get data question answers
        $data_answer = $this->response_info->getDataAnswer($start_date_data, $end_date_data, $config_anwers, $ip_adress_ary, $channel);
        // Get the question no answers
        $config_no_anwers = [
            config('const.bot.status.no_answer.id'),
            config('const.bot.status.scenario_no_answer.id')
        ];
        // Get data no question answers
        $data_no_answer = $this->response_info->getDataNoAnswer($start_date_data, $end_date_data, $config_no_anwers, $ip_adress_ary, $channel);
        $config_handle = config('const.bot.status.feedback_yes.id');
        $data_answer_handle = $this->response_info->getDataAnswerHandle($start_date_data, $end_date_data, $config_handle, $ip_adress_ary, $channel);
        $config_not_handle = config('const.bot.status.feedback_no.id');
        $data_answer_not_handle = $this->response_info->getDataAnswerHandle($start_date_data, $end_date_data, $config_not_handle, $ip_adress_ary, $channel);
        $data_answer_yet_handle = $data_answer - ($data_answer_handle + $data_answer_not_handle);
        // Get question popular answer
        $quest_popular_list = $this->response_info->getDataAnswerLimit($start_date_data, $end_date_data, $config_anwers, $ip_adress_ary, $channel);
        // Get question popular no answer
        $quest_popular_no_list = $this->response_info->getDataNoAnswerLimit($start_date_data, $end_date_data, $config_no_anwers, $ip_adress_ary, $channel);
        return [
            'count_answer' => $data_answer,
            'count_no_answer' => $data_no_answer,
            'count_answer_handle' => $data_answer_handle,
            'count_answer_no_handle' => $data_answer_not_handle,
            'count_answer_yet_handle' => $data_answer_yet_handle,
            'quest_popular_list' => $quest_popular_list,
            'quest_popular_no_list' => $quest_popular_no_list
        ];
    }

    /**
     * Get data enquete Statistical
     * 
     * @param filter
     * 
     * @return array
     */
    public function getDataEnqueteStatistical($filter) {
        $start_date_data = $this->start_date;
        $end_date_data = $this->end_date;
        $ip_adress = $filter['ip'] ?? NULL;
        $ip_adress_ary = array_filter(preg_split("/,| |　|\n|\r|\n\r/", $ip_adress));
        $channel = $filter['channel'] ?? NULL;
        $get_form_enquete = config('enquete.form.' . config('const.enquete.form_id.user_form.id') . '.items');
        $key_question_code = array();
        foreach ($get_form_enquete as $key => $value) {
            if (($value['items'] ?? NULL)) {
                $key_question_code[] = $key;
            }
        }
        $data_enquete_answer_list = array();
        // Get data enquete
        $get_list_enquete_answer = $this->enquete_answer->getEnqueteAnswer($start_date_data, $end_date_data, $key_question_code, $ip_adress_ary, $channel);
        if (count($get_list_enquete_answer) < 1) {
            return $data_enquete_answer_list;
        }
        $data_enquete_answer = array();
        foreach ($get_list_enquete_answer as $enquete_answer) {
            if (strpos(object_get($enquete_answer, 'answer'), ',') !== false) {
                $answer_ary = explode(',', object_get($enquete_answer, 'answer'));
                foreach ($answer_ary as $answer) {
                    if (!empty(($data_enquete_answer[object_get($enquete_answer, 'question_code')] ?? NULL))) {
                        $questCode = $answer;
                        if (!empty(($data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] ?? NULL))) {
                            $count = $data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] + 1;
                            $data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] = $count;
                        } else {
                            $data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] = 1;
                        }
                    } else {
                        $data_enquete_answer[object_get($enquete_answer, 'question_code')] = [
                            $answer => 1,
                        ];
                    }
                }
            } else {
                if (!empty(($data_enquete_answer[object_get($enquete_answer, 'question_code')] ?? NULL))) {
                    $questCode = (object_get($enquete_answer, 'answer') != NULL) ? object_get($enquete_answer, 'answer') : 'no';
                    if (!empty(($data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] ?? NULL))) {
                        $count = $data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] + 1;
                        $data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] = $count;
                    } else {
                        $data_enquete_answer[object_get($enquete_answer, 'question_code')][$questCode] = 1;
                    }
                } else {
                    if (object_get($enquete_answer, 'answer') == NULL) {
                        $data_enquete_answer[object_get($enquete_answer, 'question_code')] = [
                            'no' => 1,
                        ];
                    } else {
                        $data_enquete_answer[object_get($enquete_answer, 'question_code')] = [
                            object_get($enquete_answer, 'answer') => 1,
                        ];
                    }
                    
                }
            }
        }
        foreach ($key_question_code as $question) {
            // get question Name
            $question_name = $get_form_enquete[$question]['question'];
            // get items
            $items =  $get_form_enquete[$question]['items'];
            $background_color_ary = array();
            $item_ary = array();
            foreach($items as $key => $item) {
                if (is_array($item)) {
                    $item_ary[$item['id']] = $item['name'];
                } else {
                    $item_ary[$key] = $item;
                }
            }
            $item_num = count($item_ary);
            if ($item_num >= 5) {
                $bg_color = config('const.dashboard.background_color_in_20');
                $num_color = floor(count($bg_color) / $item_num);
                $bg_color_ary = array_chunk($bg_color, $num_color);
                for ($num = 0; $num < $item_num; $num++) {
                    $background_color_ary[] = $bg_color_ary[$num][0] ?? NULL;
                }
            } else {
                $bg_color = config('const.dashboard.background_color_in_5');
                for ($num = 0; $num < $item_num; $num++) {
                    $background_color_ary[] = $bg_color[$num] ?? NULL;
                }
            }
            $item_ary['no'] = '未回答';
            $background_color_ary[] = config('const.dashboard.background_color_default');
            $count_answers = array();
            $item_key_ary = array_keys($item_ary);
            foreach($item_key_ary as $key) {
                $count_answers[$key] = ($data_enquete_answer[$question][$key] ?? NULL) ? $data_enquete_answer[$question][$key] : 0;
            }
            $data_enquete_answer_list[] = [
                'question_name' => $question_name,
                'item_data' => $item_ary,
                'item_value' => $count_answers,
                'background_color' => $background_color_ary
            ];
        }
        return $data_enquete_answer_list;
    }
    
    /**
     * Get list date
     * 
     * @param start_date_data
     * @param end_date_data
     * 
     * @return array
     */
    public function getListDate($start_date_data, $end_date_data) {
        $start_date = new \DateTime($start_date_data);
        $end_date = new \DateTime($end_date_data);
        $data = array();
        for ($date = $start_date; $date <= $end_date; $date->modify('+1 day')) {
            $data[] = $date->format('Y-m-d');
        }
        return $data;
    }
    
    /**
     * Get list hour
     * 
     * @return array
     */
    public function getListHour() {
        $hour_total = 23;
        $data = array();
        for ($hour = 0; $hour <= $hour_total; $hour++) {
            $data[] = $hour;
        }
        return $data;
    }

    /**
     * Get list day of week
     * 
     * @return array
     */
    public function getListDayOfWeek() {
        $week = array(); 
        for ($i = 0; $i <7; $i++) {
            switch ($i) {
                case '0' :
                    $week[0] = '日曜日';
                    break;
                case '1' :
                    $week[1] = '月曜日';
                    break;
                case '2' :
                    $week[2] = '火曜日';
                    break;
                case '3' :
                    $week[3] = '水曜日';
                    break;
                case '4' :
                    $week[4] = '木曜日';
                    break;
                case '5' :
                    $week[5] = '金曜日';
                    break;
                case '6' :
                    $week[6] = '土曜日';
                    break;
            } 
        }
        return $week;
    }

    /**
     * Range week
     * 
     * @param $from
     * @param $to
     * @param $num
     * 
     * @return
     */
    public function rangeWeek($from, $to, $num = 0) {
        if (strtotime($from) <= strtotime($to)) {
            if ($num == 0) {
                $start_week = Carbon::parse($from)->format('Y-m-d');
                if (Carbon::parse($start_week)->isWeekend() == true AND strtotime(Carbon::parse($start_week)->format('Y-m-d')) == strtotime(Carbon::parse($start_week)->firstOfMonth()->format('Y-m-d'))) {
                    $start_week = Carbon::parse($start_week)->addDay(1)->format('Y-m-d');
                }
                if (Carbon::parse($start_week)->isWeekend() == true AND strtotime(Carbon::parse($start_week)->format('Y-m-d')) == strtotime(Carbon::parse($start_week)->endOfMonth()->format('Y-m-d'))) {
                    $end_week = Carbon::parse($start_week)->endOfWeek();
                } else {
                    $end_week = Carbon::parse($start_week)->endOfWeek()->subDay(1);
                }
                if ($end_week->month == Carbon::parse($start_week)->month) {
                    $this->week_range[] = [
                        'start' => $from,
                        'end' => $end_week->format('Y-m-d')
                    ];
                    $this->rangeWeek($end_week->addDay(1)->format('Y-m-d'), $to, 1);
                } else {
                    $this->week_range[] = [
                        'start' => $from,
                        'end' => Carbon::parse($start_week)->endOfMonth()
                    ];
                    $this->rangeWeek(Carbon::parse($start_week)->endOfMonth()->addDay(1)->format('Y-m-d'), $to, 1);
                }
            } else if (strtotime($from) == strtotime($to)) {
                $this->week_range[] = [
                    'start' => $from,
                    'end' => $to
                ];
            }
            else {
                $start_week = Carbon::parse($from)->addDay(1)->format('Y-m-d');
                $end_week = Carbon::parse($start_week)->endOfWeek()->subDay(1);
                if ($end_week->month == Carbon::parse($from)->month) {
                    if (strtotime($end_week) > strtotime($to)) {
                        $this->week_range[] = [
                            'start' => $from,
                            'end' => $to
                        ];
                    } else {
                        $this->week_range[] = [
                            'start' => $from,
                            'end' => $end_week->format('Y-m-d')
                        ];
                    }
                    $this->rangeWeek($end_week->addDay(1)->format('Y-m-d'), $to, 1);
                } else {
                    $this->week_range[] = [
                        'start' => $from,
                        'end' => Carbon::parse($from)->endOfMonth()->format('Y-m-d')
                    ];
                    $this->rangeWeek(Carbon::parse($from)->endOfMonth()->addDay(1)->format('Y-m-d'), $to, 1);
                }
            }
        }
        return;
    }
    
    /**
     * Upload image statistical into storage
     * 
     * @param $data_request_image
     * 
     * @return boolean true|false
     */
    public function uploadImageStatistics($data_request_image) {
        if ($data_request_image) {
            // Check forder image chart
            $path = storage_path('app/uploads');
            if (File::isDirectory($path)) {
                // Remove forder old
                deleteDirectory($path);
            }
            try {
                // Create forder image chart
                $file = File::makeDirectory($path, 0777, true, true);
                //image Name
                $data_image_name = $data_request_image['fileName'] ?? [];
                if ($file) {
                    foreach ($data_image_name as $key => $image) {
                        // file image name
                        $file_name = $image;
                        // Data image
                        $data = $data_request_image['dataImage'][$key] ?? NULL;
                        // Upload image
                        if(!Storage::disk('public_uploads')->put($file_name, file_get_contents($data->getPathName()))) {
                            return false;
                        }
                    }
                    return $data_image_name;
                }
                return false;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
}
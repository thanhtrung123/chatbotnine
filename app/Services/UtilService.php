<?php

namespace App\Services;

use GuzzleHttp\Client;
use Route;
use Carbon\Carbon;

/**
 * ユーティリティサービス
 * Class UtilService
 * @package App\Services
 */
class UtilService
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var array
     */
    private $execTimeAry = [];

    /**
     * UtilService constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * 設定値上書
     * @param $src
     * @param $dest
     */
    public function overrideConfig($src, $dest)
    {
        $source = config($src);
        foreach ($source as $key => $val) {
            $dest_key = $dest . '.' . $key;
            if (is_array($val)) {
                $dest_val = config($dest_key);
                if (!empty($dest_val)) {
                    $val = array_merge_recursive($dest_val, $val);
                }
            }
            config([$dest_key => $val]);
        }
    }

    /**
     * CSV関連ルート追加
     * @param $name
     * @param null $controller
     * @param array $only
     */
    public function addCsvRoute($name, $controller = null, $only = [])
    {
        if ($controller === null) {
            $controller = ucfirst(camelize($name));
        }
        if (empty($only) || in_array('import', $only)) {
            Route::get("/{$name}/import", "{$controller}Controller@import")->name("{$name}.import");
            Route::post("/{$name}/import", "{$controller}Controller@importStore")->name("{$name}.import_store");
        }
        if (empty($only) || in_array('export', $only)) {
            Route::get("/{$name}/export", "{$controller}Controller@export")->name("{$name}.export");
        }
    }

    /**
     * 実行時間測定開始
     * @param $name
     */
    public function execTimeStart($name)
    {
        $st = microtime(true);
        if (is_array($name)) {
            foreach ($name as $nm) {
                $this->execTimeAry[$nm] = $st;
            }
        } else {
            $this->execTimeAry[$name] = $st;
        }
    }

    /**
     * 実行時間測定停止
     * @param $name
     */
    public function execTimeStop($name)
    {
        $ed = microtime(true);
        if (is_array($name)) {
            foreach ($name as $nm) {
                $this->execTimeAry[$nm] = $ed - $this->execTimeAry[$nm];
            }
        } else {
            $this->execTimeAry[$name] = $ed - $this->execTimeAry[$name];
        }
    }

    /**
     * 実行時間取得
     * @return array
     */
    public function getExecTime()
    {
        return $this->execTimeAry;
    }

    /**
     * base_weekを基準に今日からweek_num週前までの日付を取得
     * @param $base_week
     * @param $week_num
     * @return array
     */
    public function getWeekAry($base_week, $week_num)
    {
        $days = [];
        $start_date = Carbon::today();
        $start_wn = 0;
        if ($start_date->dayOfWeek != $base_week) {
            //今週の基準日まで回す
            for ($wd = 0; $wd < 7; $wd++) {
                if ($start_date->dayOfWeek == $base_week) break;
                $days[0][] = $start_date->format('Ymd');
                $start_date->subDay(1);
            }
            $start_wn = 1;
        }
        //残りを取得
        for ($wn = $start_wn; $wn < $week_num; $wn++) {
            for ($wd = 0; $wd < 7; $wd++) {
                $days[$wn][] = $start_date->format('Ymd');
                $start_date->subDay(1);
            }
        }
        return $days;
    }

    /**
     * メモリ使用量をログに出力
     * @param string $msg
     */
    public function logMemory($msg = '')
    {
        $size = memory_get_usage(true);
        $unit = array('B', 'KB', 'MB', 'GB');
        $memory = round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
        logger()->debug("Memory Use [{$memory}] : {$msg}");
    }

    /**
     * DBデータ→チョイス用
     * @param $db_data
     * @param $key_col
     * @param $val_col
     * @return array
     */
    public function createChoice($db_data, $key_col, $val_col)
    {
        $choice = [];
        foreach ($db_data as $row) {
            $choice[$row[$key_col]] = $row[$val_col];
        }
        return $choice;
    }

    /**
     * 学習データ→QaAry
     * @param $learning_data
     * @return array
     */
    public function learningDataToQaAry($learning_data)
    {
        $qa = [];
        foreach ($learning_data as $row) {
            $qa[] = [
                'id' => $row['api_id'],
                'answer' => $row['answer'],
                'question_str' => $row['question'],
                'questions' => [$row['question_morph']],
                'score' => 0,
                'metadata' => [],
            ];
        }
        return $qa;
    }

    /**
     * @param $code
     * @param $code_const_path
     * @param $message_path
     * @param null $default
     * @return mixed
     */
    public function getCustomErrorMessage($code, $code_const_path, $message_path, $default = null)
    {
        $errors = \Constant::getConstArray($code_const_path);
        $messages = config($message_path);
        $message = $default ?? $messages['default'];
        if (isset($errors[$code])) {
            $message = $messages[$errors[$code]];
        }
        return $message;
    }


}
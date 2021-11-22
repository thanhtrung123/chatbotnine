<?php

if (!function_exists('datetime')) {
    /**
     * 日付文字取得
     * @return false|string
     */
    function datetime()
    {
        return date(config('app.date_format'));
    }
}

if (!function_exists('confirm')) {
    /**
     * [View用] フォーム確認時の値を取得
     * @param $name
     * @param $data
     * @return mixed
     */
    function confirm($name, $data)
    {
        return old($name, $data[$name] ?? null);
    }
}

if (!function_exists('s2d')) {
    /**
     * スラッシュtoドット
     * @param $str
     * @return mixed
     */
    function s2d($str)
    {
        return str_replace('/', '.', $str);
    }
}

if (!function_exists('d2s')) {
    /**
     * ドットtoスラッシュ
     * @param $str
     * @return mixed
     */
    function d2s($str)
    {
        return str_replace('.', '/', $str);
    }
}

if (!function_exists('a2d')) {
    /**
     * array to dot
     * @param $str
     * @return string|string[]|null
     */
    function a2d($str)
    {
        return str_replace('[]', '', preg_replace_callback('/\[(\d+)\]/', function ($match) {
            return ".{$match[1]}";
        }, $str));
    }
}

if (!function_exists('str_omit')) {
    /**
     * 文字列省略
     * @param $str
     * @param $len
     * @param string $suffix
     * @return string
     */
    function str_omit($str, $len, $suffix = '...')
    {
        $suffix = (mb_strlen($str) > $len) ? $suffix : '';
        return mb_substr($str, 0, $len) . $suffix;
    }
}

if (!function_exists('str_omit_tooltip')) {
    /**
     * 文字列省略表示用タグ
     * @param $str
     * @param $len
     * @param string $suffix
     * @return string
     */
    function str_omit_tooltip($str, $len, $suffix = '...')
    {
        $str = e($str);
        return "<span class=\"omit_message\" title=\"{$str}\">" . str_omit($str, $len, $suffix) . '</span>';
    }
}

if (!function_exists('is_vector')) {
    /**
     * 素の配列（連想配列ではない）かどうか
     * @param $ary
     * @return bool
     */
    function is_vector($ary)
    {
        return array_values($ary) === $ary;
    }
}

if (!function_exists('equal_digit')) {
    /**
     * 指定された桁が等しいか
     * @param $num
     * @param $base
     * @param null $digit
     * @param array $ignore
     * @return bool
     */
    function equal_digit($num, $base, $digit = null, $ignore = [])
    {
        if (in_array($num, $ignore)) {
            return false;
        }
        $num = strrev(strval($num));
        $base = strrev(strval($base));
        $digit = ($digit ?? strlen($base)) - 1;
        if (!isset($num[$digit]) || !isset($base[$digit])) {
            return false;
        }
        return $base[$digit] === $num[$digit];
    }
}

if (!function_exists('number_to_alpha')) {
    /**
     * 数字をアルファベットに（桁ごとAから順に）
     * @param $num
     * @return string
     */
    function number_to_alpha($num)
    {
        $snum = strval($num);
        $result = '';
        for ($i = 0; $i < strlen($snum); $i++) {
            $result .= index_to_symbol($snum[$i]);
        }
        return $result;
    }
}

if (!function_exists('index_to_symbol')) {
    /**
     * @param $idx
     * @return string
     */
    function index_to_symbol($idx)
    {
        return chr(ord('A') + $idx);
    }
}

if (!function_exists('calc_rate')) {
    /**
     * パーセント表示用計算
     * @param $num
     * @param $base_num
     * @param int $precision
     * @param int $mode
     * @return float|int
     */
    function calc_rate($num, $base_num, $precision = 0, $mode = PHP_ROUND_HALF_DOWN)
    {
        if ($num == 0) return 0;
        return round($num / $base_num * 100, $precision, $mode);
    }
}

if (!function_exists('underscore')) {
    /**
     * キャメルケースtoスネークケース
     * @param $str
     * @return string
     */
    function underscore($str)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_\0', $str)), '_');
    }
}

if (!function_exists('camelize')) {
    /**
     * スネークケースtoキャメルケース
     * @param $str
     * @return string
     */
    function camelize($str)
    {
        return lcfirst(strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']));
    }
}

if (!function_exists('mbTrim')) {
    function mbTrim($pString)
    {
        return preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $pString);
    }
}

if (!function_exists('array_flatten')) {
    /**
     * 多次元配列を一次元配列に
     * @param array $arr
     * @return array
     */
    function array_flatten(array $arr)
    {
        $ret = array();
        array_walk_recursive($arr, function ($val) use (&$ret) {
            $ret[] = $val;
        });
        return $ret;
    }
}

if (!function_exists('array_eq')) {
    /**
     * 配列同士が等しいか
     * @param array $ary_a
     * @param array $ary_b
     * @return bool
     */
    function array_eq(array $ary_a, array $ary_b)
    {
        $diff_ab = array_diff($ary_a, $ary_b);
        $diff_ba = array_diff($ary_b, $ary_a);
        return empty($diff_ab) && empty($diff_ba);
    }
}

if (!function_exists('array_duplicate_extract')) {
    /**
     * 配列の重複部を取得
     * @param $ary
     * @param null $key
     * @return array
     */
    function array_duplicate_extract($ary, $key = null)
    {
        if ($key !== null) {
            foreach ($ary as $idx => $row) {
                $ary[$idx] = $row[$key];
            }
        }
        $result = $tmp = [];
        foreach ($ary as $idx => $val) {
            if (isset($tmp[$val])) {
                if (!isset($result[$val])) {
                    $result[$val] = [$tmp[$val]];
                }
                $result[$val][] = $idx;
            }
            $tmp[$val] = $idx;
        }
        return $result;
    }
}

if (!function_exists('is_string_number')) {
    /**
     * 文字列型の数値かどうか
     * @param $val
     * @return bool
     */
    function is_string_number($val)
    {
        return (gettype($val) == 'string' && is_numeric($val));
    }
}

if (!function_exists('output_config_json')) {
    /**
     * 設定ファイルをJsonで出力（主にjs用）
     * @param $route
     * @param array $option
     * @return \Illuminate\Config\Repository|mixed
     */
    function output_config_json($route, $option = [])
    {
        $config = config($route);
        array_walk_recursive($config, function (&$val, $key) use ($option) {
            if (isset($option['allow_key'])) {
                if (!preg_match("/{$option['allow_key']}/", $key)) $val = null;
                return;
            }
            $val = __($val);
        });
        return $config;
    }
}

if (!function_exists('br2nl')) {
    /**
     * @param $string
     * @param bool $with_p
     * @return string|string[]|null
     */
    function br2nl($string, $with_p = false)
    {
        $tag = "br";
        if ($with_p) $tag = "(p|br)";
        return preg_replace("/<[[:space:]]*\/?[[:space:]]*{$tag}[[:space:]]*\/?[[:space:]]*>/i", "\n", $string);
    }
}


if (!function_exists('http_parse_query')) {
    /**
     * parse_strの制約を受けない版
     * @param $queryString
     * @param string $argSeparator
     * @param int $decType
     * @return array
     */
    function http_parse_query($queryString, $argSeparator = '&', $decType = PHP_QUERY_RFC1738)
    {
        $result = array();
        $parts = explode($argSeparator, $queryString);
        foreach ($parts as $part) {
            list($paramName, $paramValue) = explode('=', $part, 2);
            switch ($decType) {
                case PHP_QUERY_RFC3986:
                    $paramName = rawurldecode($paramName);
                    $paramValue = rawurldecode($paramValue);
                    break;
                case PHP_QUERY_RFC1738:
                default:
                    $paramName = urldecode($paramName);
                    $paramValue = urldecode($paramValue);
                    break;
            }
            if (preg_match_all('/\[([^\]]*)\]/m', $paramName, $matches)) {
                $paramName = substr($paramName, 0, strpos($paramName, '['));
                $keys = array_merge(array($paramName), $matches[1]);
            } else {
                $keys = array($paramName);
            }
            $target = &$result;
            foreach ($keys as $index) {
                if ($index === '') {
                    if (isset($target)) {
                        if (is_array($target)) {
                            $intKeys = array_filter(array_keys($target), 'is_int');
                            $index = count($intKeys) ? max($intKeys) + 1 : 0;
                        } else {
                            $target = array($target);
                            $index = 1;
                        }
                    } else {
                        $target = array();
                        $index = 0;
                    }
                } elseif (isset($target[$index]) && !is_array($target[$index])) {
                    $target[$index] = array($target[$index]);
                }
                $target = &$target[$index];
            }
            if (is_array($target)) {
                $target[] = $paramValue;
            } else {
                $target = $paramValue;
            }
        }
        return $result;
    }
}

if (!function_exists('plain_to_associative_array')) {
    /**
     * 配列の配列にキーを付ける（自身の値から）
     * @param $ary
     * @param $key
     * @return array
     */
    function plain_to_associative_array($ary, $key)
    {
        $result = [];
        foreach ($ary as $row) {
            $result[$row[$key]] = $row;
        }
        return $result;
    }
}


if (!function_exists('calc_tf_idf')) {
    /**
     * TF-IDFを計算
     * @param $data
     * @param bool $l2norm
     * @return array
     */
    function calc_tf_idf($data, $l2norm = true)
    {
        //全文書内で、単語tが含まれる文書数を取得
        $calc_dft = function ($t) use ($data) {
            $count = 0;
            foreach ($data as $idx => $row) {
                if (in_array($t, array_column($row, 0)))
                    $count++;
            }
            return $count;
        };
        //全文書数
        $na = count($data);
        //全単語を取得
        $t_ary = [];
        foreach ($data as $idx => $row) {
            $d = array_flip(array_column($row, 0));
            $t_ary += $d;
        }
        //IDF計算
        $idf_ary = [];
        foreach ($t_ary as $t => $dmy) {
            $idf_ary[$t] = log($na / $calc_dft($t), 2);
        }
        //TF-IDF計算
        $tf_idf_ary = [];
        foreach ($data as $idx => $row) {
            foreach ($row as $row_idx => $ary) {
                $tf = $ary[1];
                $idf = $idf_ary[$ary[0]];
                $tf_idf = $tf * $idf;
                if ($tf_idf == 0) continue;
                $tf_idf_ary[$idx][] = [$ary[0], $tf_idf];
            }
            //L2ノルムでノーマライズ
            if (!$l2norm) continue;
            if (!isset($tf_idf_ary[$idx])) {
                $tf_idf_ary[$idx] = [];
                continue;
            }
            $v = array_map(function ($ti_row) {
                return pow($ti_row[1], 2);
            }, $tf_idf_ary[$idx]);
            $l2norm = sqrt(array_sum($v));
            if ($l2norm == 0) {
                $tf_idf_ary[$idx] = [];
            } else {
                foreach ($tf_idf_ary[$idx] as $row_idx => $ary) {
                    $tf_idf_ary[$idx][$row_idx] = [$ary[0], $ary[1] / $l2norm];
                }
            }
        }
        return $tf_idf_ary;
    }
}

if (!function_exists('levenshteinNormalizedUtf8')) {
    /**
     * レーベンシュタイン距離計算（ノーマライズ）
     * @param $s1
     * @param $s2
     * @param int $cost_ins
     * @param int $cost_rep
     * @param int $cost_del
     * @return float|int
     */
    function levenshteinNormalizedUtf8($s1, $s2, $cost_ins = 1, $cost_rep = 1, $cost_del = 1)
    {
        $l1 = mb_strlen($s1, 'UTF-8');
        $l2 = mb_strlen($s2, 'UTF-8');
        $size = max($l1, $l2);
        if (!$size) {
            return 0;
        }
        if (!$s1) {
            return $l2 / $size;
        }
        if (!$s2) {
            return $s1 / $size;
        }
        return 1.0 - levenshteinUtf8($s1, $s2, $cost_ins, $cost_rep, $cost_del) / $size;
    }
}

if (!function_exists('levenshteinUtf8')) {

    /**
     * レーベンシュタイン距離計算
     * @param $s1
     * @param $s2
     * @param int $cost_ins
     * @param int $cost_rep
     * @param int $cost_del
     * @return float|int
     */
    function levenshteinUtf8($s1, $s2, $cost_ins = 1, $cost_rep = 1, $cost_del = 1)
    {
        $s1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
        $s2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);
        $l1 = count($s1);
        $l2 = count($s2);
        if (!$l1) {
            return $l2 * $cost_ins;
        }
        if (!$l2) {
            return $l1 * $cost_del;
        }
        $p1 = array_fill(0, $l2 + 1, 0);
        $p2 = array_fill(0, $l2 + 1, 0);
        for ($i2 = 0; $i2 <= $l2; ++$i2) {
            $p1[$i2] = $i2 * $cost_ins;
        }
        for ($i1 = 0; $i1 < $l1; ++$i1) {
            $p2[0] = $p1[0] + $cost_ins;
            for ($i2 = 0; $i2 < $l2; ++$i2) {
                $c0 = $p1[$i2] + ($s1[$i1] === $s2[$i2] ? 0 : $cost_rep);
                $c1 = $p1[$i2 + 1] + $cost_del;
                if ($c1 < $c0) {
                    $c0 = $c1;
                }
                $c2 = $p2[$i2] + $cost_ins;
                if ($c2 < $c0) {
                    $c0 = $c2;
                }
                $p2[$i2 + 1] = $c0;
            }
            $tmp = $p1;
            $p1 = $p2;
            $p2 = $tmp;
        }
        return $p1[$l2];
    }
}

if (!function_exists('deleteDirectory')) {
    /**
     * Delete directtory by command
     * @param $dir
     * @return float|int
     */
    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
    
        if (!is_dir($dir)) {
            return unlink($dir);
        }
    
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!deleteDirectoryCommand($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
    
        }
        return rmdir($dir);
    }
}

if (!function_exists('deleteDirectoryCommand')) {
    /**
     * Delete directtory by command
     * @param $dir
     * @return float|int
     */
    function deleteDirectoryCommand($dir) {
        system('rm -rf -- ' . escapeshellarg($dir), $retval);
        // UNIX commands return zero on success
        return $retval == 0;
    }
}

if (!function_exists('weekOfMonth')) {
    /**
     * Get weekly of month
     * @param $date
     * @return date
     */
    function weekOfMonth($date) {
        //Get the first day of the month.
        $first_of_month = getWeek(date("Y-m-01", $date));
        $date = getWeek(date("Y-m-d", $date));
        return (intval($date) - intval($first_of_month)) + 1;
    }
}

if (!function_exists('getIsoWeeksInYear')) {
    /**
     * Get weekly of year
     * @param $year
     * @return week format
     */
    function getIsoWeeksInYear($year) {
        $date = new DateTime;
        $date->setISODate($year, 53);
        return ($date->format("W") === "53" ? 53 : 52);
    }
}

if (!function_exists('getWeek')) {
    /**
     * Get week
     * @param $date
     * @return $week
     */
    function getWeek($date) {
        $week = date('W',strtotime($date));
        $day  = date('N',strtotime($date));
        $max_weeks = getIsoWeeksInYear(date('Y',strtotime($date)));
        if ($day == 7 && $week != $max_weeks) {
            return ++$week;
        } else if ($day == 7) {
            return 1;
        } else {
            return $week;
        }
    }
}


if (!function_exists('rangeMonth')) {
    /**
     * Get range month
     * @param $from
     * @param $to
     * @return list month
     */
    function rangeMonth($from, $to) {
        $month = strtotime(date('Y-m-01', strtotime($from)));
        $end = strtotime($to);
        $month_range = [];
        while($month <= $end) {
            $month_range[] = date('Y-m', $month);
            $month = strtotime("+1 month", $month);
        }
        return $month_range;
    }
}

if (!function_exists('rangeYear')) {
    /**
     * Get range year
     * @param $from
     * @param $to
     * @return list year
     */
    function rangeYear($from, $to) {
        $start =  date('Y', strtotime($from));
        $end = date('Y', strtotime($to));
        $year = [];
        $index = $start;
        while($index <= $end) {
            $year[] = (string) $index;
            $index = $index + 1;

        }
        return $year;
    }
}
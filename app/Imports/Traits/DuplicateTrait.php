<?php


namespace App\Imports\Traits;

/**
 * 重複チェックトレイト
 * Trait DuplicateTrait
 * @package App\Imports\Traits
 */
trait DuplicateTrait
{
    /**
     * @var array 重複チェック用配列
     */
    private $check_ary = [];

    /**
     * @var array Duplicate check multiple
     */
    private $check_mulitpe_ary = [];

    /**
     * 重複チェック（配列も対応）
     * @param string $key 重複チェックキー
     * @param array|string $row 重複チェック値
     * @return bool
     */
    public function duplicateCheck($key, $row)
    {
        $val = is_array($row) ? mbTrim($row[$key]) : mbTrim($row);
        if (isset($this->check_ary[$key][$val])) {
            return true;
        } else {
            $this->check_ary[$key][$val] = 1;
        }
        return false;
    }
    
    /**
     * Duplicate check multiple
     * @param array $key_ary array keyword
     * @param array|string $row Duplicate check value
     * @return bool
     */
    public function duplicateMultipeCheck($key_ary, $row)
    {
        $val_ary = array();
        foreach ($key_ary as $key_name) {
            $val = is_array($row) ? mbTrim($row[$key_name]) : mbTrim($row);
            $val_ary[$key_name] = $val;
        }
        $key = array_search($val_ary, $this->check_mulitpe_ary);
        if ($key !== false) {
            return true;
        } else {
            $this->check_mulitpe_ary[] = $val_ary;
        }
        return false;
    }
}
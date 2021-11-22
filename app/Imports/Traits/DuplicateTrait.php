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
     * 重複チェック（配列も対応）
     * @param string $key 重複チェックキー
     * @param array|string $row 重複チェック値
     * @return bool
     */
    public function duplicateCheck($key, $row)
    {
        $val = is_array($row) ? $row[$key] : $row;
        if (isset($this->check_ary[$key][$val])) {
            return true;
        } else {
            $this->check_ary[$key][$val] = 1;
        }
        return false;
    }
}
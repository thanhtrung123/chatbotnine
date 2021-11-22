<?php

namespace App\Services;

/**
 * 定数サービス
 * Class ConstService
 * @package App\Services
 */
class ConstService
{

    /**
     * 定数配列を取得
     * @param string|array $const （定数設定のパスか配列）
     * @param bool $key_only キーのみ出力
     * @param array $option オプション
     * @return array
     */
    public function getConstArray($const, $key_only = false, $option = [])
    {
        if (!is_array($const)) {
            $const = config('const.' . $const);
        }
        return $this->constArrayToOptions($const, $key_only, $option);
    }

    /**
     * オプション用定数配列を取得
     * @param $consts
     * @param bool $key_only
     * @param array $option
     * @return array
     */
    private function constArrayToOptions($consts, $key_only = false, $option = [])
    {
        $row = current($consts);
        if (!isset($row['id'])) {
            return $consts;
        }
        $ret = [];
        $name = $option['name'] ?? 'name';
        if ($key_only) {
            foreach ($consts as $key => $row) {
                if (!$this->refine($key, $row, $option)) continue;
                $ret[] = $row['id'];
            }
        } else {
            foreach ($consts as $key => $row) {
                if (!$this->refine($key, $row, $option)) continue;
                $ret[$row['id']] = $row[$name];
            }
        }
        return $ret;
    }


    /**
     * 絞り込み
     * @param $key
     * @param $row
     * @param $refine
     * @return bool
     */
    private function refine($key, $row, $refine)
    {
        if (empty($refine)) return true;

        if (isset($refine['key'])) {
            if (strpos($key, $refine['key']) !== FALSE) {
                return true;
            }
        }
        if (isset($refine['digit'])) {
            if (equal_digit($row['id'], $refine['digit'])) {
                return true;
            }
        }
        if (isset($refine['ignore_id'])) {
            if (!in_array($row['id'], $refine['ignore_id'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 定数IDから名前を取得
     * @param $id
     * @param $const
     * @return mixed
     */
    public function getConstName($id, $const)
    {
        if (!is_array($const)) {
            $const = config('const.' . $const);
        }
        foreach ($const as $key => $row) {
            if (isset($row['id'])) {
                if ($row['id'] != $id) continue;
            } else {
                if ($key != $id) continue;
            }
            return $row['name'];
        }
    }
}
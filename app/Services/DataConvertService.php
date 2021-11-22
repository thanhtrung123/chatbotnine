<?php

namespace App\Services;

/**
 * データコンバータサービス
 * Class DataConvertService
 * @package App\Services
 */
class DataConvertService
{
    /**
     * @var array 変換データ配列
     */
    private $converts;

    /**
     * コンバータの引数(値,行,カラム名,行のインデックス,情報)
     * @param $column
     * @param callable $converter
     * @return $this
     */
    public function setConvert($column, callable $converter): self
    {
        if (is_array($column)) {
            foreach ($column as $col) {
                $this->converts[$col] = $converter;
            }
        } else {
            $this->converts[$column] = $converter;
        }
        return $this;
    }

    /**
     * 行をコンバート
     * @param $row
     * @param int $idx
     * @param array $info
     * @return mixed
     */
    public function convertRow($row, $idx = 0, $info = [])
    {
        if (empty($this->converts)) return $row;
        foreach ($this->converts as $column => $convert) {
            if (!isset($row[$column])) continue;
            $row[$column . '_orig'] = $row[$column];
            $row[$column] = $convert($row[$column], $row, $column, $idx, $info);
        }
        return $row;
    }

    /**
     * データをコンバート
     * @param $data
     * @param array $info
     * @return mixed
     */
    public function convertData($data, $info = [])
    {
        if (empty($this->converts)) return $data;
        foreach ($data as $idx => $row) {
            $data[$idx] = $this->convertRow((array)$row, $idx, $info);
        }
        return $data;
    }

    /**
     * 一件コンバート
     * @param $column
     * @param $val
     * @return mixed
     */
    public function convertOne($column, $val)
    {
        return $this->converts[$column]($val);
    }

}
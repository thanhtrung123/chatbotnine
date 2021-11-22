<?php

namespace App\Repositories;

use App\Services\DataConvertService;

/**
 * データテーブル結果クラス
 * Class DatatablesResult
 * @package App\Repositories
 */
class DatatablesResult
{
    /**
     * @var array 情報
     */
    private $info;
    /**
     * @var array データ配列
     */
    private $data;
    /**
     * @var DataConvertService
     */
    private $converter;

    /**
     * DatatablesResult constructor.
     * @param $data
     * @param $total
     * @param $draw
     */
    public function __construct($data, $total, $draw)
    {
        $this->data = $data;
        $this->info['total'] = $total;
        $this->info['draw'] = $draw;
        $this->converter = app(DataConvertService::class);
    }

    /**
     * 配列で取得
     * @return array
     */
    public function toArray()
    {
        return [
            'draw' => $this->info['draw'],
            'recordsTotal' => $this->info['total'],
            'recordsFiltered' => $this->info['total'],
            'data' => $this->getData(),
        ];
    }

    /**
     * データ取得
     * @param null $idx
     * @return array|mixed
     */
    public function getData($idx = null)
    {
        $data = $this->converter->convertData($this->data, $this->info);
        return ($idx === null) ? $data : $data[$idx] ?? [];
    }

    /**
     * データコンバータ取得
     * @return DataConvertService
     */
    public function getConverter()
    {
        return $this->converter;
    }

}
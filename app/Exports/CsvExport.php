<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * CSV　エクスポート（汎用）
 * Class CsvExport
 * @package App\Exports
 */
class CsvExport implements FromArray, WithHeadings
{
    use Exportable;

    private $data;
    private $header;

    /**
     * CsvExport constructor.
     * @param array $data
     * @param array $header
     */
    public function __construct($data = [], $header = [])
    {
        $this->header = $header;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->header;
    }

    /**
     * データセット
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * ヘッダーセット
     * @param array $header
     * @return $this
     */
    public function setHeader(array $header)
    {
        $this->header = $header;
        return $this;
    }

}

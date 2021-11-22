<?php

namespace App\Imports\Admin;

use App\Models\Synonym;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

/**
 * 類義語インポート
 * Class SynonymImport
 * @package App\Imports\Admin
 */
class SynonymImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use \Maatwebsite\Excel\Concerns\Importable,
        \Maatwebsite\Excel\Concerns\SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Synonym([
            'keyword' => $row['keyword'],
            'synonym' => $row['synonym'],
        ]);
    }

    /**
     * ルール
     * @return array
     */
    public function rules(): array
    {
        return [
            'keyword' => 'required',
            'synonym' => 'required',
        ];
    }

    /**
     * カスタムバリデート属性
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'keyword' => '類義語文字',
            'synonym' => '置換後文字',
        ];
    }

    /**
     * ヘッダー行
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * チャンクサイズ
     * @return int
     */
    public function chunkSize(): int
    {
        return 50;
    }

    /**
     * 処理サイズ
     * @return int
     */
    public function batchSize(): int
    {
        return 50;
    }
}
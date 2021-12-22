<?php

namespace App\Imports\Admin;

use App\Imports\Traits\DuplicateTrait;
use App\Models\Variant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

/**
 * 異表記インポート
 * Class VariantImport
 * @package App\Imports\Admin
 */
class VariantImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use \Maatwebsite\Excel\Concerns\Importable,
        \Maatwebsite\Excel\Concerns\SkipsFailures,
        DuplicateTrait;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Variant([
            'noun_variant_text' => $row['noun_variant_text'],
            'noun_text' => $row['noun_text'],
        ]);
    }

    /**
     * バリデート
     * @param $validator
     * @param $row
     * @return array
     */
    public function validateRow($validator, $row)
    {
        $error = [];
        if (isset($row['noun_variant_text']) && $this->duplicateCheck('noun_variant_text', $row)) {
            $error['noun_variant_text'][] = $this->customValidationAttributes()['noun_variant_text'] . config('validation.duplicate');
        }
        return $error;
    }
    
    /**
     * ルール
     * @return array
     */
    public function rules(): array
    {
        return [
            'noun_variant_text' => 'required',
            'noun_text' => 'required',
        ];
    }

    /**
     * カスタムバリデート属性
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'noun_variant_text' => '異表記文字',
            'noun_text' => '置換後文字',
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
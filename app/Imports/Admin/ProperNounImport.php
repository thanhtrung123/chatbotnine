<?php

namespace App\Imports\Admin;

use App\Imports\Traits\DuplicateTrait;
use App\Models\ProperNoun;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

/**
 * 固有名詞インポート
 * Class ProperNounImport
 * @package App\Imports\Admin
 */
class ProperNounImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable,
        SkipsFailures,
        DuplicateTrait;

    /**
     * @param array $row
     * @return ProperNoun|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]|null
     */
    public function model(array $row)
    {
        return new ProperNoun([
            'proper_noun_id' => $row['id'],
            'word' => $row['word'],
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
        if (isset($row['id']) && $this->duplicateCheck('id', $row)) {
            $error['id'][] = $this->customValidationAttributes()['id'] . __("が重複しています。");
        }
        if (isset($row['word']) && $this->duplicateCheck('word', $row)) {
            $error['word'][] = $this->customValidationAttributes()['word'] . __("が重複しています。");
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
            'id' => 'required|integer',
            'word' => 'required',
        ];
    }

    /**
     * カスタムバリデート属性
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'id' => '固有名詞ID',
            'word' => '固有名詞',
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
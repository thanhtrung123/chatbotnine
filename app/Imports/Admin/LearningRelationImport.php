<?php

namespace App\Imports\Admin;

use App\Models\LearningRelation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

/**
 * 関連質問インポート
 * Class LearningRelationImport
 * @package App\Imports\Admin
 */
class LearningRelationImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, WithBatchInserts
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
        return new LearningRelation([
            'id' => $row['id'],
            'name' => $row['name'],
            'api_id' => $row['api_id'],
            'relation_api_id' => $row['relation_api_id'],
            'order' => $row['order'],
        ]);
    }

    /**
     * ルール
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string',
            'api_id' => 'required|integer',
            'relation_api_id' => 'required|integer',
            'order' => 'nullable|integer',
        ];
    }

    /**
     * カスタムバリデート属性
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'id' => 'ID',
            'name' => '関連質問名',
            'api_id' => 'API_ID',
            'relation_api_id' => '関連API_ID',
            'order' => '表示順',
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
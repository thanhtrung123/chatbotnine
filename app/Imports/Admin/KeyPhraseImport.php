<?php

namespace App\Imports\Admin;

use App\Imports\Traits\DuplicateTrait;
use App\Models\KeyPhrase;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

/**
 * キーフレーズインポート
 * Class KeyPhraseImport
 * @package App\Imports\Admin
 */
class KeyPhraseImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable,
        SkipsFailures,
        DuplicateTrait;

    /**
     * @param array $row
     * @return KeyPhrase|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]|null
     */
    public function model(array $row)
    {
        return new KeyPhrase([
            'key_phrase_id' => $row['id'],
            'original_word' => $row['original_word'],
            'word' => $row['word'],
            'replace_word' => $row['replace_word'],
            'type' => $row['type'] ?? '0',
            'disabled' => $row['disabled'] ?? '0',
            'priority' => $row['priority'] ?? '0',
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
        if (isset($row['original_word']) && $this->duplicateCheck('original_word', $row)) {
            $error['original_word'][] = $this->customValidationAttributes()['original_word'] . __("が重複しています。");
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
            'original_word' => 'required',
            'priority' => 'nullable|integer',
        ];
    }

    /**
     * カスタムバリデート属性
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'id' => 'キーフレーズID',
            'original_word' => '元キーフレーズ',
            'word' => 'キーフレーズ',
            'replace_word' => '置換キーフレーズ',
            'type' => 'タイプ',
            'disabled' => '無効フラグ',
            'priority' => '優先度',
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
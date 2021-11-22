<?php

namespace App\Exports\Admin;

use App\Models\KeyPhrase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Constant;

/**
 * キーフレーズ　エクスポート
 * Class KeyPhraseExport
 * @package App\Exports\Admin
 */
class KeyPhraseExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return KeyPhrase::query();
    }

    /**
     * マッピング
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->key_phrase_id,
            $row->original_word,
            $row->word,
            $row->replace_word,
            "{$row->type}",
            "{$row->disabled}",
            "{$row->priority}",
        ];
    }

    /**
     * ヘッダー
     * @return array
     */
    public function headings(): array
    {
        return [
            'id',
            'original_word',
            'word',
            'replace_word',
            'type',
            'disabled',
            'priority',
        ];
    }
}
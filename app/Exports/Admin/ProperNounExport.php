<?php

namespace App\Exports\Admin;

use App\Models\ProperNoun;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Constant;

/**
 * 固有名詞　エクスポート
 * Class ProperNounExport
 * @package App\Exports\Admin
 */
class ProperNounExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return ProperNoun::query();
    }

    /**
     * マッピング
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->proper_noun_id,
            $row->word,
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
            'word',
        ];
    }
}
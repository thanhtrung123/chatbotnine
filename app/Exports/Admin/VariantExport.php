<?php

namespace App\Exports\Admin;

use App\Models\Variant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * 異表記　エクスポート
 * Class VariantExport
 * @package App\Exports\Admin
 */
class VariantExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return Variant::query();
    }

    /**
     * マッピング
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->noun_variant_text,
            $row->noun_text,
        ];
    }

    /**
     * ヘッダー
     * @return array
     */
    public function headings(): array
    {
        return [
            'noun_variant_text',
            'noun_text',
        ];
    }
}
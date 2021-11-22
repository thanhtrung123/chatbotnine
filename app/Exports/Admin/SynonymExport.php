<?php

namespace App\Exports\Admin;

use App\Models\Synonym;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * 類義語　エクスポート
 * Class SynonymExport
 * @package App\Exports\Admin
 */
class SynonymExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @return \Illuminate\Database\Eloquent\Builder|Builder
     */
    public function query()
    {
        return Synonym::query();
    }

    /**
     * マッピング
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->keyword,
            $row->synonym,
        ];
    }

    /**
     * ヘッダー
     * @return array
     */
    public function headings(): array
    {
        return [
            'keyword',
            'synonym',
        ];
    }

}
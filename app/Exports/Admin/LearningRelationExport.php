<?php
namespace App\Exports\Admin;

use App\Models\LearningRelation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * 関連質問　エクスポート
 * Class LearningRelationExport
 * @package App\Exports\Admin
 */
class LearningRelationExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return LearningRelation::query();
    }

    /**
     * マッピング
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->api_id,
            $row->relation_api_id,
            $row->order,
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
            'name',
            'api_id',
            'relation_api_id',
            'order',
        ];
    }
}
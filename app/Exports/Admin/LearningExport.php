<?php

namespace App\Exports\Admin;

use App\Models\Learning;
use App\Repositories\Truth\TruthRepositoryInterface;
use App\Services\Bot\Truth\TruthDbService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * 学習データ　エクスポート
 * Class LearningExport
 * @package App\Exports\Admin
 */
class LearningExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    private $truth_db_service;

    /**
     * LearningExport constructor.
     * @param TruthDbService $truth_db_service
     */
    public function __construct(TruthDbService $truth_db_service)
    {
        $this->truth_db_service = $truth_db_service;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return Learning::query();
    }

    /**
     * マッピング
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        //手動設定の場合、キーフレーズを取得する
        $truth_gen = $this->truth_db_service->getRepositoryTruth()->setParams(['api_id' => $row->api_id])->filterByParams()->getDbResult()->getGenerator();
        $key_phrase_ary = [];
        if ($row->auto_key_phrase_disabled) {
            foreach ($truth_gen as $truth_row) {
                $priority = ($truth_row['auto_key_phrase_priority_disabled'] == '1') ?
                    $truth_row['key_phrase_priority'] ?? $truth_row['priority'] : '';
                $key_phrase_ary[] = "[{$truth_row['key_phrase_id']}]:{$priority}";
            }
        }
        return [
            $row->api_id,
            $row->question,
            $row->answer,
            $row->metadata,
            implode(',', $key_phrase_ary),
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
            'question',
            'answer',
            'metadata',
            'key_phrase',
        ];
    }

}
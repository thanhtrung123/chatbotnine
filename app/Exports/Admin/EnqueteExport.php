<?php

namespace App\Exports\Admin;

use App\Models\EnqueteAnswer;
use App\Services\Admin\EnqueteService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * アンケート　エクスポート
 * Class EnqueteExport
 * @package App\Exports\Admin
 */
class EnqueteExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    private $query;
    private $enquete_service;
    private $form_setting;

    /**
     * EnqueteExport constructor.
     * @param EnqueteService $enquete_service
     */
    public function __construct(EnqueteService $enquete_service)
    {
        $this->enquete_service = $enquete_service;
    }

    /**
     * set query
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * マッピング
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $this->enquete_service->getItemToLabel($row, $this->form_setting);
        return [
            $row->id,
            $row->post_id,
            $row->question,
            $row->answer,
            $row->chat_id,
            $row->posted_at,
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
            'post_id',
            'question',
            'answer',
            'chat_id',
            'posted_at',
        ];
    }

    /**
     * set query and form_id
     * @param $query
     * @param $form_id
     * @return $this
     */
    public function setQueryAndFormId($query, $form_id)
    {
        $this->query = $query;
        $this->form_setting = $this->enquete_service->getFormSettings($form_id);
        return $this;
    }
}
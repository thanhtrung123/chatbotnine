<?php

namespace App\Exports\Admin;

use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * 応答ログ　エクスポート
 * Class ResponseInfoExport
 * @package App\Exports\Admin
 */
class ResponseInfoExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    private $query;

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * set query builder
     * @param $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
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
            $row->chat_id,
            $row->talk_id,
            $row->user_ip,
            $row->action_datetime,
            $row->status,
            \Constant::getConstName($row->status, 'bot.status'),
            $row->user_input,
            $row->user_input_morph,
            $row->api_id,
            $row->api_answer,
            $row->api_score,
            $row->api_question,
            $row->question,
            $row->metadata,
            $row->is_hear_back,
            \Constant::getConstName($row->is_hear_back, 'common.yes_no'),
            $row->is_select,
            \Constant::getConstName($row->is_select, 'common.yes_no'),

        ];
    }

    /**
     * ヘッダー
     * @return array
     */
    public function headings(): array
    {
        return [
            "ログID",
            "チャットID",
            "トークID",
            "ユーザーIP",
            "処理日時",
            "ステータス",
            "ステータスの名称",
            "ユーザ入力文章",
            "ユーザ入力文章（解析後）",
            "API ID",
            "API 回答",
            "API スコア",
            "API 質問",
            "API 質問",
            "タグ",
            "聞き返しの有無",
            "聞き返しの有無の名称",
            "選択",
            "選択の名称",
        ];
    }

}

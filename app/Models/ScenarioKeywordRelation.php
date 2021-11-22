<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * シナリオキーワード紐づけモデル
 * Class ScenarioRelation
 * @package App\Models
 */
class ScenarioKeywordRelation extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_scenario_keyword_relation';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['scenario_id', 'scenario_keyword_id', 'group_no', 'order'];
}
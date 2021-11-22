<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * シナリオ紐づけモデル
 * Class ScenarioRelation
 * @package App\Models
 */
class ScenarioRelation extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_scenario_relation';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['scenario_id', 'parent_scenario_id'];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * シナリオ学習データ紐づけモデル
 * Class ScenarioLearningRelation
 * @package App\Models
 */
class ScenarioLearningRelation extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_scenario_learning_relation';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['scenario_id', 'api_id', 'node_id', 'order'];
}
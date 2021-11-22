<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * シナリオキーワードモデル
 * Class ScenarioRelation
 * @package App\Models
 */
class ScenarioKeyword extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_scenario_keyword';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['keyword'];
}
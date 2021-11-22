<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * シナリオモデル
 * Class Scenario
 * @package App\Models
 */
class Scenario extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_scenario';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['id', 'name', 'category_id', 'order'];
}
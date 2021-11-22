<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 学習データ紐づけモデル
 * Class LearningRelation
 * @package App\Models
 */
class LearningRelation extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_learning_relation';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['api_id', 'relation_api_id', 'name', 'order'];
}
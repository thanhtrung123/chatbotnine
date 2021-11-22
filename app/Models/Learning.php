<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 学習データモデル
 * Class Learning
 * @package App\Models
 */
class Learning extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_learning';
    /**
     * @var array 登録許可
     */
    protected $fillable = [
        'question', 'question_morph', 'answer', 'api_id', 'metadata', 'auto_key_phrase_disabled', 'category_id', 'update_at', 'synced_at'
    ];
}

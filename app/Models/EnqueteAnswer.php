<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * アンケートモデル
 * Class EnqueteAnswer
 * @package App\Models
 */
class EnqueteAnswer extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_enquete_answer';
    /**
     * @var array 登録許可
     */
    protected $fillable = [
        'form_id', 'post_id', 'question_code', 'answer', 'chat_id', 'posted_at'
    ];
}
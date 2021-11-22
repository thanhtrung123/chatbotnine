<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ストップワードモデル
 * Class StopWords
 * @package App\Models
 */
class StopWords extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_stop_words';
    /**
     * @var array 登録拒否
     */
    protected $guarded = ['id'];
}
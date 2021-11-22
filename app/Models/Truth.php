<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 真理表モデル
 * Class Truth
 * @package App\Models
 */
class Truth extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_truth';
    /**
     * @var array 登録拒否
     */
    protected $guarded = ['id'];
}
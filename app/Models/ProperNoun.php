<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 固有名詞モデル
 * Class ProperNoun
 * @package App\Models
 */
class ProperNoun extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_proper_noun';
    /**
     * @var array 登録拒否
     */
    protected $guarded = ['id'];
    /**
     * @var array 登録許可
     */
    protected $fillable = [
        'proper_noun_id', 'word',
    ];
}
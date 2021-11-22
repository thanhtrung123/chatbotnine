<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 異表記モデル
 * Class Variant
 * @package App\Models
 */
class Variant extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_variant';
    /**
     * @var array 登録許可
     */
    protected $fillable = [
        'noun_variant_text', 'noun_text'
    ];
}

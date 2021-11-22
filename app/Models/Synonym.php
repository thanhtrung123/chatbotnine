<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 類義語モデル
 * Class Synonym
 * @package App\Models
 */
class Synonym extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_synonym';
    /**
     * @var array 登録許可
     */
    protected $fillable = [
        'keyword', 'synonym'
    ];
}
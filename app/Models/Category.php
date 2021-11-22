<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * カテゴリモデル
 * Class Category
 * @package App\Models
 */
class Category extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_category';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['name'];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 応答情報モデル
 * Class ResponseInfo
 * @package App\Models
 */
class ResponseInfo extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_response_info';
    /**
     * @var array 登録拒否
     */
    protected $guarded = ['id'];
}
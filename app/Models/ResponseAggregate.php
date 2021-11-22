<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 応答情報集計モデル
 * Class ResponseAggregate
 * @package App\Models
 */
class ResponseAggregate extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_response_aggregate';
    /**
     * @var array 登録拒否
     */
    protected $guarded = ['id'];
}

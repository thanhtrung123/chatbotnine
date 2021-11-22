<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 応答情報（真理表）モデル
 * Class ResponseInfoTruth
 * @package App\Models
 */
class ResponseInfoTruth extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_response_info_truth';
    /**
     * @var array 登録拒否
     */
    protected $guarded = ['id'];
}
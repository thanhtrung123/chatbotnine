<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 画像情報モデル
 * Class ImageInformation
 * @package App\Models
 */
class ImageInformation extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_image_information';
    /**
     * @var array 登録許可
     */
    protected $fillable = [
        'file_name', 'file_path', 'post_at', 'update_at'
    ];

}

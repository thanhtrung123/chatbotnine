<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 応答情報（利用者）モデル
 * Class ResponseInfoUser
 * @package App\Models
 */
class ResponseInfoUser extends Model
{
    /**
     * updated_at カラム名
     */
    const UPDATED_AT = null;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_response_info_user';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['chat_id', 'referrer', 'useragent', 'os_id', 'os_version', 'browser_id', 'browser_version', 'remote_ip', 'status', 'created_at'];
}
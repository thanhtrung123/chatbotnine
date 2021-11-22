<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SnsUidMap
 * @package App\Models
 */
class SnsUidMap extends Model
{
    protected $primaryKey = 'sns_uid';
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_sns_uid_map';
    /**
     * @var array 登録許可
     */
    protected $fillable = ['sns_uid', 'chat_id', 'enquete_key', 'channel'];
}
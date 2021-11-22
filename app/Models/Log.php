<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ログモデル
 * Class Log
 * @package App\Models
 */
class Log extends Model
{
    /**
     * created_at カラム名
     */
    const CREATED_AT = 'action_datetime';
    /**
     * updated_at カラム名
     */
    const UPDATED_AT = null;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_log';
}
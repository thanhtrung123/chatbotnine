<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * キーフレーズモデル
 * Class KeyPhrase
 * @package App\Models
 */
class KeyPhrase extends Model
{
    /**
     * @var bool タイムスタンプ
     */
    public $timestamps = false;
    /**
     * @var string テーブル名
     */
    protected $table = 'tbl_key_phrase';
    /**
     * @var array 登録拒否
     */
    protected $guarded = ['id'];
    /**
     * @var array 登録許可
     */
    protected $fillable = [
        'key_phrase_id', 'word', 'replace_word', 'disabled', 'type', 'original_word', 'priority',
    ];
}
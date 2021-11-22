<?php

namespace App\Services\Bot;

use Illuminate\Contracts\Logging\Log;
use Util;

/**
 * チャットボット用 形態素解析サービス
 * Class BotMorphService
 * @package App\Services\Bot
 */
class BotMorphService
{
    //プロパティ
    /**
     * @var MorphBaseService
     */
    private $morph_base_service;
    /**
     * @var string 文章
     */
    private $message;

    /**
     * BotMorphService constructor.
     * @param MorphBaseService $morph_base_service
     */
    public function __construct(
        MorphBaseService $morph_base_service
    )
    {
        $this->morph_base_service = $morph_base_service;
    }

    /**
     * API用キーワード抽出
     * @param bool $replace_message 辞書を使用して置換
     * @return $this
     */
    public function execMorph($replace_message = true)
    {
        if (!isset($this->message)) {
            //必須パラメータがなかったら終了
            throw new \InvalidArgumentException('message is required.');
        }
        $this->morph_base_service->setMessage($this->message);
        if ($replace_message) {
            $this->morph_base_service->replaceMessageUseDictionary(false, false);
        }
        $this->message = $this->morph_base_service->makeApiMorphKeyword();
        return $this;
    }

    /*
     * getter setter
     */

    /**
     * メッセージ取得
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * メッセージセット
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * チャットボット用形態素解析基本サービス取得
     * @return MorphBaseService
     */
    public function getMorphBaseService(): MorphBaseService
    {
        return $this->morph_base_service;
    }

}
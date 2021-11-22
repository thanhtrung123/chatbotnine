<?php

namespace App\Services\Bot\Truth;

use App\Services\Bot\MorphBaseService;

/**
 * 真理表用形態素解析サービス
 * Class TruthService
 * @package App\Services\Truth
 */
class TruthMorphService
{
    private $morph_base_service;
    private $truth_db_service;
    private $message;
    private $words = [];
    private $word_counts = [];

    /**
     * TruthMorphService constructor.
     * @param TruthDbService $truth_db_service
     * @param MorphBaseService $morph_base_service
     */
    public function __construct(TruthDbService $truth_db_service, MorphBaseService $morph_base_service)
    {
        $this->morph_base_service = $morph_base_service;
        $this->truth_db_service = $truth_db_service;
    }

    /**
     * キーフレーズ抽出実行
     * @param bool $use_stop_word ストップワードを除く
     * @param bool $replace_message 辞書を使用して置換
     * @return $this
     */
    public function execMorph($use_stop_word = true, $replace_message = true)
    {
        if (!isset($this->message)) {
            //必須パラメータがなかったら終了
            throw new \InvalidArgumentException('message is required.');
        }
        $this->morph_base_service->setMessage($this->message);
        if ($replace_message) {
            $this->morph_base_service->replaceMessageUseDictionary(true, true);
        }
        $word_counts = $this->morph_base_service->makeKeyPhrase();
        if ($use_stop_word) {
            foreach ($word_counts as $word => $count) {
                if (!$this->truth_db_service->isStopWord($word)) continue;
                unset($word_counts[$word]);
            }
        }
        $this->word_counts = $word_counts;
        $key_word_ary = array_keys($word_counts);
        $this->message = implode(' ', $key_word_ary);
        $this->words = $key_word_ary;
        return $this;
    }

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
     * キーフレーズ取得
     * @return array
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * キーフレーズ（出現回数）取得
     * @return array
     */
    public function getWordCounts(): array
    {
        return $this->word_counts;
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
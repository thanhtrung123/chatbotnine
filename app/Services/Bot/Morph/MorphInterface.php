<?php

namespace App\Services\Bot\Morph;

/**
 * 形態素解析サービス　インターフェース
 * Interface MorphInterface
 * @package App\Services\Bot\Morph
 */
interface MorphInterface
{

    /**
     * 設定をセット
     * @param array $setting
     * @return $this
     */
    public function setSetting(array $setting = []): self;

    /**
     * パラメータをセット
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self;

    /**
     * 解析対象メッセージをセット
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self;

    /**
     * 言語をセット
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language): self;

    /**
     * 形態素解析実行
     * @param array $option
     * @return $this
     */
    public function execute(array $option = []): self;

    /**
     * 結果を取得
     * @param array $option
     * @return MorphResult[]
     */
    public function getResult(array $option = []): array;
}
<?php

namespace App\Services\Bot\Morph;

/**
 * 形態素解析サービス抽象クラス
 * Class MorphAbstract
 * @package App\Services\Bot\Morph
 */
abstract class MorphAbstract implements MorphInterface
{
    /**
     * @var array 設定
     */
    protected $setting;
    /**
     * @var array パラメータ
     */
    protected $params;
    /**
     * @var array 結果配列
     */
    protected $result;
    /**
     * @var string 言語
     */
    protected $language;
    /**
     * @var string 文章
     */
    protected $message;
    /**
     * @var string
     */
    protected $empty_string = '*';

    /**
     * 空を表す文字列を置き換え
     * @param string $str
     * @param null|string $replace
     * @return string|null
     */
    public function replaceEmptyString($str, $replace = null)
    {
        return ($str === $this->empty_string) ? $replace : $str;
    }

    /**
     * 設定をセット
     * @param array $setting
     * @return $this
     */
    public function setSetting(array $setting = []): MorphInterface
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * パラメータをセット
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): MorphInterface
    {
        $this->params = $params;
        return $this;
    }

    /**
     * 言語をセット
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language): MorphInterface
    {
        $this->language = $language;
        return $this;
    }

    /**
     * 解析対象メッセージをセット
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): MorphInterface
    {
        $this->message = $message;
        return $this;
    }

    /**
     * 結果を取得
     * @param array $option
     * @return MorphResult[]
     */
    public function getResult(array $option = []): array
    {
        return $this->result;
    }
}
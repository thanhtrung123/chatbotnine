<?php

namespace App\Services\Bot\Morph;

/**
 * 形態素解析結果クラス
 * Class MorphResult
 * @package App\Services\Bot\Morph
 */
class MorphResult
{
    /**
     * @var string 入力値
     */
    private $input_str;
    /**
     * @var string 表層形
     */
    private $surface_form;
    /**
     * @var string 品詞
     */
    private $pos;
    /**
     * @var array 品詞細分類
     */
    private $pos_selection = [];
    /**
     * @var string 活用型
     */
    private $inflection;
    /**
     * @var string 活用形
     */
    private $conjugated_form;
    /**
     * @var string 原形
     */
    private $original_form;
    /**
     * @var string 読み
     */
    private $reading;
    /**
     * @var string 発音
     */
    private $pronunciation;

    /**
     * 品詞細分類 inArray
     * @param $needle
     * @return bool
     */
    public function inPosSelection($needle)
    {
        return in_array($needle, $this->pos_selection);
    }

    /**
     * 入力値 取得
     * @return string
     */
    public function getInputStr()
    {
        return $this->input_str;
    }

    /**
     * 表層形 取得
     * @return string
     */
    public function getSurfaceForm()
    {
        return $this->surface_form;
    }

    /**
     * 品詞 取得
     * @return string
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * 品詞細分類 取得
     * @param null|integer $idx
     * @return array|string
     */
    public function getPosSelection($idx = null)
    {
        if ($idx === null)
            return $this->pos_selection;
        else
            return $this->pos_selection[$idx];
    }

    /**
     * 活用型 取得
     * @return string
     */
    public function getInflection()
    {
        return $this->inflection;
    }

    /**
     * 活用形 取得
     * @return string
     */
    public function getConjugatedForm()
    {
        return $this->conjugated_form;
    }

    /**
     * 原型 取得
     * @return string
     */
    public function getOriginalForm()
    {
        return $this->original_form;
    }

    /**
     * 読み 取得
     * @return string
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * 発音 取得
     * @return string
     */
    public function getPronunciation()
    {
        return $this->pronunciation;
    }

    /**
     * 入力値 セット
     * @param $input_str
     * @return $this
     */
    public function setInputStr($input_str)
    {
        $this->input_str = $input_str;
        return $this;
    }

    /**
     * 表層形 セット
     * @param string $surface_form
     * @return $this
     */
    public function setSurfaceForm($surface_form)
    {
        $this->surface_form = $surface_form;
        return $this;
    }

    /**
     * 品詞 セット
     * @param string $pos
     * @return $this
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
        return $this;
    }

    /**
     * 品詞細分類 セット
     * @param array $pos_selection
     * @return $this
     */
    public function setPosSelection(array $pos_selection = [])
    {
        $this->pos_selection = $pos_selection;
        return $this;
    }

    /**
     * 品詞細分類 追加
     * @param string $pos_selection
     * @return $this
     */
    public function addPosSelection($pos_selection)
    {
        if ($pos_selection === null)
            return $this;
        $this->pos_selection[] = $pos_selection;
        return $this;
    }

    /**
     * 活用型 セット
     * @param string $inflection
     * @return $this
     */
    public function setInflection($inflection)
    {
        $this->inflection = $inflection;
        return $this;
    }

    /**
     * 活用形 セット
     * @param string $conjugated_form
     * @return $this
     */
    public function setConjugatedForm($conjugated_form)
    {
        $this->conjugated_form = $conjugated_form;
        return $this;
    }

    /**
     * 原型 セット
     * @param string $original_form
     * @return $this
     */
    public function setOriginalForm($original_form)
    {
        $this->original_form = $original_form;
        return $this;
    }

    /**
     * 読み セット
     * @param string $reading
     * @return $this
     */
    public function setReading($reading)
    {
        $this->reading = $reading;
        return $this;
    }

    /**
     * 発音 セット
     * @param string $pronunciation
     * @return $this
     */
    public function setPronunciation($pronunciation)
    {
        $this->pronunciation = $pronunciation;
        return $this;
    }
}
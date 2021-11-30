<?php

namespace App\Services\Bot\Morph;

/**
 * 形態素解析サービス[MeCab]
 * Class MecabService
 * @package App\Services\Bot\Morph
 */
class MecabService extends MorphAbstract
{
    /**
     * @var \MeCab\Tagger  MeCabクラス
     */
    private $mecab;

    /**
     * MecabService constructor.
     * @param array $setting
     */
    public function __construct($setting = [])
    {
        $this->setSetting($setting);
    }

    /**
     * 設定
     * @param array $setting
     * @return MorphInterface
     */
    public function setSetting(array $setting = array()): MorphInterface
    {
        $mecab_setting = [];
        if (isset($setting['main_dic'])) array_push($mecab_setting, '-d', $setting['main_dic']);
        if (isset($setting['user_dic'])) array_push($mecab_setting, '-u', $setting['user_dic']);
        $this->mecab = new \MeCab\Tagger($mecab_setting);
        return $this;
    }

    /**
     * 形態素解析実行
     * @param array $option
     * @return $this
     */
    public function execute(array $option = []): MorphInterface
    {
        $morph_ary = [];
        // スペース区切りを許可している場合は変換
        if (config('bot.morph.enable_space_span')) {
            $this->message = preg_replace('/\s/', '　', $this->message);
        }
        // 形態素を解析する
        $nodes = $this->mecab->parseToNode($this->message);
        foreach ($nodes as $node) {
            if ($node->getSurface() == '') {
                continue;
            }
            $feature_ary = explode(',', $node->getFeature());
            $morph_ary[] = array_merge(array($node->getSurface()), $feature_ary);
        }
        $this->result = $morph_ary;
        return $this;
    }

    /**
     * 解析結果
     * @param array $option
     * @return MorphResult[]
     */
    public function getResult(array $option = []): array
    {
        //形を整える(MorphResultの配列にする)
        foreach ($this->result as &$row) {
            $morph_result = new MorphResult();
            $morph_result->setInputStr(implode(' ', $row))
                ->setSurfaceForm($this->replaceEmptyString($row[0]))
                ->setPos($this->replaceEmptyString($row[1]))
                ->addPosSelection($this->replaceEmptyString($row[2]))
                ->addPosSelection($this->replaceEmptyString($row[3]))
                ->addPosSelection($this->replaceEmptyString($row[4]))
                ->setConjugatedForm($this->replaceEmptyString($row[5]))
                ->setInflection($this->replaceEmptyString($row[6]))
                ->setOriginalForm($this->replaceEmptyString($row[7]))
                ->setReading($this->replaceEmptyString($row[8] ?? null))
                ->setPronunciation($this->replaceEmptyString($row[9] ?? null));
            $row = $morph_result;
        }
        return $this->result;
    }
}
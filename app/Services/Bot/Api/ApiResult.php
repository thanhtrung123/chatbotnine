<?php

namespace App\Services\Bot\Api;

/**
 * チャットボットAPIサービス問い合わせ結果クラス
 * Class ApiResult
 * @package App\Services\Bot\Api
 */
class ApiResult
{
    /**
     * @var integer API_ID
     */
    private $id = null;
    /**
     * @var array 質問配列
     */
    private $questions = [];
    /**
     * @var string 回答（or API_ID）
     */
    private $answer = null;
    /**
     * @var float スコア
     */
    private $score = null;
    /**
     * @var string 質問文（表示用）
     */
    private $question_str = null;
    /**
     * @var string シンボル
     */
    private $selection_symbol = null;
    /**
     * @var array メタデータ
     */
    private $metadata = [];

    /**
     * ApiResult constructor.
     * @param array $ary
     */
    public function __construct($ary = [])
    {
        $this->setId($ary['id'] ?? null);
        $this->setScore($ary['score'] ?? null);
        $this->setQuestions($ary['questions'] ?? []);
        if (!empty($ary['question'])) $this->setQuestion($ary['question']);
        $this->setAnswer($ary['answer'] ?? null);
        $this->setQuestionStr($ary['question_str'] ?? null);
        $this->setSelectionSymbol($ary['selection_symbol'] ?? null);
        $this->setMetadata($ary['metadata'] ?? []);
    }

    /**
     * 配列に変換
     * @return array
     */
    public function toArray()
    {
        $ary = [];
        if ($this->getId() !== null) $ary['id'] = $this->getId();
        if ($this->getScore() !== null) $ary['score'] = $this->getScore();
        if (!empty($this->getQuestions())) {
            $ary['questions'] = $this->getQuestions();
            $ary['question'] = $this->getQuestion();
        }
        if ($this->getAnswer() !== null) $ary['answer'] = $this->getAnswer();
        if ($this->getQuestionStr() !== null) $ary['question_str'] = $this->getQuestionStr();
        if ($this->getSelectionSymbol() !== null) $ary['selection_symbol'] = $this->getSelectionSymbol();
        if (!empty($this->getMetadata())) {
            $ary['metadata'] = array_column($this->getMetadata(), 'value', 'name');
        }
        return $ary;
    }

    /**
     * ID取得
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * ID設定
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * 質問取得(配列)
     * @param null|integer $idx インデックス
     * @return array|string
     */
    public function getQuestions($idx = null)
    {
        if ($idx === null) return $this->questions;
        else return $this->questions[$idx];
    }

    /**
     * 質問取得(1件目)
     * @return string
     */
    public function getQuestion()
    {
        return $this->questions[0];
    }

    /**
     * 質問設定(1件目)
     * @param string $question
     * @return $this
     */
    public function setQuestion($question)
    {
        $this->questions[0] = $question;
        return $this;
    }

    /**
     * 質問設定(配列)
     * @param array $question
     * @return $this
     */
    public function setQuestions(array $question)
    {
        $this->questions = $question;
        return $this;
    }

    /**
     * 回答取得
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * 回答設定
     * @param string $answer
     * @return $this
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * スコア取得
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * スコア設定
     * @param float $score
     * @return $this
     */
    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    /**
     * 質問文取得
     * @return string
     */
    public function getQuestionStr()
    {
        return $this->question_str;
    }

    /**
     * シンボル取得
     * @return string
     */
    public function getSelectionSymbol()
    {
        return $this->selection_symbol;
    }

    /**
     * 質問文設定
     * @param string $question_str
     * @return $this
     */
    public function setQuestionStr($question_str)
    {
        $this->question_str = $question_str;
        return $this;
    }

    /**
     * シンボル設定
     * @param string $selection_symbol
     * @return $this
     */
    public function setSelectionSymbol($selection_symbol)
    {
        $this->selection_symbol = $selection_symbol;
        return $this;
    }

    /**
     * メタデータ取得
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * メタデータ設定
     * @param array $metadata メタデータ
     * @param null|string $key キー
     * @return $this
     */
    public function setMetadata($metadata, $key = null)
    {
        if ($key === null) {
            if (is_vector($metadata)) {
                $this->metadata = $metadata;
            } else {
                foreach ($metadata as $key => $val) {
                    $this->metadata[] = [
                        'name' => $key,
                        'value' => $val,
                    ];
                }
            }
        } else {
            $this->metadata[$key] = $metadata;
        }
        return $this;
    }
}
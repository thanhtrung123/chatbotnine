<?php

namespace App\Services\Bot;

use App\Services\Bot\Api\ApiResult;

/**
 * チャットボット用回答サービス
 * Class BotAnswerService
 * @package App\Services\Bot
 */
class BotAnswerService
{
    /**
     * @var BotDbService
     */
    private $db_service;
    /**
     * @var array 結果配列
     */
    private $result_data;

    /**
     * BotAnswerService constructor.
     * @param BotDbService $db_service
     */
    public function __construct(
        BotDbService $db_service
    )
    {
        $this->db_service = $db_service;
    }


    /**
     * 実行：回答
     * @param ApiResult[] $api_result_ary
     * @return bool
     */
    public function execAnswer($api_result_ary)
    {
        $res_qa_ary = [];
        //スコアによって回答を分ける
        //MEMO:動的に呼ぶとphpmdに呼ばれてないメソッドがあると怒られるので直接書いてみる
        if ($api_result_ary[0]->getScore() >= 80) {
            $this->createAnswer1($api_result_ary, $res_qa_ary);
        } elseif ($api_result_ary[0]->getScore() >= 60) {
            $this->createAnswer2($api_result_ary, $res_qa_ary);
        } elseif ($api_result_ary[0]->getScore() >= 40) {
            $this->createAnswer3($api_result_ary, $res_qa_ary);
        } else {
            return false;
        }

        //セッション用配列に変換
        $sess_qa_ary = [];
        foreach ($res_qa_ary as $qa_ary) {
            foreach ($qa_ary as $api_result) {
                $sess_qa_ary[] = $api_result->toArray();
            }
        }

        //レスポンス
        $this->result_data['num'] = count($sess_qa_ary);
        $this->result_data['qa'] = $sess_qa_ary;
        return true;
    }

    /**
     * 80点以上回答
     * @param ApiResult[] $api_result_ary
     * @param array $res_qa_ary
     */
    private function createAnswer1($api_result_ary, &$res_qa_ary)
    {
        // 最も高スコアなQAが80点以上の場合、一意の回答とする
        if ($api_result_ary[0]->getScore() != $api_result_ary[1]->getScore()) {
            //次スコアが同点ではない場合
            //聞き返しFALSE
            $this->result_data['hear_back_flg'] = false;
            $this->addQa($res_qa_ary, $api_result_ary[0]);
        } else {
            //次スコアが同点の場合
            //聞き返しTRUE
            $this->result_data['hear_back_flg'] = true;
            $this->addQaAryAll($res_qa_ary, $api_result_ary, function ($api_result_ary, $api_result_idx) {
                // スコアが異なる場合、止める
                return ($api_result_ary[$api_result_idx]->getScore() != $api_result_ary[0]->getScore());
            });
        }
    }

    /**
     * 60～80点回答
     * @param ApiResult[] $api_result_ary
     * @param array $res_qa_ary
     */
    private function createAnswer2($api_result_ary, &$res_qa_ary)
    {
        // 最も高スコアなQAが80点未満、60点以上の場合以下を処理
        //聞き返しTRUE
        $this->result_data['hear_back_flg'] = true;
        if ($api_result_ary[0]->getScore() != $api_result_ary[1]->getScore()) {
            //次スコアが同点ではない場合
            if (count($api_result_ary[0]->getQuestions()) == 1) {
                // 質問データが1件の場合
                $this->addQa($res_qa_ary, $api_result_ary[0]);
            } else {
                // 質問データが複数件の場合
                $this->addQaAry($res_qa_ary, $api_result_ary[0]);
            }
        } else {
            // 次スコアが同点の場合
            $this->addQaAryAll($res_qa_ary, $api_result_ary, function ($api_result_ary, $api_result_idx) {
                // スコアが異なる場合、止める
                return ($api_result_ary[$api_result_idx]->getScore() != $api_result_ary[0]->getScore());
            });
        }
    }

    /**
     * 40～60点回答
     * @param ApiResult[] $api_result_ary
     * @param array $res_qa_ary
     */
    private function createAnswer3($api_result_ary, &$res_qa_ary)
    {
        // 最も高スコアなQAが60点未満、40点以上の場合以下を処理
        //聞き返しTRUE
        $this->result_data['hear_back_flg'] = true;
        if ($api_result_ary[1]->getScore() < 40) {
            //次スコアが40未満の場合
            if (count($api_result_ary[0]->getQuestions()) == 1) {
                // 質問データが1件の場合
                $this->addQa($res_qa_ary, $api_result_ary[0]);
            } else {
                // 質問データが複数件の場合
                $this->addQaAry($res_qa_ary, $api_result_ary[0]);
            }
        } else {
            // 次スコアが40点以上である場合
            $this->addQaAryAll($res_qa_ary, $api_result_ary, function ($api_result_ary, $api_result_idx) {
                //スコアが４０未満の場合、止める
                return ($api_result_ary[$api_result_idx]->getScore() < 40);
            });
        }
    }

    /**
     * 返信用QAデータ作成(回答分)
     * @param array $res_qa_ary
     * @param array $api_result_ary
     * @param callable $break_callback
     */
    private function addQaAryAll(array &$res_qa_ary, array $api_result_ary, callable $break_callback = null)
    {
        //API結果ループ
        foreach ($api_result_ary as $api_result_idx => $api_result) {
            //スコアが異なる場合、抜ける
            if ($break_callback !== null && $break_callback($api_result_ary, $api_result_idx)) {
                break;
            }
            //返信用QAデータ作成(質問分)
            $this->addQaAry($res_qa_ary, $api_result, $api_result_idx);
        }
    }

    /**
     * 返信用QAデータ作成(質問分)
     * @param array $res_qa_ary
     * @param ApiResult $api_result
     * @param int $api_result_idx
     */
    private function addQaAry(array &$res_qa_ary, ApiResult $api_result, $api_result_idx = 0)
    {
        //質問データ分ループ
        foreach (array_keys($api_result->getQuestions()) as $question_idx) {
            //返信用QAデータ作成
            $this->addQa($res_qa_ary, $api_result, $api_result_idx, $question_idx, true);
        }
    }

    /**
     * 返信用QAデータ作成
     * @param array $res_qa_ary
     * @param ApiResult $api_result
     * @param int $api_result_idx
     * @param int $question_idx
     * @param bool $use_symbol
     */
    private function addQa(array &$res_qa_ary, ApiResult $api_result, $api_result_idx = 0, $question_idx = 0, $use_symbol = false)
    {
        $before_ma_question_str = $api_result->getQuestions($question_idx);
        if (config('bot.api.default.morph_enabled')) {
            //学習データが形態素解析されている場合
            $learning_data = $this->db_service->findLearningData($api_result->getId());
            $before_ma_question_str = $learning_data ? $learning_data['question'] : $before_ma_question_str;
        }
        //返信用QAデータ
        $res_qa = new ApiResult($api_result->toArray());
        $res_qa->setQuestion($api_result->getQuestion())
            ->setQuestionStr($before_ma_question_str);
        if ($use_symbol) {
            $alpha_idx = count($res_qa_ary, COUNT_RECURSIVE) - count($res_qa_ary);
            $res_qa->setSelectionSymbol(index_to_symbol($alpha_idx));
        }

        $res_qa_ary[$api_result_idx][$question_idx] = $res_qa;
    }

    /**
     * 結果取得
     * @return array 結果配列
     */
    public function getResultData()
    {
        return $this->result_data;
    }
}
<?php

namespace App\Services\Bot;

use App\Services\Bot\Api\ApiResult;
use App\Services\Bot\Truth\TruthDbService;
use App\Services\Bot\Truth\TruthLogService;
use App\Services\Bot\Truth\TruthMatchService;
use App\Services\Bot\Truth\TruthMorphService;

/**
 * チャットボット用 真理表サービス
 * Class BotTruthService
 * @package App\Services\Bot
 */
class BotTruthService
{
    /** @var BotBaseService */
    private $bot_base_service;
    /**
     * @var TruthLogService
     */
    private $truth_log_service;
    /**
     * @var BotApiService
     */
    private $bot_api_service;
    /**
     * @var BotLogService
     */
    private $bot_log_service;
    /**
     * @var BotDbService
     */
    private $bot_db_service;
    /**
     * @var BotSessionService
     */
    private $bot_session_service;
    /**
     * @var BotMorphService
     */
    private $bot_morph_service;
    /**
     * @var TruthDbService
     */
    private $db_service;
    /**
     * @var TruthMatchService
     */
    private $match_service;
    /**
     * @var TruthMorphService
     */
    private $morph_service;

    /**
     * @var string ステータス
     */
    private $status;
    /**
     * @var string 文章
     */
    private $message;
    /**
     * @var string 原文
     */
    private $message_original;
    /**
     * @var string スタックした文章
     */
    private $message_stack;
    /**
     * @var string 形態素解析した文章
     */
    private $message_morph;
    /**
     * @var array 結果配列
     */
    private $result_data;

    /**
     * BotTruthService constructor.
     * @param BotBaseService $bot_base_service
     * @param TruthLogService $truth_log_service
     * @param TruthDbService $db_service
     * @param TruthMatchService $match_service
     * @param TruthMorphService $morph_service
     */
    public function __construct(
        BotBaseService $bot_base_service, TruthLogService $truth_log_service, TruthDbService $db_service, TruthMatchService $match_service, TruthMorphService $morph_service
    )
    {
        $this->truth_log_service = $truth_log_service;
        $this->db_service = $db_service;
        $this->match_service = $match_service->setBotTruthService($this);
        $this->morph_service = $morph_service;
        $this->setBotBaseService($bot_base_service);
    }

    /**
     * 真理表をスキップするかどうか
     * @param array $truth_session 真理表用セッションデータ
     * @return bool
     */
    private function isSkipTruthTable($truth_session)
    {
        //通常聞き返しは真理表処理スキップ
        if ($this->status == config('bot.const.bot_status_select') && $this->bot_session_service->isHearBack()) {
            return true;
        }
        if (empty($truth_session)) {
            //セッション無し
        } else {
            //セッションあり
            if ($truth_session['done']) {
                return true;
            }
        }
        return false;
    }

    /**
     * APIに投げてスコアを確認する（真理表を使用時）
     * @param array $word_array キーフレーズ配列
     * @return bool
     */
    private function tryApiForTruthWord($word_array)
    {
        //ワード数が閾値以下なら処理しない
        if (count($word_array) <= config('bot.truth.hear_back_word_cnt')) return false;
        //APIに問い合わせをして、スコアがapi_no_hear_back_scoreを超えたら、次の処理(botService)、超えなかったら聞き返し
        $message = $this->bot_morph_service->setMessage($this->message)->execMorph()->getMessage();
        $api_result_ary = $this->bot_api_service->getApi()->setParams(['question' => $message, 'top' => config('bot.api.default.query_top')])->inquiry()->getResult();
        //MEMO:同じスコアのものが複数ある場合、絞られない問題あり…（morph_serviceの上書きをなくしてとりあえず回避）
        /* @var $api_result ApiResult */
        $api_result = $api_result_ary[0];
        $this->bot_base_service->setBotInfoData('try_api_message', $message);
        $this->bot_base_service->setBotInfoData('try_api', $api_result->toArray());
        if ($api_result->getScore() >= config('bot.truth.api_no_hear_back_score')) {
            return true;
        }
        return false;
    }

    /**
     * ヒントワード関連処理
     * @param array $truth_session 真理表用セッションデータ
     * @return array
     */
    private function hintWordProcess(&$truth_session)
    {
        $other = false;
        $no_hint = false;
        $next_hint = false;
        $hint_index = count($truth_session['yes']) + count($truth_session['no']);
        $hint_offset = $truth_session['hint_offset'];
        if ($this->bot_session_service->isHearBack()) {
            $input_info = $this->bot_log_service->getInputData();
            $word = $truth_session['hints'][$hint_index][$hint_offset];
            $this->truth_log_service->setInfoId($input_info['id'] ?? 0);
            if ($this->status == config('bot.const.bot_status_select_keyword')) {
                //複数の場合（選択肢）
                $idx = array_search($this->message, $word);
                $truth_session['yes'][] = $hint_index . '_' . $hint_offset . '_' . $idx;
                $this->truth_log_service->setYesWord($this->message)->create();
                $next_hint = true;
            } elseif ($this->status == config('bot.const.bot_status_select_keyword_other')) {
                $other = true;
            } elseif ($this->status == config('bot.const.bot_status_select_keyword_none')) {
                $no_hint = true;
            }
        }
//        logger()->debug($this->message);
//        logger()->debug('session', $truth_session ?? []);
        $yes_words = $no_words = [];
        foreach ($truth_session['yes'] as $idx => $pos) {
            $pos = explode('_', $pos);
            $yes_words[] = $this->bot_base_service->getDataConverterService()->convertOne('key_phrase', $truth_session['hints'][$pos[0]][$pos[1]][$pos[2]]);
        }
        foreach ($truth_session['no'] as $idx => $pos) {
            $pos = explode('_', $pos);
            $no_words[] = $truth_session['hints'][$pos[0]][$pos[1]][$pos[2]];
        }
        return [
            'hint_index' => $hint_index,
            'next_hint' => $next_hint,
            'no_hint' => $no_hint,
            'other' => $other,
            'yes_words' => $yes_words,
            'no_words' => $no_words,
        ];
    }

    /**
     * 真理表処理実行
     * @return $this
     */
    public function execTruth()
    {
        $this->result_data['normal'] = true;
        $truth_session = $this->bot_session_service->get('truth');
        //スキップチェック
        if ($this->isSkipTruthTable($truth_session)) {
            return $this;
        }
        $is_first = false;
        if (empty($truth_session)) {
            $is_first = true;
            $truth_session = ['yes' => [], 'no' => [], 'hints' => [], 'hint_offset' => 0, 'message' => $this->message, 'done' => false];
        }
        //真理表処理
        //yes no ワードリスト取得
        $yn_words = $this->hintWordProcess($truth_session);
        //マッチングサービスにメッセージ等をセット
        $match_service = $this->match_service->setMessage($truth_session['message'])
            ->setYesWords($yn_words['yes_words'])->setNoWords($yn_words['no_words']);
        //初回の場合、キーフレーズ数チェック＆API問い合わせ
        if ($is_first && $this->tryApiForTruthWord($match_service->getInputWords())) {
            $this->result_data['no_truth'] = true;
            return $this;
        }
        //候補ワードオフセット設定
        $truth_session['hint_offset'] = $yn_words['other'] ? $truth_session['hint_offset'] + 1 : 0;
        $match_service->setHintOffset($truth_session['hint_offset']);
        // 除外API_IDを設定
        $match_service->setIgnoreApiIds($this->bot_session_service->get('ignore_api_ids') ?? []);
        //実行
        $truth_result = $match_service->execMatch()->getResult();
        $this->result_data['hint_offset'] = $match_service->getHintOffset();
        $this->result_data['hint_index'] = count($yn_words['yes_words']) + count($yn_words['no_words']);
        $this->bot_base_service->setBotInfoData('truth_input_words', [$match_service->getMatchUseWords(), $match_service->getInputMorphWords(), $match_service->getSearchWords()]);
        $this->bot_base_service->setBotInfoData('truth_result', $truth_result);
        $this->message_stack = rtrim($truth_session['message'] . ' ' . implode(' ', $match_service->getYesWords()));
        //候補ワード無しを選んだ場合
        if ($yn_words['no_hint']) {
            $this->message = $this->message_stack;
            $this->result_data['init_question'] = true;
            $this->result_data['no_hint'] = true;
            return $this;
        }
        $this->message_morph = $this->morph_service->getMessage();
        if ($this->status == config('bot.const.bot_status_question')) {
            //初回の場合、応答ログ
            $this->bot_log_service->setStatus(config('const.bot.status.question_input.id'))
                ->setUserInput($this->message_original)->setUserInputMorph($this->message_morph)
                ->create();
        }
        if (env('APP_DEBUG')) {
            logger()->debug('TRUTH_RESULT', $truth_result);
        }
        //マッチング結果を分岐
        if ($truth_result['match']) {
            if ($truth_result['refine']) {
                //絞りこめた
                $api_ids = $truth_result['perfect'];
                foreach ($api_ids as $idx => $api_id) {
                    $learning_data = $this->bot_db_service->findLearningData($api_id);
                    $this->message = $learning_data['question'];
                    $this->result_data['qa'][] = (new ApiResult())->setId($api_id)->setQuestionStr($this->message)->setQuestion($learning_data['question_morph'])
                        ->setSelectionSymbol(index_to_symbol($idx))->toArray();
                }
                $this->result_data['num'] = count($api_ids);
                $this->result_data['hear_back_flg'] = true;
                $this->result_data['normal'] = false;
                $truth_session['done'] = true;
                $this->bot_session_service->set(true, 'hear_back_flg')->set($this->result_data['qa'], 'qa')->set($truth_session, 'truth')->save();
            } else {
                //絞り込めない
                if (!empty($truth_result['perfect']) && $match_service->getHintOffset() == 0) {
                    //完全一致が存在 & 「その他」を押してない　場合
                    foreach ($truth_result['perfect'] as $idx => $api_id) {
                        $learning_data = $this->bot_db_service->findLearningData($api_id);
                        $this->message = $learning_data['question'];
                        $this->result_data['qa'][] = (new ApiResult())->setId($api_id)->setQuestionStr($this->message)->setQuestion($learning_data['question_morph'])
                            ->setSelectionSymbol(index_to_symbol($idx))->toArray();
                    }
                    $this->result_data['num'] = count($truth_result['perfect']);
                    $this->bot_session_service->set($this->result_data['qa'], 'qa');
                }
                //候補ワード
                $this->result_data['hints'] = $truth_result['hints'];
                $this->result_data['normal'] = false;
                $this->result_data['hear_back_flg'] = true;
                $hint_index = $yn_words['hint_index'];
                if ($yn_words['next_hint']) $hint_index++;
                $truth_session['hints'][$hint_index][] = $truth_result['hints'];
                $this->bot_session_service->set(true, 'hear_back_flg')->set($truth_session, 'truth')->save();
            }
        } else {
            //回答なし
            $this->result_data['init_question'] = true;
        }
        return $this;
    }


    /**
     * 真理表ログサービス取得
     * @return TruthLogService
     */
    public function getTruthLogService(): TruthLogService
    {
        return $this->truth_log_service;
    }

    /**
     * 真理表DBサービス取得
     * @return TruthDbService
     */
    public function getDbService(): TruthDbService
    {
        return $this->db_service;
    }

    /**
     * 真理表マッチングサービス取得
     * @return TruthMatchService
     */
    public function getMatchService(): TruthMatchService
    {
        return $this->match_service;
    }

    /**
     * 真理表キーフレーズ抽出サービス取得
     * @return TruthMorphService
     */
    public function getMorphService(): TruthMorphService
    {
        return $this->morph_service;
    }

    /**
     * ステータス取得
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * ステータスセット
     * @param mixed $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * メッセージ取得
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * メッセージセット
     * @param mixed $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * オリジナルメッセージセット
     * @param mixed $message_original
     * @return $this
     */
    public function setMessageOriginal($message_original)
    {
        $this->message_original = $message_original;
        return $this;
    }

    /**
     * メッセージスタック取得
     * @return mixed
     */
    public function getMessageStack()
    {
        return $this->message_stack;
    }

    /**
     * 結果取得
     * @return mixed
     */
    public function getResultData()
    {
        return $this->result_data;
    }

    /**
     * セッションサービス取得
     * @return BotSessionService
     */
    public function getBotSessionService(): BotSessionService
    {
        return $this->bot_session_service;
    }

    /**
     * チャットボット基本サービスセット
     * @param BotBaseService $bot_base_service
     * @return $this
     */
    public function setBotBaseService(BotBaseService $bot_base_service)
    {
        $this->bot_base_service = $bot_base_service;
        $this->bot_db_service = $bot_base_service->getBotDbService();
        $this->bot_session_service = $bot_base_service->getBotSessionService();
        $this->bot_log_service = $bot_base_service->getBotLogService();
        $this->bot_api_service = $bot_base_service->getBotApiService();
        $this->bot_morph_service = $bot_base_service->getBotMorphService();
        $this->match_service->setBotBaseService($bot_base_service);
        return $this;
    }

}
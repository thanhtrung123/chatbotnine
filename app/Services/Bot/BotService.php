<?php

namespace App\Services\Bot;

use App\Services\Bot\Api\ApiResult;
use App\Services\Bot\Sns\SnsUidMapService;
use Illuminate\Contracts\Logging\Log;
use Util;

/**
 * チャットボット用サービス
 * Class BotService
 * @package App\Services\Bot
 */
class BotService
{
    //プロパティ
    /**
     * @var BotBaseService
     */
    private $base_service;
    /**
     * @var BotMorphService
     */
    private $morph_service;
    /**
     * @var BotTruthService
     */
    private $truth_service;
    /**
     * @var BotApiService
     */
    private $api_service;
    /**
     * @var BotSessionService
     */
    private $session_service;
    /**
     * @var BotLogService
     */
    private $log_service;
    /**
     * @var BotDbService
     */
    private $db_service;
    /**
     * @var BotEnqueteService
     */
    private $enquete_service;
    /**
     * @var Log
     */
    private $logger;
    /**
     * @var BotScenarioService
     */
    private $scenario_service;
    /**
     * @var BotAnswerService
     */
    private $answer_service;
    /**
     * @var string 入力文章
     */
    private $message;
    /**
     * @var string 入力文章（オリジナル）
     */
    private $message_original;
    /**
     * @var string 入力文章（スタック）
     */
    private $message_stack;
    /**
     * @var string 入力文章（QnA用キーワド変換済み）
     */
    private $message_morph;
    /**
     * @var string 選択されたボタンのメッセージ
     */
    private $message_btn;
    /**
     * @var string ステータス
     */
    private $status;
    /**
     * @var string $talk_id
     */
    private $talk_id;
    /**
     * @var string $prev_talk_id
     */
    private $prev_talk_id;
    /**
     * @var string $chat_id
     */
    private $chat_id;
    /**
     * @var string $disp_id（画面ID）
     */
    private $disp_id;
    /**
     * @var array 会話を戻る情報
     */
    private $prev_talk_info;
    /**
     * @var array 結果配列
     */
    private $result_data;
    /**
     * @var \App\Services\DataConvertService
     */
    private $data_converter;
    /**
     * @var bool 候補なし
     */
    private $is_no_hint;

    /**
     * BotService constructor.
     * @param Log $logger
     * @param BotBaseService $bot_base_service
     * @param BotTruthService $truth_service
     */
    public function __construct(
        Log $logger, BotBaseService $bot_base_service, BotTruthService $truth_service
    )
    {
        $this->logger = $logger;
        $this->base_service = $bot_base_service;
        $this->data_converter = $bot_base_service->getDataConverterService();
        $this->session_service = $bot_base_service->getBotSessionService();
        $this->log_service = $bot_base_service->getBotLogService();
        $this->api_service = $bot_base_service->getBotApiService();
        $this->morph_service = $bot_base_service->getBotMorphService();
        $this->db_service = $bot_base_service->getBotDbService();
        $this->answer_service = $bot_base_service->getBotAnswerService();
        $this->scenario_service = $bot_base_service->getBotScenarioService();
        $this->enquete_service = $bot_base_service->getBotEnqueteService();
        $this->truth_service = $truth_service;
    }

    /**
     * 初期化
     */
    public function init()
    {
        //channel情報セット
        $this->log_service->setChannel($this->base_service->getChannel());
        $this->base_service->getBotBiLogService()->setChannel($this->base_service->getChannel());
        //デバッグ情報初期化
        $this->base_service->setBotInfoData('init', ['message' => $this->message, 'status' => $this->status]);
        //セッションチェック
        $disp_id = $this->session_service->get('disp_id');
        if (empty($disp_id)) {
            $this->session_service->set($this->disp_id, 'disp_id')->save();
        } else {
            if ($this->disp_id != $disp_id) {
                //同一セッション内で別画面から操作
                $this->session_service->delete();
                $this->session_service->set($this->disp_id, 'disp_id')->save();
                throw new \Exception('セッションが不正です。');
            }
        }

        //セッションを初期化
        if ($this->status == config('bot.const.bot_status_show_category')) {
            //カテゴリ表示時（初期化時）セッション消す
            $this->session_service->delete();
        } else if ($this->status == config('bot.const.bot_status_question')) {
            //question→別ステータス変換処理　それ以外はセッション消す
            if (!$this->changeQuestionStatusProcess()) $this->session_service->delete();
        }

        //talk_id発行
        if ($this->session_service->get('talk_id')) {
            $this->talk_id = $this->session_service->get('talk_id');
        } else {
            $this->updateTalkId();
        }
        //SNSの場合、回答後、フィードバック以外はtalk_idを書き換える
        $ignore_status = [
            config('bot.const.bot_status_feedback'),
            config('bot.const.bot_status_related_answer'),
        ];
        if ($this->base_service->getChannel() != config('const.bot.channel.web.id') && $this->session_service->get('BOT_ANSWERED', false) && !in_array($this->status, $ignore_status)) {
            $this->updateTalkId();
        }

        //prev_talk_idがある場合はチェック
        $prev_check_ignore_status = [
            config('bot.const.bot_status_select_scenario'),
            config('bot.const.bot_status_select_category'),
        ];
        if (!empty($this->prev_talk_id) && !in_array($this->status, $prev_check_ignore_status)) {
//            logger()->debug('TIDCHK', ['tid' => $this->talk_id, 'ptid' => $this->prev_talk_id]);
            if ($this->talk_id != $this->prev_talk_id) {
                throw new \Exception('Select old button error.', config('const.bot.error.old_button.id'));
            }
        }

        //ボタンの文字を取得
        if (empty($this->message_btn)) $this->message_btn = $this->message;
        //キーフレーズ選択を戻る処理
        if (!empty($this->prev_talk_info)) {
            //セッション書き換え
            $truth_session = $this->session_service->get('truth');
            foreach (array_keys($truth_session['hints']) as $idx) {
                if ($idx < $this->prev_talk_info['hint_index']) continue;
                unset($truth_session['yes'][$idx]);
                if ($idx <= $this->prev_talk_info['hint_index']) continue;
                unset($truth_session['hints'][$idx]);
            }
            $truth_session['yes'] = array_values($truth_session['yes']);
            $truth_session['hints'] = array_values($truth_session['hints']);
            $truth_session['hint_offset'] = $this->prev_talk_info['hint_offset'];
            $truth_session['done'] = false;
            $this->session_service->set($truth_session, 'truth')->save();
        }

        $date = date('Y:m:d H:i:s');
        $this->base_service->setBotInfoData('meta', ['chat_id' => $this->chat_id, 'talk_id' => $this->talk_id, 'data' => $date]);
        //処理日時、IDセット
        $this->log_service->setActionDatetime($date)
            ->setChatId($this->chat_id)
            ->setTalkId($this->talk_id);
        //API側の回答が学習データのIDの場合
        if (config('bot.api.answer_is_id')) {
            $this->data_converter->setConvert('answer', function ($val) {
                return $this->db_service->findAnswer($val);
            });
        }
        //デモ以外だと元の質問文がなくなるので戻す？
        if (config('bot.api.use') != 'demo') {
            $this->data_converter->setConvert('question_str', function ($val, $row) {
                return $this->db_service->findLearningData($row['id'])['question'] ?? $val;
            });
        }
        //キーフレーズ変換用データコンバーター
        $this->data_converter->setConvert('key_phrase', function ($val) {
            $key_phrase_row = $this->truth_service->getDbService()->getRepositoryKeyPhrase()->findOneBy(['key_phrase_id' => $val]);
            $key_phrase = $val;
            if (isset($key_phrase_row['replace_word'])) $key_phrase = $key_phrase_row['replace_word'];
            elseif (isset($key_phrase_row['word'])) $key_phrase = $key_phrase_row['word'];
            return $key_phrase;
        });
        //コンバーターセット
        $this->api_service->getApi()->setConverter($this->data_converter);

        $this->is_no_hint = false;
        return $this;
    }

    /**
     * 質問ステータス変更処理
     * @return bool
     */
    public function changeQuestionStatusProcess()
    {
        $processed = false;
        $select_button = $this->session_service->get('select_button') ?? [];
        //前回のボタン生成データをチェックして同じ文言があればステータ変更
        $option = 'KVa';
        foreach ($select_button as $button_row) {
            foreach ($button_row as $row) {
                if (mb_convert_kana($this->message, $option) != mb_convert_kana($row['message'], $option)) continue;
                $this->message_btn = $this->message;
                $this->message = $row['symbol'] ?? $row['message'];
                $this->status = $row['status'];
                $processed = true;
                break 2;
            }
        }
        $this->session_service->set($processed ? $this->message : null, 'selected_symbol')->save();
        return $processed;
    }

    /**
     * 実行
     * @return $this
     */
    public function exec()
    {
        if (!isset($this->message) || empty($this->status)) {
            //必須パラメータがなかったら終了
            throw new \InvalidArgumentException('message and status is required.');
        }
        $this->message_original = $this->message_stack = $this->message;
        $this->result_data = [];
        try {
            // 入力文字の整形(ex.半角カナ文字を全角、全角数字を半角に変換など)
            $this->message = trim(mb_convert_kana($this->message, 'KVas'));
            //数値のみの場合、Zeroにする
            if ($this->status == config('bot.const.bot_status_question') && preg_match('/^\d+$/', $this->message)) {
                $this->message = '0';
            }

            //初期化
            $this->init();
//            logger()->debug("CID:{$this->chat_id} TID:{$this->talk_id} SID:{$this->getBaseService()->getBotSessionService()->getSessionId()}");
            // 情報セット
            $this->base_service->setBotInfoData('exec', ['message' => $this->message, 'status' => $this->status]);

            //有人チャット
            if ($this->status == config('bot.const.bot_status_chat_call')) {
                $this->log_service->setStatus(config('const.bot.status.chat_start.id'))
                    ->create();
                return $this;
            }
            //キーフレーズ中のこの中にありません選択
            if ($this->status == config('bot.const.bot_status_show_hint')) {
                $this->result_data['hear_back_flg'] = true;
                $truth_session = $this->session_service->get('truth');
                $hint_index = count($truth_session['hints']) - 1;
                $this->result_data['hints'] = $truth_session['hints'][$hint_index][0];
                $truth_session['hint_offset'] = 0;
                $this->session_service->set($truth_session, 'truth')->save();
                $this->result_data['hint_index'] = $hint_index;
                // 選択されなかった質問文は次に出てこないように除外に追加
                $ignore_api_ids = $this->session_service->get('ignore_api_ids') ?? [];
                $id_list = array_column($this->session_service->get('qa'), 'id');
                $ignore_api_ids = array_unique(array_merge($ignore_api_ids, $id_list));
                $this->session_service->set($ignore_api_ids, 'ignore_api_ids')->save();
                return $this;
            }
            //フィードバック
            if ($this->status == config('bot.const.bot_status_feedback')) {
                $this->execFeedback();
                return $this;
            }
            // 表示する回答が一意に絞れる場合（サジェスト入力により質問文と完全一致する場合 or 関連質問を選択）
            if (in_array($this->status, [config('bot.const.bot_status_question'), config('bot.const.bot_status_related_answer')])) {
                $learning_data = [];
                $log_status;
                // 関連質問で選択したQAデータを取得
                if ($this->status == config('bot.const.bot_status_related_answer')) {
                    // 関連質問を選択した場合はTalkIDを変更する
                    $this->updateTalkId();
                    $this->log_service->setTalkId($this->talk_id);
                    $learning_data = $this->db_service->findLearningData($this->message);
                    $log_status = config('const.bot.status.related_answer.id');
                } // サジェストで入力された質問文のQAデータを取得
                else {
                    $learning_data = $this->db_service->findLearningDataFromQuestion($this->message);
                    $log_status = config('const.bot.status.question_answer.id');
                }
                // QAデータが取得できた場合はレスポンス処理をおこなう
                if ($learning_data != FALSE) {
                    // 学習データを回答用データに整形
                    $qa_save_ary = Util::learningDataToQaAry([$learning_data]);
                    // 回答が確定したログを記録
                    $this->log_service->setStatus($log_status)
                        ->setUserInput($qa_save_ary[0]['question_str'])->setResponse($qa_save_ary[0])->create();
                    // レスポンスデータの作成
                    $this->result_data['num'] = count($qa_save_ary);
                    $this->result_data['qa'] = $qa_save_ary;
                    $this->result_data['hear_back_flg'] = false;
                    $this->result_data['hear_back_result'] = true;
                    return $this;
                }
            }
            //シナリオモード
            if ($this->execScenario()) {
                return $this;
            }
            //真理表を使用する場合
            if (config('bot.truth.enabled')) {
                //真理表用処理
                $this->truth_service->setBotBaseService($this->base_service);
                $truth_result = $this->truth_service->setMessage($this->message)->setMessageOriginal($this->message_original)
                    ->setStatus($this->status)->execTruth()->getResultData();
                $this->message_stack = $this->truth_service->getMessageStack() ?? $this->message_original;
                if ($truth_result['normal']) { //通常処理に投げる場合
                    //入力ワード変更
                    $this->message = $this->truth_service->getMessage();
                    if (!empty($truth_result['init_question'])) {//質問初期化
                        $this->status = config('bot.const.bot_status_question');
                        $this->session_service->set(false, 'hear_back_flg')->set(null, 'truth')->save();
                    }
                    $this->is_no_hint = empty($truth_result['no_hint']) ? false : true;
                    if (env('APP_DEBUG') && $this->is_no_hint) logger()->debug('NO_HINT_SRC', [$this->message]);
                    //通常処理
                    $this->execNormal(empty($truth_result['no_truth']) ? true : false);
                } else {
                    //真理表使用結果セット
                    //FIXME:暫定対応
                    if (!empty($truth_result['qa'])) {
                        $truth_result['qa'] = $this->ignoreDiffAnswer($truth_result['qa']);
                        $truth_result['num'] = count($truth_result['qa']);
                        $this->truth_service->getBotSessionService()->set($truth_result['qa'], 'qa')->save();
                    }
                    $this->result_data = $truth_result;
                }
            } else {
                //通常処理
                $this->execNormal();
            }
        } catch (\Exception $ex) {
            dd($ex);
            $this->logger->error('[BOT-ERROR]' . $ex->getMessage() . ' [CODE]' . $ex->getCode());
            if (env('APP_DEBUG')) {
                $this->result_data['err'] = $ex->getMessage();
                $this->logger->error('trace', $ex->getTrace());
            } else {
                $this->result_data['err'] = config('bot.const.bot_message_api_fail');
            }
            $this->result_data['err_code'] = $ex->getCode();
        }
        return $this;
    }

    /**
     * シナリオ実行
     * @return bool
     */
    private function execScenario()
    {
        //シナリオモード無効の場合
        if (!config('bot.scenario.enabled')) {
            if ($this->status == config('bot.const.bot_status_show_category')) {
                $this->result_data['category'] = [];
                return true;
            } else {
                return false;
            }
        }
        //シナリオモード
        if (in_array($this->status, [
            config('bot.const.bot_status_show_category'),   //カテゴリ(表示)
            config('bot.const.bot_status_select_category'), //カテゴリ
            config('bot.const.bot_status_select_scenario'), //シナリオ
            config('bot.const.bot_status_select_answer'),   //回答
            config('bot.const.bot_status_select_no_answer'),//この中にない
        ])) {
            $before_result_status = $this->session_service->get('result_status');
            $this->result_data = $this->scenario_service->setStatus($this->status)->setId($this->message)->exec()->getResult();
            $select_button = [];
            $status = config('const.bot.status.scenario_answer.id');
            switch ($this->status) {
                // カテゴリを選択した場合
                case config('bot.const.bot_status_select_category'):
                    // カテゴリ名を選択
                    $cate_name = $this->scenario_service->getCategories()[$this->message] ?? $this->message;
                    // カテゴリ選択のログ
                    $this->log_service->setStatus(config('const.bot.status.question_input.id'))->setUserInput($cate_name)->create();
                    break;
                case config('bot.const.bot_status_select_no_answer'):
                    $select_button = $this->session_service->get('select_button')['default'];
                    $status = config('const.bot.status.scenario_no_answer.id');
                    // 回答無し
                    $this->log_service->setStatus(config('const.bot.status.scenario_no_answer.id'))->updateQuestionInput();
                    //talk_idを消す
                    $this->session_service->set(null, 'talk_id')->save();
                    break;
                case config('bot.const.bot_status_select_answer'):
                    //MEMO:回答→回答の場合talk_idを更新する
                    if ($before_result_status == config('bot.const.bot_result_status_answer')) {
                        $this->updateTalkId();
                        $this->log_service->setTalkId($this->talk_id);
                    }
                    $this->log_service->setStatus($status)->setResponse($this->result_data['qa'][0] ?? [])->create();
                    foreach ($select_button as $row) {
                        $learning_data = $this->db_service->findLearningData($row['symbol']);
                        $this->log_service->setStatus(config('const.bot.status.response_other.id'))->setApiId($learning_data['api_id'])
                            ->setApiQuestion($learning_data['question'])->setApiAnswer($learning_data['answer'])->create();
                    }
                    break;
            }
            return true;
        }

        if ($this->status == config('bot.const.bot_status_question')) {
            $this->scenario_service->setMessage($this->message);
            // カテゴリ名一致チェック
            $category_id = $this->scenario_service->matchCategoryId();
            // シナリオ一致チェック
            $scenario_id = $this->scenario_service->matchScenarioId();
            // シナリオキーワードチェック
            $scenario_ids = $this->scenario_service->matchRelationKeywordScenarioIds();
            // シナリオと一致した場合
            if ($category_id !== null || $scenario_id !== null || $scenario_ids !== null) {
                // カテゴリ名が一致する場合（質問ステータの場合のみ）
                if ($category_id !== null) {
                    $this->result_data = $this->scenario_service->setStatus(config('bot.const.bot_status_select_category'))->setId($category_id)->exec()->getResult();
                } // シナリオと完全一致する場合
                else if ($scenario_id !== null) {
                    $this->result_data = $this->scenario_service->setStatus(config('bot.const.bot_status_select_scenario'))->setId($scenario_id)->exec()->getResult();
                } //シナリオキーワードと一致する場合
                else if ($scenario_ids !== null) {
                    $this->result_data = $this->scenario_service->setStatus(config('bot.const.bot_status_select_scenario'))->setIds($scenario_ids)->exec()->getResult();
                }
                // ログを記録する
                $this->log_service->setStatus(config('const.bot.status.question_input.id'))->setUserInput($this->message_original)->create();
                return true;
            }
        }

        return false;
    }

    /**
     * FIXME: 暫定処理：同じ回答の質問をまとめる
     * @param array $qa QA配列
     * @return array
     */
    private function ignoreDiffAnswer($qa)
    {
        $qa_ary = [];
        foreach ($qa as $row) {
            $qa_ary[$row['id']] = $row;
        }
        $learning_data = plain_to_associative_array(
            $this->db_service->getLearningRepository()->setParams(['api_ids' => array_keys($qa_ary)])->filterByParams()->getDbResult()->getPlainArray(), 'api_id');
        $alpha_idx = 0;
        $answer_ary = [];
        $new_qa = [];
        foreach (array_keys($qa_ary) as $api_id) {
            $row = $learning_data[$api_id];
            if (isset($answer_ary[$row['answer']])) continue;
            $answer_ary[$row['answer']] = 1;
            $symbol = index_to_symbol($alpha_idx++);
            $new_qa_row = $qa_ary[$row['api_id']];
            $new_qa_row['selection_symbol'] = $symbol;
            $new_qa[] = $new_qa_row;
        }
        return $new_qa;
    }

    /**
     * 通常会話用
     * @param bool $is_truth 真理表使用
     */
    private function execNormal($is_truth = false)
    {
        //聞き返しフラグが有効な場合は回答内容を確認する
        if ($this->session_service->isHearBack() && $this->status == config('bot.const.bot_status_select')) {
            $this->execHearBack($is_truth);
            return;
        }

        //通常会話開始↓
        // 形態素解析結果をクエリとする場合、以下を処理
        $this->message_original = $this->message;
        if (config('bot.morph.enabled')) {
            $this->message = $this->morph_service->setMessage($this->message)->execMorph()->getMessage();
            if (config('bot.truth.no_key_phrase_inquiry_api')) {
                if (empty($this->message)) $this->message = $this->message_original;
            } else {
                if (empty($this->message)) return;
            }
            $this->message_morph = $this->message;
        }
        //応答状況出力
        if (!$is_truth) {
            $this->log_service->setStatus(config('const.bot.status.question_input.id'))
                ->setUserInput($this->message_original)->setUserInputMorph($this->message)
                ->create();
        }

        //質問内容をセッションに保存
        $this->session_service->set($this->message, 'msg')->save();
        //回答処理
        $this->execAnswer();
    }

    /**
     * 実行：フィードバック
     */
    private function execFeedback()
    {
        $qa = $this->session_service->get('qa');
        //応答状況出力
        $fb_status = ($this->message == config('const.common.yes_no.yes.name')) ?
            config('const.bot.status.feedback_yes.id') : config('const.bot.status.feedback_no.id');
//        logger()->debug('FB', $qa ?? []);
        $this->log_service->setStatus($fb_status)->setApiId($qa[0]['id'])->create();
        $this->result_data['feedback'] = ($this->message == config('const.common.yes_no.yes.name'));
        //talk_idを消す
        $this->session_service->set(null, 'talk_id')->save();
    }

    /**
     * 実行：聞き返し
     * @param bool $is_truth 真理表使用
     */
    private function execHearBack($is_truth = false)
    {
        $qa_ary = $this->session_service->get('qa');
        $end = false;
        $qa_id = $qa_ary[0]['id'];
        if ($this->message == config('const.common.yes_no.no.name')) {
            //いいえの場合
            $end = true;
        } else if (preg_match('/^[A-Za-z0-9]$/i', $this->message)) {
            //英数字の場合
            for ($i = 0; $i < count($qa_ary); $i++) {
                if ($qa_ary[$i]['selection_symbol'] == $this->message) {
                    $qa_id = $qa_ary[$i]['id'];
                    break;
                }
            }
        }
        if ($end) {
            //終了
            //応答状況出力:回答無し
            $this->log_service->setStatus(config('const.bot.status.no_answer.id'))
                ->updateQuestionInput();
        } else {
            //MEMO:聞き返しの場合APIを通さないようにする（同じ解析後文言だと回答が同じになるバグ対策）
            $learning_data = $this->db_service->findLearningData($qa_id);
            $qa_save_ary = Util::learningDataToQaAry([$learning_data]);
            //QAデータをセッションに保存
            $this->session_service->set($qa_save_ary, 'qa')->save();
            //応答状況出力
            if ($is_truth) {
                //真理表を使用している場合、候補データがないので登録
                $data = $this->log_service->existsStatus([config('const.bot.status.response_select.id'), config('const.bot.status.response_other.id')]);
                if (empty($data)) {
                    $this->log_service->setStatus(config('const.bot.status.response_select.id'))
                        ->setResponse($qa_save_ary[0])->create();
                }
            }
            //回答確定
            $this->log_service->setStatus(config('const.bot.status.question_answer.id'))
                ->setResponse($qa_save_ary[0])
                ->updateQuestionInput();
            //レスポンス
            $this->result_data['num'] = count($qa_save_ary);
            $this->result_data['qa'] = $qa_save_ary;
            $this->result_data['hear_back_flg'] = false;
            $this->result_data['hear_back_result'] = true;
        }
    }

    /**
     * 回答処理
     */
    private function execAnswer()
    {
        // QnA Makerへ問い合わせ
        $response_qa_ary = [];
        /** @var ApiResult[] $api_result_ary */
        $api_result_ary = $this->api_service->getApi()->setParams(['question' => $this->message, 'top' => config('bot.api.default.query_top')])
            ->inquiry()
            ->getResult();
        $api_result_ary_tmp = [];
        foreach ($api_result_ary as $api_result) {
            $api_result_ary_tmp[] = $api_result->toArray();
        }
        $this->base_service->setBotInfoData('api_result_message', $this->message);
        $this->base_service->setBotInfoData('api_result', $api_result_ary_tmp);

        //回答取得
        $has_answer = $this->answer_service->execAnswer($api_result_ary);
        if ($has_answer) {
            //回答あり
            $this->result_data = $this->answer_service->getResultData();
            //FIXME:暫定対応
            if (!empty($this->result_data['qa'])) {
                $this->result_data['qa'] = $this->ignoreDiffAnswer($this->result_data['qa']);
                $this->result_data['num'] = count($this->result_data['qa']);
            }
            if ($this->result_data['hear_back_flg']) {
                //聞き返しがある場合
                /* @var $api_result ApiResult */
                foreach ($api_result_ary as $idx => $api_result) {
                    $response_qa_ary[$idx] = $api_result->toArray();
                    foreach ($this->result_data['qa'] as $qa_idx => $qa_row) {
                        if ($qa_row['id'] == $api_result->getId()) {
                            $response_qa_ary[$idx]['is_select'] = 1;
                            $response_qa_ary[$idx]['selection_symbol'] = $qa_row['selection_symbol'] ?? '1';
                        }
                    }
                }
                //回答確定用にメッセージをセッションに保持
                $this->session_service->set(['original' => $this->message_original, 'morph' => $this->message], 'messages')
                    ->save();
            } else {
                //聞き返しがない場合
                foreach ($api_result_ary as $idx => $api_result) {
                    $response_qa_ary[$idx] = $api_result->toArray();
                    if ($this->result_data['qa'][0]['id'] == $api_result->getId()) {
                        $response_qa_ary[$idx]['is_select'] = 1;
                    }
                }
            }
            //応答状況出力
            foreach ($response_qa_ary as $response_qa_row) {
                $response_status = (empty($response_qa_row['is_select'])) ?
                    config('const.bot.status.response_other.id') : config('const.bot.status.response_select.id');
                $this->log_service->setStatus($response_status)->setResponse($response_qa_row)->create();
            }
            //聞き返しがない場合、回答確定レコード出力
            if (!$this->result_data['hear_back_flg']) {
                $this->log_service->setStatus(config('const.bot.status.question_answer.id'))
                    ->setResponse($this->result_data['qa'][0])->updateQuestionInput();
            }
            //セッションに保存
            $this->session_service->set($this->result_data['hear_back_flg'], 'hear_back_flg')
                ->set($this->result_data['qa'], 'qa')->save();
        } else {
            //応答状況出力
            //回答候補
            foreach ($api_result_ary as $api_result) {
                $this->log_service->setStatus(config('const.bot.status.response_other.id'))
                    ->setResponse($api_result->toArray())->create();
            }
            //回答なし
            $this->log_service->setStatus(config('const.bot.status.no_answer.id'))
                ->updateQuestionInput();
        }
    }

    /**
     * 選択ボタン用データ作成
     * @param string $status ボタンステータス
     * @param string $message ボタンメッセージ
     * @param string null|string ボタンシンボル
     * @param array オプション
     * @return array
     */
    private function createSelectButtonDataRow($status, $message, $symbol = null, $option = [])
    {
        $cls_config = config('bot.const.bot_button_classes');
        if (isset($cls_config[$status])) {
            if (is_array($cls_config[$status])) {
                if (isset($cls_config[$status][$message])) {
                    $option['class'] = $cls_config[$status][$message];
                } elseif (isset($cls_config[$status][$symbol])) {
                    $option['class'] = $cls_config[$status][$symbol];
                }
            } else {
                $option['class'] = $cls_config[$status];
            }
        }
        $option['talk_id'] = $this->talk_id;
        return [
            'status' => $status,
            'message' => $message,
            'symbol' => ($symbol === null) ? $message : $symbol,
            'option' => $option,
        ];
    }

    /**
     * @param $href
     * @param $message
     * @param null $target
     * @param array $option
     * @return array
     */
    private function createLinkButtonDataRaw($href, $message, $target = null, $option = [])
    {
        $option['talk_id'] = $this->talk_id;
        return [
            'href' => $href,
            'message' => $message,
            'target' => $target,
            'option' => $option,
        ];
    }

    /**
     * ボタン生成用データを結果にセット
     */
    private function assignSelectButtonToResult()
    {
        $select_button = [];
        $result_status = config('bot.const.bot_result_status_no_answer');
        $skip_hint = false;
        if (isset($this->result_data['qa']) && $this->status != config('bot.const.bot_status_show_hint')) {
            //回答がある場合
            if (count($this->result_data['qa']) == 1 && !$this->result_data['hear_back_flg']) {
                //回答1件＋聞き返しではない＝回答表示
                $result_status = config('bot.const.bot_result_status_answer');
            } else if (count($this->result_data['qa']) == 1 && $this->result_data['hear_back_flg'] && !isset($this->result_data['hints'])) {
                //回答1件＋聞き返し＋候補ワード無し＝はい・いいえ
                $result_status = config('bot.const.bot_result_status_yn');
                $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_select'), config('bot.const.bot_symbol_yes'));
                $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_select'), config('bot.const.bot_symbol_no'));
            } else {
                //それ以外
                foreach ($this->result_data['qa'] as $row) {
                    $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_select'), $row['question_str'], $row['selection_symbol']);
                }
                if (isset($this->result_data['hints'])) {
                    $skip_hint = true;
                    $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_show_hint'), config('bot.const.bot_symbol_not_in'), config('bot.const.bot_symbol_not'));
                }
                $result_status = config('bot.const.bot_result_status_select');
            }
        }
        if (isset($this->result_data['hints']) && !$skip_hint) {
            //候補ワードがある場合
            if (is_array($this->result_data['hints'])) {
                foreach ($this->result_data['hints'] as $hint) {
                    $status = config('bot.const.bot_status_select_keyword');
                    if ($hint == config('bot.const.bot_symbol_not_in')) {
                        $status = config('bot.const.bot_status_select_keyword_none');
                    } else if ($hint == config('bot.const.bot_symbol_other_hint')) {
                        $status = config('bot.const.bot_status_select_keyword_other');
                    }
                    //データコンバートしてkey_phrase_id→文字列に変換
                    $select_button[] = $this->createSelectButtonDataRow($status, $this->data_converter->convertOne('key_phrase', $hint), $hint, [
                        'prev_talk_info' => [
                            'hint_offset' => $this->result_data['hint_offset'] ?? 0,
                            'hint_index' => $this->result_data['hint_index'] ?? 0
                        ]]);
                }
                $result_status = config('bot.const.bot_result_status_keyword');
            } else {
                //MEMO:このパターンは無くなった
            }
        }
        $find_scenario_answer = false;
        //シナリオがある場合
        if (isset($this->result_data['scenario'])) {
            foreach ($this->result_data['scenario'] as $row) {
                if (!empty($row['api_id'])) {
                    //シナリオ中に回答がある場合
                    $learning_data = $this->db_service->findLearningData($row['api_id']);
                    $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_select_answer'), $learning_data['question'], $row['api_id']);
                    $find_scenario_answer = true;
                } else {
                    $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_select_scenario'), $row['name'], $row['scenario_id']);
                }
            }
            $result_status = config('bot.const.bot_result_status_scenario');
        }
        if ($find_scenario_answer) {
            $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_select_no_answer'), config('bot.const.bot_symbol_not_in'));
        }
        //カテゴリがある場合
        if (isset($this->result_data['category'])) {
            foreach ($this->result_data['category'] as $id => $val) {
                $select_button[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_select_category'), $val, $id);
            }
            $result_status = config('bot.const.bot_result_status_category');
        }
        //フィードバック
        if (isset($this->result_data['feedback'])) {
            $result_status = config('bot.const.bot_result_status_feedback');
        }
        //聞き返し結果
        if (!empty($this->result_data['hear_back_result'])) {
            $result_status = config('bot.const.bot_result_status_answer');
        }
        //有人チャット
        if ($this->status == config('bot.const.bot_status_chat_call')) {
            $result_status = config('bot.const.bot_result_status_chat_call');
        }
        //結果にセット
        if (!empty($select_button))
            $this->result_data['select_button']['default'] = $select_button;
        $this->result_data['result_status'] = $this->result_data['result_status'] ?? $result_status;
    }

    /**
     * 関連する回答を結果にセット
     */
    private function assignRelatedAnswer()
    {
        $related_answer = [];
        $learning_relation_gen = $this->db_service->getLearningRelationRepository()->setOrder(['order' => 'asc'])
            ->setParams(['api_id' => $this->result_data['qa'][0]['id']])->filterByParams()->getDbResult()->getGenerator();
        foreach ($learning_relation_gen as $row) {
            $related_answer[] = $this->createSelectButtonDataRow(config('bot.const.bot_status_related_answer'), $row['name'], $row['relation_api_id']);
        }
        if (!empty($related_answer))
            $this->result_data['select_button']['related_answer'] = $related_answer;
    }

    /**
     * 追加ボタンを結果にセット
     */
    private function assignOtherButton()
    {
        switch ($this->result_data['result_status']) {
            case config('bot.const.bot_result_status_answer'):
                //回答だったらフィードバックボタンを付ける
                $this->result_data['select_button']['feedback'] = [
                    $this->createSelectButtonDataRow(config('bot.const.bot_status_feedback'), config('bot.const.bot_symbol_feedback_yes'), config('bot.const.bot_symbol_yes')),
                    $this->createSelectButtonDataRow(config('bot.const.bot_status_feedback'), config('bot.const.bot_symbol_feedback_no'), config('bot.const.bot_symbol_no')),
                ];
                //アンケート
                if (config('bot.enquete.enabled')) {
                    $session_id = $this->session_service->getSessionId();
                    if (($this->base_service->getChannel() == config('const.bot.channel.web.id'))) { //WEB
                        $this->session_service->getSession()->put('ENQUETE_EXP_TIME', time());
                        $enquete_params = [$this->enquete_service->makeFormHash(config('bot.enquete.form_id'))];
                    } else {                                                                              //Webhook
                        $enqeute_key = $this->base_service->getSnsUidMapService()
                            ->setChannel($this->base_service->getChannel())
                            ->generateEnqueteKey($session_id);
                        $enquete_params = [$this->enquete_service->makeFormHash(config('bot.enquete.form_id'), $enqeute_key), $enqeute_key];
                    }
                    $enquete_uri = route('enquete.entry', $enquete_params);
                    $this->result_data['select_button']['enquete'] = [
                        $this->createLinkButtonDataRaw($enquete_uri, config('bot.const.bot_symbol_enquete'))
                    ];
                }
                break;
            case config('bot.const.bot_result_status_no_answer'):
                //回答なしだったらチャット呼び出しボタンを付ける
                if (config('bot.human_chat.enabled'))
                    $this->result_data['select_button']['chat_call'] = [
                        $this->createSelectButtonDataRow(config('bot.const.bot_status_chat_call'), config('bot.const.bot_symbol_chat_call')),
                    ];
                break;
            case config('bot.const.bot_result_status_feedback'):
                //フィードバック（いいえ）だったらチャット呼び出しボタンを付ける
                if (config('bot.human_chat.enabled') && $this->message == config('bot.const.bot_symbol_no'))
                    $this->result_data['select_button']['chat_call'] = [
                        $this->createSelectButtonDataRow(config('bot.const.bot_status_chat_call'), config('bot.const.bot_symbol_chat_call')),
                    ];
                break;
        }
    }

    /**
     * オウム返しを結果にアサイン
     */
    public function assignRepeatMessage()
    {
        //オウム返し（仕様未確認）
        if ($this->result_data['result_status'] == config('bot.const.bot_result_status_no_answer')) {
            return;
        }
        if ($this->status == config('bot.const.bot_status_question')) {
            $msg_key = $this->is_no_hint ? 'msg_stack' : 'msg';
            $this->result_data[$msg_key] = $this->message_stack;
        } else if ($this->status == config('bot.const.bot_status_select_keyword')) {
            $this->result_data['msg'] = $this->message_stack;
        }
    }

    /**
     * BIツール用ログ書き出し
     */
    private function outputBiLog()
    {
        if (empty($this->chat_id)) return;//初回のカテゴリ表示は不要？
        //BIログ用サービス
        $bi_log_service = $this->getBaseService()->getBotBiLogService();
        $bi_log_service->setChatId($this->chat_id)->setTalkId($this->talk_id)
            ->setStatus($this->result_data['input_status'])->setResultStatus($this->result_data['result_status']);
        //入力ステータスによって出力を分ける
        switch ($this->result_data['input_status']) {
            case config('bot.const.bot_status_question'):
                $bi_log_service->setUserInput($this->message_original);
                break;
        }
        //結果ステータスによって出力を分ける
        switch ($this->result_data['result_status']) {
            //回答表示
            case config('bot.const.bot_result_status_answer'):
                $bi_log_service->setSelectId($this->result_data['qa'][0]['id'])
                    ->setSelectMessage($this->message_btn)
                    ->setSelectMessage($this->result_data['qa'][0]['question_str']);
                break;
            //フィードバック表示
            case config('bot.const.bot_result_status_feedback'):
                $val = ($this->message == config('const.common.yes_no.yes.name')) ?
                    config('const.common.yes_no.yes.id') : config('const.common.yes_no.no.id');
                $bi_log_service->setSelectMessage($this->message_btn)
                    ->setSelectFeedback($val);
                break;
            //質問「はい・いいえ」選択肢表示
            case config('bot.const.bot_result_status_yn'):
                //質問選択肢表示
            case config('bot.const.bot_result_status_select'):
                $choice_questions = [];
                foreach ($this->result_data['select_button']['default'] as $idx => $btn_row) {
                    $qa = $this->result_data['qa'][$idx] ?? null;
                    if ($qa === null)
                        $choice_questions[] = "[]:{$btn_row['message']}";
                    else
                        $choice_questions[] = "[{$qa['id']}]:{$qa['question_str']}";
                }
                $bi_log_service->setChoiceQuestion(implode(',', $choice_questions));
                break;
            //キーフレーズ表示
            case config('bot.const.bot_result_status_keyword'):
                $key_phrases = array_column($this->result_data['select_button']['default'], 'message');
                $bi_log_service->setSelectMessage($this->message_btn)
                    ->setChoiceKeyPhrase(implode(',', $key_phrases));
                break;
            //シナリオ表示
            case config('bot.const.bot_result_status_scenario'):
                $choice_scenario = [];
                foreach ($this->result_data['select_button']['default'] as $idx => $btn_row) {
                    $choice_scenario[] = "[]:{$btn_row['message']}";
                }
                $bi_log_service->setSelectMessage($this->message_btn)
                    ->setChoiceQuestion(implode(',', $choice_scenario))
                    ->setScenarioUsed(config('const.common.on_off.on.id'));
                break;
            //カテゴリ表示
            case config('bot.const.bot_result_status_category'):

                break;
            //回答なし
            case config('bot.const.bot_result_status_no_answer'):
                $bi_log_service->setSelectMessage($this->message_btn);
                break;
            //有人チャット
            case config('bot.const.bot_result_status_chat_call'):
                $bi_log_service->setSelectMessage($this->message_btn)
                    ->setChatUsed(config('const.common.on_off.on.id'));
                break;
        }
        $bi_log_service->output();
    }

    /**
     * 結果返却前処理
     */
    private function dispatch()
    {
        $this->assignSelectButtonToResult();
        if ($this->result_data['result_status'] == config('bot.const.bot_result_status_answer')) {
            //回答の場合、関連する回答を取得
            $this->assignRelatedAnswer();
            $this->session_service->set(true, 'BOT_ANSWERED')->save();
        }
        //追加ボタン
        $this->assignOtherButton();
        //オウム返し
        $this->assignRepeatMessage();

        //その他返却値セット
        $this->session_service->set($this->result_data['select_button'] ?? [], 'select_button')->set($this->result_data['result_status'], 'result_status')->save();
        $this->result_data['selected_symbol'] = $this->session_service->get('selected_symbol');
        $this->result_data['talk_id'] = $this->talk_id;
        $this->result_data['input_status'] = $this->status;
        $this->result_data['enabled_talk_prev'] = config('bot.truth.enabled_talk_prev');
        if ($this->base_service->getIsDebug()) {
            $this->result_data['info'] = $this->base_service->getBotInfoData();
        }
        //BIツール用ログ
        $this->outputBiLog();
    }

    /**
     * talk_idの更新
     */
    private function updateTalkId()
    {
        $this->talk_id = $this->base_service->getTalkId();
        $this->session_service->set($this->talk_id, 'talk_id')->save();
    }

    /*
     * getter setter
     */

    /**
     * チャットボット基本サービス取得
     * @return BotBaseService
     */
    public function getBaseService(): BotBaseService
    {
        return $this->base_service;
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
     * ステータス取得
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * ステータスセット
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * chat_id 取得
     * @return string
     */
    public function getChatId()
    {
        return $this->chat_id;
    }

    /**
     * chat_id セット
     * @param string $chat_id
     * @return $this
     */
    public function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;
        return $this;
    }

    /**
     * 画面IDセット
     * @param mixed $disp_id
     * @return $this
     */
    public function setDispId($disp_id)
    {
        $this->disp_id = $disp_id;
        return $this;
    }

    /**
     * ボタン用メッセージセット
     * @param mixed $message_btn
     * @return $this
     */
    public function setMessageBtn($message_btn)
    {
        $this->message_btn = $message_btn;
        return $this;
    }

    /**
     * 戻る用会話情報セット
     * @param mixed $prev_talk_info
     * @return $this
     */
    public function setPrevTalkInfo($prev_talk_info)
    {
        $this->prev_talk_info = $prev_talk_info;
        return $this;
    }

    /**
     * 結果取得
     * @return mixed
     */
    public function getResultData()
    {
        $this->dispatch();
        return $this->result_data;
    }

    /**
     * チャットボット応答情報サービス取得
     * @return BotLogService
     */
    public function getLogService()
    {
        return $this->log_service;
    }

    /**
     * @return \App\Services\DataConvertService
     */
    public function getDataConverter()
    {
        return $this->data_converter;
    }

    /**
     * パラメータセット
     * @param array $params
     * @return $this
     */
    public function setRequestParams($params)
    {
        $this->setMessage($params['message'])
            ->setStatus($params['status'])
            ->setChatId($params['id']);
        if (!empty($params['disp_id']))
            $this->setDispId($params['disp_id']);
        if (!empty($params['prev_talk_info']))
            $this->setPrevTalkInfo($params['prev_talk_info']);
        if (!empty($params['disp_msg']))
            $this->setMessageBtn($params['disp_msg']);
        if (!empty($params['prev_talk_id']))
            $this->setPrevTalkId($params['prev_talk_id']);
        //disp_info=1の時のみデバッグ表示
        if (isset($params['disp_info']))
            $this->getBaseService()->setIsDebug($params['disp_info'] == config('const.common.on_off.on.id'));
        return $this;
    }

    /**
     * @param string $prev_talk_id
     * @return $this
     */
    public function setPrevTalkId(string $prev_talk_id)
    {
        $this->prev_talk_id = $prev_talk_id;
        return $this;
    }


}
<?php

namespace App\Services\Bot\Truth;

use App\Services\Bot\BotBaseService;
use App\Services\Bot\BotTruthService;

/**
 * 真理表用マッチングサービス
 * Class TruthService
 * @package App\Services\Truth
 */
class TruthMatchService
{
    /**
     * @var BotTruthService
     */
    private $bot_truth_service;
    /**
     * @var BotBaseService
     */
    private $bot_base_service;
    /**
     * @var TruthDbService
     */
    private $truth_db_service;
    /**
     * @var string 文章
     */
    private $message;
    /**
     * @var array 入力キーフレーズ
     */
    private $input_words = [];
    /**
     * @var array 分かち書きした入力キーフレーズ
     */
    private $input_morph_words = [];
    /**
     * @var array マッチング用キーフレーズ
     */
    private $match_use_words = [];
    /**
     * @var array YESワード配列
     */
    private $yes_words = [];
    /**
     * @var array NOワード配列
     */
    private $no_words = [];
    /**
     * @var array 検索用キーフレーズ
     */
    private $search_words = [];
    /**
     * @var array 結果配列
     */
    private $result = [];
    /**
     * @var int 候補ワードオフセット
     */
    private $hint_offset = 0;
    /**
     * @var array 除外API_ID配列
     */
    private $ignore_api_ids = [];

    /**
     * TruthMatchService constructor.
     * @param TruthDbService $truth_db_service
     */
    public function __construct(TruthDbService $truth_db_service)
    {
        $this->truth_db_service = $truth_db_service;
    }

    /**
     * マッチング実行
     * @return $this
     */
    public function execMatch()
    {
        $this->result = [
            'match' => false,
            'refine' => false,
            'perfect' => [],
            'info' => [],
        ];

        $this->match_use_words = array_merge($this->input_words, $this->yes_words);
        $this->input_morph_words = empty($this->input_morph_words) ? [] : array_merge($this->input_morph_words, $this->yes_words);
        $this->search_words = empty($this->search_words) ? [] : array_merge($this->search_words, $this->yes_words);
        //キーフレーズに対してのマッチング処理
        $result1 = $this->matchingProcess($this->match_use_words);
        $this->result['info'][] = $result1['info'] + ['perfect' => $result1['perfect']];
        //分かち書きしたキーフレーズに対してのマッチング処理
        $result2 = $this->matchingProcess($this->input_morph_words, true);
        $this->result['info'][] = $result2['info'] + ['perfect' => $result2['perfect']];
        //置換無しのキーフレーズ(+分かち書)に対してのマッチング処理
        $result3 = $this->matchingProcess($this->search_words, true);
        $this->result['info'][] = $result3['info'] + ['perfect' => $result3['perfect']];
        //キーフレーズに完全一致した
        if (!empty($result1['perfect'])) {
            $this->result['perfect'] = array_slice($result1['perfect'], 0, config('bot.truth.no_one_refine_max_answer'));
        } else {
            $this->result['perfect'] = array_slice($result2['perfect'], 0, config('bot.truth.no_one_refine_max_answer'));
        }
        //候補キーフレーズをマージ
        $merge_hint_words = $this->mergeHintWords([$result1['hint_words'], $result2['hint_words'], $result3['hint_words']]);
        if (empty($merge_hint_words) && empty($this->result['perfect'])) {
            //候補ワードが無かった
            return $this;
        } else if (empty($merge_hint_words)) {
            //完全一致のみ
            $this->result['match'] = $this->result['refine'] = true;
            return $this;
        }

        //候補ワードヒット時
        $this->result['match'] = true;
        //ページング処理
        $limit = config('bot.truth.hint_word_max');
        $offset = $this->hint_offset * $limit;
        $hints = array_slice($merge_hint_words, $offset, $limit);
        //logger()->debug('OLW', ['OL' => $offset + $limit, 'CW' => count($words)]);
        if (($offset + $limit) < count($merge_hint_words)) {
            $hints[] = config('bot.const.bot_symbol_other_hint');
        } else {
            $hints[] = config('bot.const.bot_symbol_not_in');
        }
        $this->result['hints'] = $hints;
        return $this;
    }

    /**
     * 真理表にマッチしたキーフレーズを取得
     * @param array $words キーフレーズ
     * @return array
     */
    private function getMatchTruthWords(array $words)
    {
        //入力ワードからマッチした真理表を取得
        $match_words = $this->truth_db_service->getTruthWords(['key_phrases' => $words, 'ignore_api_ids' => $this->ignore_api_ids]);
        //マッチしない場合終了
        if (empty($match_words)) return [];
        //マッチした真理表から関連ワードを取得
        $truth_words = $this->truth_db_service->getTruthWords(['api_ids' => array_keys($match_words)]);
        return [$match_words, $truth_words];
    }

    /**
     * マッチング処理
     * @param array $words キーフレーズ
     * @param bool $priority_rate_down 優先度を下げる
     * @return array
     */
    private function matchingProcess(array $words, bool $priority_rate_down = false)
    {
        $result = [
            'info' => ['match_status' => 'NO_MATCH'],
            'perfect' => [],
            'hint_words' => [],
        ];
        if (count($words) == 0) return $result;
        $priority_rate = $priority_rate_down ? 1 / count($words) : 1;
        //DBから取得
        $match_truth_words = $this->getMatchTruthWords($words);
        if (empty($match_truth_words)) return $result;
        list($match_words, $truth_words) = $match_truth_words;
        //
        //優先度の高い真理表を検索
        $many_match_truth = $this->searchManyMatchTruth($words, $match_words, $truth_words);
        //完全一致あり
        if (!empty($many_match_truth['perfect'])) {
            $result['perfect'] = array_keys($many_match_truth['perfect']);
            //通常一致無し
            if (empty($many_match_truth['normal'])) {
                $result['info']['match_status'] = 'PERFECT_ONLY';
                return $result;
            }
        }
        $refine_truth_words = $many_match_truth['normal'];
        //
        //複数マッチして、すべて同じ真理表の場合
        $is_all_eq = $this->isAllEqual($refine_truth_words);
        //絞り込めたら返却
        if ($this->isRefined($refine_truth_words) || $is_all_eq) {
            $result['perfect'] = array_merge($result['perfect'], array_keys($refine_truth_words));
            $result['info']['match_status'] = 'REFINED';
            return $result;
        }
        //候補キーフレーズを検索
        $hint_words_result = $this->searchHintWord($words, $refine_truth_words, $match_words, $priority_rate);
        //完全一致あり
        if (!empty($hint_words_result['perfect'])) {
            $result['perfect'] = array_merge($result['perfect'], $hint_words_result['perfect']);
            //絞り込まれた
            if ($hint_words_result['refine']) {
                $result['info']['match_status'] = 'REFINE_HINT';
                return $result;
            }
        }
        //候補キーフレーズ
        $result['info']['match_status'] = 'NORMAL';
        $result['hint_words'] = $hint_words_result['hint_words'];
        //デバッグ用
        if ($this->getBotBaseService()->getIsDebug()) {
            $debug_ary = [];
            $idx = 0;
            foreach ($result['hint_words'] as $key_phrase_id => $api_ids) {
                $word = $this->getBotBaseService()->getDataConverterService()->convertOne('key_phrase', $key_phrase_id);
                $debug_ary["{$idx}_{$word}"] = [
                    'key_phrase_id' => $key_phrase_id,
                    'priority' => current($api_ids),
                    'api_ids' => $api_ids,
                ];
                $idx++;
            }
            $result['info']['debug_ary'] = $debug_ary;
        }
        return $result;
    }

    /**
     * 一致率が高いキーフレーズを検索
     * @param array $input_words キーフレーズ
     * @param array $match_truth マッチした真理表
     * @param array $truth_words マッチしたキーフレーズがある真理表
     * @return array
     */
    private function searchManyMatchTruth(array $input_words, array $match_truth, array $truth_words)
    {
        $input_words_count = count($input_words);
        $normal_match = $perfect_match = [];
        $max_match_count = count(current($match_truth));
        if ($max_match_count > $input_words_count) $max_match_count = $input_words_count;
        foreach ($match_truth as $api_id => $match_row) {
            $truth_row = $truth_words[$api_id];
            $words_count = count($truth_row);
            $match_count = count($match_row);
            //一番多いヒット数を下回ったら終了（設定ON時）
            if ($match_count < $max_match_count && config('bot.truth.hint_use_max_match_truth')) break;
            //「いいえ」を選んだワードがあったらスキップ
            if ($this->findNoWords($truth_row)) continue;
            if ($match_count === $words_count && $words_count === $input_words_count && $match_count === $max_match_count) {
                //マッチ数=ワード数=入力ワード数（完全一致）　優先度：高
                $perfect_match[$api_id] = $truth_row;
            } else {
                $normal_match[$api_id] = $truth_row;
            }
        }

        return [
            'perfect' => $perfect_match,
            'normal' => $normal_match,
        ];
    }

    /**
     * すべて同じ真理表かどうか
     * @param array $many_match_truth マッチした真理表
     * @return bool
     */
    private function isAllEqual(array $many_match_truth)
    {
        if (count($many_match_truth) <= 1) return false;
        $all_eq = true;
        $base = array_column(current($many_match_truth), 'word');
        while ($row = next($many_match_truth)) {
            if (!array_eq($base, array_column($row, 'word'))) {
                $all_eq = false;
                break;
            }
        }
        return $all_eq;
    }

    /**
     * 絞り込めたか
     * @param array $many_match_truth マッチした真理表
     * @return bool
     */
    private function isRefined(array $many_match_truth)
    {
        if (count($many_match_truth) === 1) {
            return true;
        }
        if (count($this->yes_words) + count($this->no_words) >= config('bot.truth.hear_back_cnt')) {
            return true;
        }
        return false;
    }

    /**
     * NOワードと一致
     * @param array $truth_row 真理表（行）
     * @return bool
     */
    private function findNoWords(array $truth_row)
    {
        foreach ($this->no_words as $no_word) {
            if (in_array($no_word, $truth_row['replace_word'])) return true;
        }
        return false;
    }

    /**
     * 候補キーフレーズを検索
     * @param array $input_words キーフレーズ
     * @param array $refine_truth_words 絞り込み済み真理表
     * @param array $match_words マッチしたキーフレーズがある真理表
     * @param float $priority_rate プライオリティの重み
     * @return array
     */
    private function searchHintWord(array $input_words, array $refine_truth_words, array $match_words, float $priority_rate = 1)
    {
        $input_word_count = count($input_words);
        $hint_words = $perfect_ids = $truth_ary = $max_priority_key_phrase_ary = $api_priority_ary = $match_key_phrase_count_ary = $truth_count_ary = [];
        $refine_truth_count = count($refine_truth_words);
        //キーフレーズ毎にまとめる
        foreach ($refine_truth_words as $api_id => $truth_rows) {
            $api_priority_ary[$api_id] = $max_priority = 0;
            $truth_count_ary[$api_id] = count($truth_rows);
            foreach ($truth_rows as $truth_id => $truth_row) {
                $is_match_word = in_array($truth_id, array_keys($match_words[$api_id]));
                $key_phrase_id = $truth_row['key_phrase_id'];
                if ($is_match_word) {
                    //マッチしたワードのプライオリティを加算
                    $api_priority_ary[$api_id] += $truth_row['priority'] * $priority_rate;
                    $truth_count_ary[$api_id]--;
                } else {
                    //キーフレーズ毎のヒットしたQA数をカウント
                    if (!isset($match_key_phrase_count_ary[$key_phrase_id])) $match_key_phrase_count_ary[$key_phrase_id] = 0;
                    $match_key_phrase_count_ary[$key_phrase_id]++;
                    //QA中プライオリティが最大のキーフレーズを抽出
                    if ($max_priority < $truth_row['priority']) {
                        $max_priority_key_phrase_ary[$api_id] = $truth_row;
                        $max_priority = $truth_row['priority'];
                    }
                }
            }
        }
        //完全一致を抽出
        //入手ワードがすべて入力ワードの場合（置換がある場合、このパターンに引っかかる可能性有）
        foreach ($truth_count_ary as $api_id => $count) {
            if ($count != 0 || count($match_words[$api_id]) != count($refine_truth_words[$api_id]) || count($match_words[$api_id]) != $input_word_count) continue;
            $perfect_ids[] = $api_id;
        }
        //候補ワードがなくなった場合
        foreach ($match_key_phrase_count_ary as $key_phrase_id => $count) {
            if ($count != $refine_truth_count) continue;
            foreach ($truth_count_ary as $api_id => $word_cnt) {
                $truth_count_ary[$api_id]--;
                if ($truth_count_ary[$api_id] != 0) continue;
                if ($input_word_count != count($match_words[$api_id])) continue;
                //候補ワードが無くなったので完全一致
                $perfect_ids[] = $api_id;
                unset($api_priority_ary[$api_id]);
            }
        }
        //置換前後のキーフレーズすべてが入力キーフレーズと一致する場合
        foreach ($match_words as $api_id => $match_rows) {
            if (in_array($api_id, $perfect_ids)) continue;
            $kp_match_count = 0;
            $match_rows_count = count($match_rows);
            $truth_rows = $refine_truth_words[$api_id] ?? [];
            if ($match_rows_count != count($truth_rows)) continue;
            foreach ($match_rows as $truth_id => $match_row) {
                if (!in_array($match_row['word'], $input_words) && !in_array($match_row['replace_word'], $input_words)) continue;
                $kp_match_count++;
            }
            if ($match_rows_count != $kp_match_count) continue;
            $perfect_ids[] = $api_id;
        }
        //選択したキーフレーズのプライオリティの降順で処理
        arsort($api_priority_ary);
        foreach ($api_priority_ary as $api_id => $priority) {
            if (!isset($max_priority_key_phrase_ary[$api_id])) continue;
            $key_phrase_row = $max_priority_key_phrase_ary[$api_id];
            $hint_words[$key_phrase_row['key_phrase_id']][$api_id] = $api_priority_ary[$api_id];
        }
        //同一のAPI_IDを持つものは除外
        $exclusion_words = [];
        foreach ($hint_words as $key_phrase_id => $api_ids) {
            if (isset($exclusion_words[$key_phrase_id])) continue;
            foreach ($hint_words as $key_phrase_id2 => $api_ids2) {
                if ($key_phrase_id === $key_phrase_id2) continue;
                if (!array_eq(array_keys($api_ids), array_keys($api_ids2))) continue;
                $exclusion_words[$key_phrase_id2] = 1;
                unset($hint_words[$key_phrase_id2]);
            }
        }
        //候補ワードが一つになったときは絞り込んだことにする
        $refine = false;
        if (count($hint_words) === 1) {
            $refine = true;
            $perfect_ids = array_merge($perfect_ids, array_keys(current($hint_words)));
            $hint_words = [];
        }
        if (empty($hint_words) && !empty($perfect_ids)) $refine = true;
        return [
            'hint_words' => $hint_words,
            'refine' => $refine,
            'perfect' => $perfect_ids,
        ];
    }

    /**
     * 候補ワードをマージ
     * @param array $hint_words_ary
     * @return array
     */
    private function mergeHintWords(array $hint_words_ary)
    {
        $max_key_phrase_priority_ary = $result = [];
        foreach ($hint_words_ary as $idx => $hint_words) {
            foreach ($hint_words as $key_phrase_id => $api_ids) {
                $priority = current($api_ids);
                if (!isset($max_key_phrase_priority_ary[$key_phrase_id]) || $max_key_phrase_priority_ary[$key_phrase_id] < $priority) {
                    $result[$key_phrase_id] = $priority;
                    $max_key_phrase_priority_ary[$key_phrase_id] = $priority;
                }
            }
        }
        arsort($result);
        return array_keys($result);
    }

    /**
     * 入力メッセージをコンバート
     */
    private function convertMessage()
    {
        $morph_service = $this->getBotTruthService()->getMorphService();
        //入力ワードをキーフレーズに分解
        $words = $morph_service->setMessage($this->message)->execMorph()->getWords();
        //TODO:入力キーフレーズは↑
        //検索用キーフレーズ生成（置換前のキーフレーズ＋それを分かち書きしたもの）
        $search_words = config('bot.truth.add_before_synonym_key_phrase') ? $morph_service->setMessage($this->message)->execMorph(true, false)->getWords() : [];
        list(, , $morph_search_words) = $morph_service->getMorphBaseService()->morphMessageProperNoun(implode($search_words));
        $search_words = array_values(array_unique(array_merge($search_words, array_column($morph_search_words, 'word'))));
        //入力キーフレーズを分かち書きしたものを取得（名詞のみ）
        $morph_input_words = [];
        $diff_exists_count = 0;
        foreach ($words as $word) {
            list(, , $morph_input_word) = $morph_service->getMorphBaseService()->morphMessageProperNoun($word);
            $diff_exists = false;
            foreach ($morph_input_word as $row) {
                //名詞以外が含まれる場合、元キーフレーズにする
                if ($row['pos'] != config('bot.morph.default.keywords.noun')) {
                    $morph_input_words[] = $word;
                    continue 2;
                }
                if (!in_array($row['word'], $words)) $diff_exists = true;
            }
            if (!$diff_exists) $diff_exists_count++;
            $morph_input_words = array_merge($morph_input_words, array_column($morph_input_word, 'word'));
        }
        //分かち書きしたものが入力キーフレーズを内包していたらやらない。
        if ($diff_exists_count == count($words)) {
            $morph_input_words = [];
        }
        //検査機用キーフレーズがすべて入力キーフレーズに入っていたらやらない
        $search_in_all = true;
        foreach ($search_words as $word) {
            if (in_array($word, $words) || in_array($word, $morph_input_words)) continue;
            $search_in_all = false;
        }
        if ($search_in_all) $search_words = [];
        $this->input_words = $words;
        $this->input_morph_words = $morph_input_words;
        $this->search_words = $search_words;
    }

    /**
     * 真理表サービス取得
     * @return BotTruthService
     */
    public function getBotTruthService()
    {
        return $this->bot_truth_service;
    }

    /**
     * 真理表サービスセット
     * @param mixed $bot_truth_service
     * @return $this
     */
    public function setBotTruthService($bot_truth_service)
    {
        $this->bot_truth_service = $bot_truth_service;
        return $this;
    }

    /**
     * チャットボット基本サービス取得
     * @return BotBaseService
     */
    public function getBotBaseService()
    {
        return $this->bot_base_service;
    }

    /**
     * チャットボット基本サービスセット
     * @param mixed $bot_base_service
     * @return $this
     */
    public function setBotBaseService($bot_base_service)
    {
        $this->bot_base_service = $bot_base_service;
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
        $this->convertMessage();
        return $this;
    }

    /**
     * 入力ワード（キーフレーズ）取得
     * @return array
     */
    public function getInputWords(): array
    {
        return $this->input_words;
    }

    /**
     * 入力ワード（キーフレーズ）セット
     * @param array $input_words
     * @return $this
     */
    public function setInputWords(array $input_words)
    {
        $this->input_words = $input_words;
        return $this;
    }

    /**
     * 分かち書きした入力ワードセット
     * @param array $input_morph_words
     * @return $this
     */
    public function setInputMorphWords(array $input_morph_words)
    {
        $this->input_morph_words = $input_morph_words;
        return $this;
    }

    /**
     * 分かち書きした入力ワード取得
     * @return array
     */
    public function getInputMorphWords(): array
    {
        return $this->input_morph_words;
    }

    /**
     * YESワード取得
     * @return array
     */
    public function getYesWords(): array
    {
        return $this->yes_words;
    }

    /**
     * YESワードセット
     * @param array $yes_words
     * @return $this
     */
    public function setYesWords(array $yes_words)
    {
        $this->yes_words = $yes_words;
        return $this;
    }

    /**
     * NOワード取得
     * @return array
     */
    public function getNoWords(): array
    {
        return $this->no_words;
    }

    /**
     * NOワードセット
     * @param array $no_words
     * @return $this
     */
    public function setNoWords(array $no_words)
    {
        $this->no_words = $no_words;
        return $this;
    }

    /**
     * 検索ワードセット
     * @param array $search_words
     * @return $this
     */
    public function setSearchWords(array $search_words)
    {
        $this->search_words = $search_words;
        return $this;
    }

    /**
     * 検索ワード取得
     * @return array
     */
    public function getSearchWords(): array
    {
        return $this->search_words;
    }

    /**
     * 結果取得
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * 候補ワードオフセット設定
     * @param int $hint_offset
     * @return $this
     */
    public function setHintOffset(int $hint_offset)
    {
        $this->hint_offset = $hint_offset;
        return $this;
    }

    /**
     * マッチングに使用したキーフレーズを取得
     * @return array
     */
    public function getMatchUseWords(): array
    {
        return $this->match_use_words;
    }

    /**
     * 候補ワードオフセット取得
     * @return int
     */
    public function getHintOffset(): int
    {
        return $this->hint_offset;
    }

    /**
     * 除外API_IDを設定
     * @param array $ignore_api_ids
     * @return $this
     */
    public function setIgnoreApiIds(array $ignore_api_ids)
    {
        $this->ignore_api_ids = $ignore_api_ids;
        return $this;
    }
}
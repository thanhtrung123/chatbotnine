<?php


namespace App\Services\Bot;


use App\Services\Bot\Morph\MorphInterface;
use App\Services\Bot\Morph\MorphResult;
use App\Services\Bot\Morph\MorphService;
use Util;

/**
 * チャットボット用形態素解析基本サービス
 * Class MorphBaseService
 * @package App\Services\Bot
 */
class MorphBaseService
{
    /**
     * @var string 文章
     */
    private $message;
    /**
     * @var array 類義語変換表
     */
    private $synonyms = [];
    /**
     * @var array 固有名詞表
     */
    private $proper_nouns = [];
    /**
     * @var array キーフレーズ
     */
    private $key_phrases = [];
    /**
     * @var array 置換ID配列
     */
    private $replace_ids = [];
    /**
     * @var array 否定結合スタック
     */
    private $negative_join_stack = [];

    /* @var MorphInterface */
    private $morph_service;
    /**
     * @var BotDbService
     */
    private $bot_db_service;

    /**
     * MorphBaseService constructor.
     * @param MorphService $morph_service
     * @param BotDbService $bot_db_service
     * @throws \Exception
     */
    public function __construct(MorphService $morph_service, BotDbService $bot_db_service)
    {
        $this->bot_db_service = $bot_db_service;
        //Morph
        Util::overrideConfig('bot.morph.' . config('bot.morph.use'), 'bot.morph.default');
        $this->morph_service = $morph_service->getService(config('bot.morph.default.service'));
        $this->morph_service->setSetting(config('bot.morph.default'));
    }

    /**
     * 分かち書き
     * @param string $keyword 文章
     * @param string $glue 結合文字
     * @return array [string 表層形分かち書き, string 原型分かち書き, array 品詞等]
     */
    public function morphKeyword($keyword, $glue = ' ')
    {
        $join_keyword = $original_keyword = $result = [];
        $gen = $this->morph_service->setMessage($keyword)->execute()->getResult();
        /** @var MorphResult $row * */
        foreach ($gen as $row) {
            $result[] = [
                'word' => $row->getSurfaceForm(),
                'original' => $row->getOriginalForm(),
                'pos' => $row->getPos(),
            ];
            $join_keyword[] = $row->getSurfaceForm();
            $original_keyword[] = $row->getOriginalForm();
        }
        return [implode($glue, $join_keyword), implode($glue, $original_keyword), $result];
    }

    /**
     * 辞書（異表記、類語）を使用して入力文書を置換
     * @param $use_variant
     * @param $use_synonym
     * @return $this
     */
    public function replaceMessageUseDictionary($use_variant, $use_synonym)
    {
        $rep_idx = 0;
        $this->replace_ids = ['variant' => [], 'synonym' => [], 'proper_noun' => []];
        // 異表記
        if ($use_variant) {
            $this->message = $this->convertMessageToVariant($this->message);
        }
        // 類義語
        if ($use_synonym) {
            // 半角スペースはシステムで使うので全角にする
            $this->message = str_replace(' ', '　', $this->message);
            // 類義語置き換え処理
            $synonym_result = $this->convertMessageToSynonym($this->message, $rep_idx);
            $this->synonyms = $synonym_result['synonyms'];
            $this->message = str_replace(' ', '', $synonym_result['message']);
            // 置き換えた全角スペースを元に戻す
            $this->message = str_replace('　', ' ', $this->message);
        }
        return $this;
    }

    /**
     * メッセージ内の特殊置換文字を置換
     * @return $this
     */
    public function replaceMessageUseSpecialReplaceWord()
    {
        foreach ($this->proper_nouns as $key => $proper_noun) {
            $this->message = str_replace($key, $proper_noun, $this->message);
        }
        foreach ($this->synonyms as $key => $synonym) {
            $this->message = str_replace($key, $synonym, $this->message);
        }
        return $this;
    }

    /**
     * 文字列が特殊置換文字の場合、変換表を使用して置換
     * @param $word
     * @return mixed
     */
    private function replaceSpecialReplaceWord($word)
    {
        if (isset($this->proper_nouns[$word])) return $this->proper_nouns[$word];
        if (isset($this->synonyms[$word])) return $this->synonyms[$word];
        return $word;
    }

    /**
     * キーフレーズ抽出
     * @param bool $replace_synonyms 類義語置換をする
     * @return array
     */
    public function makeKeyPhrase($replace_synonyms = true)
    {
        $this->makeApiMorphKeyword(true, $replace_synonyms);
        return $this->key_phrases;
    }

    /**
     * API用形態素解析キーワード抽出
     * @param bool $key_phrase_flag キーフレーズ抽出フラグ
     * @param bool $use_special_replace_word 特殊置換文字を置換をする
     * @return string
     */
    public function makeApiMorphKeyword($key_phrase_flag = false, $use_special_replace_word = true)
    {
        $morph_result_ary = $this->getMorphService()->setMessage($this->message)->execute()->getResult();
        $words = [];
        $word_idx = 0;

        for ($idx = 0; $idx < count($morph_result_ary); $idx++) {
            $morph_result = $morph_result_ary[$idx];
            if ($this->isUndefined($morph_result)) {
                if (!empty($words[$word_idx])) $word_idx++;
                continue;
            }
            $morph_result_next = $morph_result_ary[$idx + 1] ?? null;
            $morph_result_prev = $morph_result_ary[$idx - 1] ?? null;
            switch ($morph_result->getPos()) {
                case config('bot.morph.default.keywords.adjective')://形容詞
                    if (!$key_phrase_flag && $this->negativeAdjectiveCheck($morph_result_ary, $idx)) {
                        $words[$word_idx++][] = $morph_result->getSurfaceForm() . last($this->negative_join_stack);
                        $idx += count($this->negative_join_stack);
                    } else {
                        if (!$this->inPos($morph_result_next, true, !$key_phrase_flag)) break; //+(動詞or名詞)じゃない
                        $words[$word_idx][] = $morph_result->getSurfaceForm();
                    }
                    break;
                case config('bot.morph.default.keywords.prefix')://接頭詞
                case config('bot.morph.default.keywords.adverb')://副詞
                    if (!$this->inPos($morph_result_next, true, !$key_phrase_flag)) break; //+(動詞or名詞)じゃない
                    $words[$word_idx][] = $morph_result->getSurfaceForm();
                    break;
                case config('bot.morph.default.keywords.noun')://名詞
                    if ((empty($words[$word_idx]) || !$this->inPos($morph_result_prev, true)) && $this->isNonautonomy($morph_result)) break;//非自立
                    if (!$key_phrase_flag && $this->negativeNounCheck($morph_result_ary, $idx)) {
                        $words[$word_idx++][] = $morph_result->getSurfaceForm() . last($this->negative_join_stack);
                        $idx += count($this->negative_join_stack);
                    } else {
                        $words[$word_idx][] = $morph_result->getSurfaceForm();
                    }
                    if (!$this->inPos($morph_result_next, true)) $word_idx++;
                    break;
                case config('bot.morph.default.keywords.verb')://動詞
                    if ($key_phrase_flag) {
                        $word_idx++;
                        break;
                    }
                    if ($this->inPos($morph_result_next, false, true)) { //連続した動詞
                        $words[$word_idx][] = $morph_result->getSurfaceForm();
                        break;
                    }
                    //非定かどうか
                    if ($this->negativeVerbCheck($morph_result_ary, $idx)) {
                        $words[$word_idx][] = $this->convertVerbToNegative($morph_result);
                    } else {
                        $words[$word_idx][] = $morph_result->getOriginalForm();
                    }
                    $word_idx++;
                    break;
                default:
                    $word_idx++;
                    break;
            }
        }
        $key_words = [];
        foreach ($words as $word_ary) {
            $key_phrase = implode($word_ary);
            if ($use_special_replace_word) $key_phrase = $this->replaceSpecialReplaceWord($key_phrase);
            $key_words[] = $key_phrase;
        }
        $this->key_phrases = array_count_values($key_words);

        return implode(' ', $key_words);
    }

    /**
     * 品詞チェック（対象の品詞かどうか）
     * @param MorphResult $morph_result
     * @param bool $noun 名詞
     * @param bool $verb 動詞
     * @param bool $adjective 形容詞
     * @return bool
     */
    private function inPos($morph_result, $noun = false, $verb = false, $adjective = false)
    {
        if (!$morph_result) return false;
        $result = [];
        if ($noun) {
            $result[] = $morph_result->getPos() == config('bot.morph.default.keywords.noun');
        }
        if ($verb) {
            $result[] = $morph_result->getPos() == config('bot.morph.default.keywords.verb');
        }
        if ($adjective) {
            $result[] = $morph_result->getPos() == config('bot.morph.default.keywords.adjective');
        }
        $ret = false;
        foreach ($result as $flg) {
            if ($flg) $ret = true;
        }
        return $ret;
    }

    /**
     * 非自立か
     * @param MorphResult $morph_result
     * @return bool
     */
    public function isNonautonomy(MorphResult $morph_result)
    {
        return in_array(config('bot.morph.default.keywords.nonautonomy'), $morph_result->getPosSelection());
    }

    /**
     * 未定義か
     * @param MorphResult $morph_result
     * @return bool
     */
    public function isUndefined(MorphResult $morph_result)
    {
        return preg_match('/' . preg_quote(config('bot.morph.default.keywords.undefined'), '/') . '$/', $morph_result->getInputStr());
    }

    /**
     * 否定形の動詞かチェック
     * @param MorphResult[] $morph_result_ary
     * @param integer $morph_idx インデックス
     * @return bool
     */
    private function negativeVerbCheck($morph_result_ary, $morph_idx)
    {
        $is_not = false;
        $is_masu = false;
        $is_sp_not = false;
        $this->negative_join_stack = [];
        for ($idx = $morph_idx + 1; $idx < count($morph_result_ary); $idx++) {
            $morph_result = $morph_result_ary[$idx];
            switch ($morph_result->getPos()) {
                case config('bot.morph.default.keywords.prefix')://接頭詞
                case config('bot.morph.default.keywords.noun')://名詞
                case config('bot.morph.default.keywords.verb')://動詞
                    break 2;
                case config('bot.morph.default.keywords.auxiliary_verb')://助動詞
                    if ($morph_result->getConjugatedForm() == config('bot.morph.default.keywords.sp_not')) {//特殊・ナイ
                        if ($morph_result->getInflection() == config('bot.morph.default.keywords.standard')) {
                            $is_not = true;
                        } else {
                            $is_sp_not = true;
                        }
                    } elseif ($morph_result->getConjugatedForm() == config('bot.morph.default.keywords.sp_nu')) {
                        $is_not = true;
                    } elseif ($morph_result->getConjugatedForm() == config('bot.morph.default.keywords.sp_ta')) {
                        if ($morph_result->getInflection() == config('bot.morph.default.keywords.standard') && $is_sp_not) {
                            $is_not = true;
                        }
                    }
                    if ($is_masu && $morph_result->getConjugatedForm() == config('bot.morph.default.keywords.no_change')) {
                        $is_not = true;
                    }
                    if ($morph_result->getConjugatedForm() == config('bot.morph.default.keywords.sp_masu')) {
                        $is_masu = true;
                    }
                    break;
                default:
                    break;
            }
            $this->negative_join_stack[] = $morph_result->getSurfaceForm();
            if ($is_not) break;
        }
        return $is_not;
    }

    /**
     * 否定形名詞か
     * @param MorphResult[] $morph_result_ary
     * @param integer $morph_idx インデックス
     * @return bool
     */
    private function negativeNounCheck($morph_result_ary, $morph_idx)
    {
        $is_not = false;
        $this->negative_join_stack = [];
        for ($idx = $morph_idx + 1; $idx < count($morph_result_ary); $idx++) {
            $morph_result = $morph_result_ary[$idx];
            switch ($morph_result->getPos()) {
                case config('bot.morph.default.keywords.prefix')://接頭詞
                case config('bot.morph.default.keywords.adverb')://副詞
                case config('bot.morph.default.keywords.noun')://名詞
                case config('bot.morph.default.keywords.verb')://動詞
                    break 2;
                case config('bot.morph.default.keywords.auxiliary_verb')://助動詞
                    if ($morph_result->getConjugatedForm() == config('bot.morph.default.keywords.sp_not')) {
                        if ($morph_result->getInflection() != config('bot.morph.default.keywords.standard')) break;
                        $is_not = true;
                    }
                    break;
                case config('bot.morph.default.keywords.adjective')://形容詞
                    if ($morph_result->getOriginalForm() == 'ない') {
                        $is_not = true;
                    }
                    break;
            }
            $this->negative_join_stack[] = $is_not ? $morph_result->getOriginalForm() : $morph_result->getSurfaceForm();
            if ($is_not) break;
        }
        return $is_not;
    }

    /**
     * 否定形形容詞か
     * @param MorphResult[] $morph_result_ary
     * @param integer $morph_idx インデックス
     * @return bool
     */
    private function negativeAdjectiveCheck($morph_result_ary, $morph_idx)
    {
        $is_not = false;
        $this->negative_join_stack = [];
        for ($idx = $morph_idx + 1; $idx < count($morph_result_ary); $idx++) {
            $morph_result = $morph_result_ary[$idx];
            switch ($morph_result->getPos()) {
                case config('bot.morph.default.keywords.prefix')://接頭詞
                case config('bot.morph.default.keywords.adverb')://副詞
                case config('bot.morph.default.keywords.adjective')://形容詞
                case config('bot.morph.default.keywords.noun')://名詞
                case config('bot.morph.default.keywords.verb')://動詞
                    break 2;
                case config('bot.morph.default.keywords.auxiliary_verb')://助動詞
                    if ($morph_result->getConjugatedForm() == config('bot.morph.default.keywords.sp_not')) {
                        if ($morph_result->getInflection() != config('bot.morph.default.keywords.standard')) break;
                        $is_not = true;
                    }
                    break;
            }
            $this->negative_join_stack[] = $is_not ? $morph_result->getOriginalForm() : $morph_result->getSurfaceForm();
            if ($is_not) break;
        }
        return $is_not;
    }

    /**
     * 動詞を否定形に変換
     * @param $morph_result MorphResult
     * @return string
     */
    private function convertVerbToNegative($morph_result)
    {
        if ($morph_result->getOriginalForm() == 'ある') {
            $verb = '';
        } else if ($morph_result->getConjugatedForm() == config('bot.morph.default.keywords.sa_do')) {
            $verb = 'し';
        } else if (preg_match('/(.)行/u', $morph_result->getConjugatedForm(), $match)) {
            $verb_not_con = mb_convert_kana($match[1], 'c');
            $verb_orig = mb_substr($morph_result->getOriginalForm(), 0, mb_strlen($morph_result->getOriginalForm()) - 1);
            $verb = $verb_orig . $verb_not_con;
        } else {
            $verb = preg_replace('/る$/', '', $morph_result->getOriginalForm());
        }
        return $verb . 'ない';
    }

    /**
     * 異表記変換
     * @param string $message メッセージ
     * @return string $message
     */
    private function convertMessageToVariant($message)
    {
        if (!config('bot.morph.variant_process_enabled')) return $message;
        $variant_gen = $this->bot_db_service->getVariantRepository()->findByMessage($message)->getGenerator();
        foreach ($variant_gen as $row) {
            $this->replace_ids['variant'][] = $row['id'];
            $short_text = $long_text = '';
            $reverse_flg = FALSE;
            // 変更後（noun_text）の文字列が変更前（noun_variant_text）の文字列と同じか短い場合
            if (mb_strlen($row['noun_text']) <= mb_strlen($row['noun_variant_text'])) {
                $short_text = $row['noun_text'];
                $long_text = $row['noun_variant_text'];
            }
            // 変更前の文字列が短い場合
            else {
                $short_text = $row['noun_variant_text'];
                $long_text = $row['noun_text'];
                $reverse_flg = TRUE;
            }
            // 長い文字列を短い文字列で置き換えをおこなう
            $message = str_ireplace($long_text, $short_text, $message);
            // 変更前の文字列に置き換えた場合は、変更後の文字列に置き換えをおこなう
            if ($reverse_flg == TRUE) {
                $message = str_ireplace($row['noun_variant_text'], $row['noun_text'], $message);
            }
        }
        return $message;
    }

    /**
     * 類義語変換（メイン）
     * @param string $message 文章
     * @param int $rep_idx 置換インデックス
     * @return array [message=>変換後メッセージ, synonyms=>類義語変換表, rep_idx=>置換インデックス]
     */
    private function convertMessageToSynonym($message, $rep_idx = 0)
    {
        if (!config('bot.morph.thesaurus_process_enabled')) return ['message' => $message, 'synonyms' => []];
        //類義語辞書にある"名詞以外がある置換前ワード"の中にスペースが入らないように抜き出す
        list($tmp_message, $tmp_synonyms, $rep_idx) = $this->convertSynonyms($message, $rep_idx, true);
        //↑で引っかからなかったものは連続した名詞にスペースを入れる
        $tmp_message = $this->splitNouns(str_replace(' ', '', $tmp_message));
        //類義語置換
        list($message, $synonyms) = $this->convertSynonyms($tmp_message, $rep_idx);
        return ['message' => $message, 'synonyms' => array_merge($tmp_synonyms, $synonyms), 'rep_idx' => $rep_idx];
    }

    /**
     * 類義語変換処理
     * @param $message
     * @param int $rep_idx
     * @param bool $only_include_not_noun
     * @return array
     */
    private function convertSynonyms($message, $rep_idx = 0, $only_include_not_noun = false)
    {
        $synonyms = [];
        //分かち書き処理
        list($message, $original_keyword, $morph_result) = $this->morphMessageProperNoun($message);
        //SQLで類義語をひっかけてループする
        $synonym_gen = $this->bot_db_service->getSynonymRepository()->findByMessage($message, $original_keyword)->getGenerator();
        foreach ($synonym_gen as $idx => $row) {
            if ($only_include_not_noun && count(explode(' ', $this->splitNouns($row['keyword']))) <= 1) continue;
            list($morph_keyword, , $morph_word_result) = $this->morphMessageProperNoun($row['keyword']);
            if ($this->isFindVerb($morph_word_result)) {
                //動詞が含まれる
                $word_count = count($morph_word_result);
                $word_stack = [];
                for ($i = 0; $i < count($morph_result); $i++) {
                    $count = 0;
                    for ($j = 0; $j < $word_count; $j++) {
                        if (!isset($morph_result[$i + $j])) continue 2;
                        if ($morph_result[$i + $j]['original'] == $morph_word_result[$j]['original']) {
                            $count++;
                        }
                        $word_stack[$j] = $morph_result[$i + $j]['word'];
                    }
                    if ($count == $word_count) {
                        $morph_keyword = implode(' ', $word_stack);
                    }
                }
            }
            list($synonym_morph, ,) = $this->morphMessageProperNoun($row['synonym']);
            //置換対象ワードをREPXREP(システム用置換文字)に変換
            $tmp_synonym = $row['synonym'];
            list($key, $row['synonym']) = $this->getSpecialReplaceWord($rep_idx);
            // MEMO:置き換え前後の対象が同一箇所では無い場合は置き換えをおこなう
            $tmp_message = $message;
            $offset = 0;
            while (preg_match('/(^|\s)' . preg_quote($synonym_morph) . '($|\s)/i', $message, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                preg_match('/(^|\s)' . preg_quote($morph_keyword) . '($|\s)/i', $message, $matches2, PREG_OFFSET_CAPTURE, $offset);
                if (empty($matches2) || $matches[0][1] < $matches2[0][1] || $matches2[0][1] + strlen($morph_keyword) < $matches[0][1]) {
                    $message = substr_replace($message, $morph_keyword, $matches[0][1] + strlen($matches[1][0]), strlen($synonym_morph));
                    $offset = $matches[0][1] + strlen($morph_keyword);
                    continue;
                }
                $offset = $matches[0][1] + 1;
            }
            $message = preg_replace('/(^|\s)' . preg_quote($morph_keyword) . '($|\s)/i', '${1}' . $row['synonym'] . '${2}', $message);
            if ($tmp_message !== $message) {
                $this->replace_ids['synonym'][] = $row['id'];
                $synonyms[$key] = $tmp_synonym;
                $rep_idx++;
            }
        }
        return [$message, $synonyms, $rep_idx];
    }

    /**
     * 固有名詞を考慮して形態素解析をおこなう
     * @param $message 文章
     * @param string $glue 結合文字
     * @return array [string 表層形分かち書き, string 原型分かち書き, array 品詞等]
     */
    public function morphMessageProperNoun($message, $glue = ' ') {
        // 固有名詞機能を使わない場合は分かち書きした結果をそのままリターン
        if (!config('bot.morph.proper_noun_process_enabled')) {
            return $this->morphKeyword($message, $glue);
        }
        $proper_nouns = [];
        // 入力文書を分かち書きする
        list(, , $msg_pos_ary) = $this->morphKeyword($message);
        // 文章中に固有名詞が含まれる部分を検索
        $proper_noun_gen = $this->bot_db_service->getProperNounRepository()->findByMessage($message)->getGenerator();
        // 抽出されてきた固有名詞の数分ループ
        foreach ($proper_noun_gen as $row) {
            // 固有名詞として抽出したワードを分かち書きする
            list(, , $word_pos_ary) = $this->morphKeyword($row['word']);
            // （分かち書きした）先頭の品詞が名詞以外、または先頭が接頭辞であり次の品詞が名詞以外の場合はcontinueする
            if ($word_pos_ary[0]['pos'] != config('bot.morph.default.keywords.noun') && ($word_pos_ary[0]['pos'] == config('bot.morph.default.keywords.prefix') && $word_pos_ary[1]['pos'] != config('bot.morph.default.keywords.noun'))) {
                continue;
            }
            // （分かち書きした）最後の品詞が名詞以外、または最後が接尾辞であり前の品詞が名詞以外の場合はcontinueする
            if (last($word_pos_ary)['pos'] != config('bot.morph.default.keywords.noun') && (last($word_pos_ary)['pos'] == config('bot.morph.default.keywords.suffix') && $word_pos_ary[count($word_pos_ary) - 2]['pos'] != config('bot.morph.default.keywords.noun'))) {
                continue;
            }
            //FIXME:引っかかった固有名詞の前の単語の品詞をチェック（どうする？）
            //方法1：あらかじめ全体を分解しておき、あとで当てはめる（注：一致しない可能性有）
            $msg_idx = 0;
            $prev_word_pos_ary = [];
            foreach ($msg_pos_ary as $mp_idx => $msg_pos) {
                $msg_word_len = mb_strlen($msg_pos['word']);
                $msg_idx += $msg_word_len;
                if (strcasecmp($msg_pos['word'], $word_pos_ary[0]['word']) != 0) continue;
                for ($i = 0; $i < count($word_pos_ary); $i++) {
                    if (!isset($msg_pos_ary[$mp_idx + $i]) || strcasecmp($msg_pos_ary[$mp_idx + $i]['word'], $word_pos_ary[$i]['word']) != 0) break;
                }
                if ($i == count($word_pos_ary)) {
                    $prev_word_pos_ary[$msg_idx - $msg_word_len] = $msg_pos_ary[$mp_idx - 1]['pos'] ?? '';
                }
            }
            //方法2：引っかかった固有名詞の前にある文章を分解して末尾の品詞をチェック（注：固有名詞とくっついて意味合いが変化する場合、別の品詞になる可能性有）
            $word_len = mb_strlen($row['word']);
            $msg_idx = 0;
            $split_message_by_word = explode($row['word'], $message);
            for ($i = 0; $i < count($split_message_by_word) - 1; $i++) {
                $prev_word = $split_message_by_word[$i];
                $msg_idx += mb_strlen($prev_word) + $word_len;
                if (isset($prev_word_pos_ary[$msg_idx - $word_len])) {
                    continue;
                }
                list(, , $prev_msg_pos_ary) = $this->morphKeyword($prev_word);
                $prev_word_pos_ary[$msg_idx - $word_len] = last($prev_msg_pos_ary)['pos'];
            }
            // 固有名詞置換処理
            $proper_nouns[$row['id']] = ['org_word' => $row['word'], 'rep_word' => 'PNREP' . number_to_alpha($row['id']) . 'PNREP'];
            // 場所を考慮して置換
            foreach ($prev_word_pos_ary as $m_idx => $pos) {
                if ($pos == config('bot.morph.default.keywords.noun')) {
                    continue;
                }
                $rep_message = mb_substr($message, $m_idx);
                $rep_message = preg_replace('/' . preg_quote($proper_nouns[$row['id']]['org_word'], '/') . '/i', $proper_nouns[$row['id']]['rep_word'], $rep_message, 1);
                $message = mb_substr($message, 0, $m_idx) . $rep_message;
            }
        }
        // 形態素解析をおこなう
        $morph_message = $this->morphKeyword($message, $glue);
        // 固有名詞で置き換えたものを元に戻す
        foreach ($proper_nouns as $nouns) {
            $morph_message[0] = str_replace($nouns['rep_word'], $nouns['org_word'], $morph_message[0]);
            $morph_message[1] = str_replace($nouns['rep_word'], $nouns['org_word'], $morph_message[1]);
            foreach ($morph_message[2] as $id => $row) {
                if (strpos($row['word'], $nouns['rep_word']) !== FALSE) {
                    $morph_message[2][$id]['word'] = str_replace($nouns['rep_word'], $nouns['org_word'], $row['word']);
                    $morph_message[2][$id]['original'] = (is_null($row['original']) ? $morph_message[2][$id]['word'] : str_replace($nouns['rep_word'], $nouns['org_word'], $row['original']));
                    $morph_message[2][$id]['pos'] = config('bot.morph.default.keywords.noun');
                }
            }
        }
        return $morph_message;
    }

    /**
     * 置換用特殊文字取得
     * @param $rep_idx
     * @return array
     */
    private function getSpecialReplaceWord($rep_idx)
    {
        $alnum = number_to_alpha($rep_idx);
        $key = "REP{$alnum}REP";
        return [$key, "　{$key}　"];
    }

    /**
     * 名詞をスペースで区切る
     * @param string $message 文章
     * @param string $glue 結合文字
     * @return string
     */
    private function splitNouns($message, $glue = ' ')
    {
        $morph_messages = $this->morph_service->setMessage($message)->execute()->getResult();
        $result = $sp_pos = [];
        foreach ($morph_messages as $idx => $morph_message) {
            $result[] = $morph_message->getSurfaceForm();
            //一つあとの単語取得
            $next_morph = $morph_messages[$idx + 1] ?? false;
            if (!$next_morph) continue;
            if (!$this->isConnectNoun($morph_message) && $this->isConnectNoun($next_morph)) {
                //開始にスペース（今が名詞以外で、後が名詞）
                $result[] = $glue;
            } else if ($this->isConnectNoun($morph_message) && !$this->isConnectNoun($next_morph)) {
                //終了にスペース（今が名詞で、後が名詞以外）
                $result[] = $glue;
            }
        }
        return implode($result);
    }

    /**
     * 繋げる名詞か
     * @param MorphResult $morph_result
     * @return bool
     */
    private function isConnectNoun($morph_result)
    {
        return (
            $morph_result->getPos() == config('bot.morph.default.keywords.noun') ||
            in_array(config('bot.morph.default.keywords.noun_connect'), $morph_result->getPosSelection())
        );
    }

    /**
     * 動詞か
     * @param MorphResult $morph_result
     * @return bool
     */
    private function isFindVerb($morph_result)
    {
        $is_verb = false;
        foreach ($morph_result as $row) {
            if ($row['pos'] !== config('bot.morph.default.keywords.verb')) continue;
            $is_verb = true;
            break;
        }
        return $is_verb;
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
     * 類義語置換票取得
     * @return array
     */
    public function getSynonyms(): array
    {
        return $this->synonyms;
    }

    /**
     * 置換済みID取得
     * @return array
     */
    public function getReplaceIds(): array
    {
        return $this->replace_ids;
    }

    /**
     * 形態素解析サービス取得
     * @return MorphInterface
     */
    public function getMorphService(): MorphInterface
    {
        return $this->morph_service;
    }

    /**
     * チャットボットDBサービス取得
     * @return BotDbService
     */
    public function getBotDbService(): BotDbService
    {
        return $this->bot_db_service;
    }

}
<?php

namespace App\Services\Bot\Truth;

use App\Repositories\KeyPhrase\KeyPhraseRepositoryInterface;
use App\Repositories\Truth\TruthRepositoryInterface;
use App\Repositories\StopWords\StopWordsRepositoryInterface;

/**
 * 真理表用DBサービス
 * Class TruthDbService
 * @package App\Services\Bot\Truth
 */
class TruthDbService
{
    /**
     * @var TruthRepositoryInterface
     */
    private $repository_truth;
    /**
     * @var StopWordsRepositoryInterface
     */
    private $repository_stop_words;
    /**
     * @var KeyPhraseRepositoryInterface
     */
    private $repository_key_phrase;

    /**
     * TruthDbService constructor.
     * @param TruthRepositoryInterface $repository_truth
     * @param StopWordsRepositoryInterface $repository_stop_words
     * @param KeyPhraseRepositoryInterface $repository_key_phrase
     */
    public function __construct(TruthRepositoryInterface $repository_truth, StopWordsRepositoryInterface $repository_stop_words, KeyPhraseRepositoryInterface $repository_key_phrase)
    {
        $this->repository_truth = $repository_truth;
        $this->repository_stop_words = $repository_stop_words;
        $this->repository_key_phrase = $repository_key_phrase;
    }

    /**
     * キーフレーズごとの使用数を取得
     * @return array
     */
    public function getWordsCount()
    {
        $word_cnt_ary = [];
        $gen = $this->repository_truth->filterWordCount()->getDbResult()->getGenerator();
        foreach ($gen as $row) {
            $word_cnt_ary[$row['word']] = $row['cnt'];
        }
        return $word_cnt_ary;
    }

    /**
     * ストップワードかどうか
     * @param $word
     * @return bool
     */
    public function isStopWord($word)
    {
        return $this->repository_stop_words->exists($word);
    }

    /**
     * ストップワードセット
     * @param $word
     * @return bool
     */
    public function saveStopWord($word)
    {
        //ワードが既にあったら終了
        if ($this->isStopWord($word)) return false;
        //ワードが真理表で一つだけ使われてたら終了
        if ($this->isOnlyOneWord($word)) return false;
        //ストップワード登録
        $this->repository_stop_words->create([
            'word' => $word,
        ]);
        //MEMO:キーフレーズが使用されていたら削除（真理表とキーフレーズリストから）
        $key_phrase_gen = $this->repository_key_phrase->setParams(['sys_word' => $word])->filterByParams()->getDbResult()->getGenerator();
        foreach ($key_phrase_gen as $key_phrase) {
            $this->repository_truth->setParams(['key_phrase_id' => $key_phrase['id']])->filterByParams()->deleteByQuery();
            $this->repository_key_phrase->deleteOneById($key_phrase['id']);
        }
        return true;
    }

    /**
     * 指定されたワードが真理表内に一つだけ使われているかどうか
     * @param $word
     * @return bool
     */
    private function isOnlyOneWord($word)
    {
        $api_ids_ary = $this->repository_truth->setParams(['word' => $word])->filterWordCountPerApiId()->filterByParams()->getDbResult()->getArray();
        $api_ids = array_column($api_ids_ary, 'api_id');
        $word_count_gen = $this->repository_truth->setParams(['api_ids' => $api_ids])->filterWordCountPerApiId()->filterByParams()->getDbResult()->getGenerator();
        $exists = false;
        foreach ($word_count_gen as $row) {
            if ($row['cnt'] != 1) continue;
            $exists = true;
            break;
        }
        return $exists;
    }

    /**
     * 真理表保存
     * @param $api_id
     * @param $word_count_ary
     * @return $this
     */
    public function saveTruthTable($api_id, $word_count_ary)
    {
        $truth_data = $this->repository_truth->setParams(['api_id' => $api_id])->filterByParams()->getDbResult()->getPlainArray();
        $truth_data = array_column($truth_data, 'key_phrase_priority', 'key_phrase_id');
        //キーフレーズ削除
        $this->repository_truth->setParams(['api_id' => $api_id])->filterByParams()->deleteByQuery();
        foreach ($word_count_ary as $word => $count) {
            //キーフレーズリスト検索・登録
            $key_phrase_id = $this->repository_key_phrase->findOrSave($word, 0, true);
            //ストップワードは登録しない
            if ($this->isStopWord($word)) continue;
            //登録処理
            $this->repository_truth->create([
                'api_id' => $api_id,
                'key_phrase_id' => $key_phrase_id,
                'key_phrase_priority' => $truth_data[$key_phrase_id] ?? 0,
                'count' => $count,
            ]);
        }
        return $this;
    }

    /**
     * 真理表ワード取得
     * @param array $params
     * @return array
     */
    public function getTruthWords(array $params)
    {
        $match_truth_gen = $this->repository_truth->setParams($params + ['without_delete' => true])->filterByParams()->getDbResult()->getGenerator();
        $result = [];
        foreach ($match_truth_gen as $truth_row) {
            $result[$truth_row['api_id']][$truth_row['truth_id']] = [
                'truth_id' => $truth_row['truth_id'],
                'key_phrase_id' => $truth_row['key_phrase_id'],
                'word' => $truth_row['word'],
                'replace_word' => $truth_row['replace_word'] ?? $truth_row['word'],
                'priority' => $truth_row['key_phrase_priority'] ?? $truth_row['priority'],
            ];
        }
        arsort($result);
        return $result;
    }

    /**
     * 真理表リポジトリ取得
     * @return TruthRepositoryInterface
     */
    public function getRepositoryTruth(): TruthRepositoryInterface
    {
        return $this->repository_truth;
    }

    /**
     * ストップワードリポジトリ取得
     * @return StopWordsRepositoryInterface
     */
    public function getRepositoryStopWords(): StopWordsRepositoryInterface
    {
        return $this->repository_stop_words;
    }

    /**
     * キーフレーズリポジトリ取得
     * @return KeyPhraseRepositoryInterface
     */
    public function getRepositoryKeyPhrase(): KeyPhraseRepositoryInterface
    {
        return $this->repository_key_phrase;
    }

}
<?php

namespace App\Services\Admin;

use App\Repositories\DatatablesResult;
use App\Repositories\KeyPhrase\KeyPhraseRepositoryInterface;
use App\Services\Bot\BotTruthService;
use App\Services\Bot\Truth\TruthDbService;
use App\Services\RepositoryServiceInterface;

/**
 * キーフレーズサービス
 * Class KeyPhraseService
 * @package App\Services\Admin
 */
class KeyPhraseService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var KeyPhraseRepositoryInterface
     */
    private $repository;
    /**
     * @var LearningService
     */
    private $learning_service;
    /**
     * @var BotTruthService
     */
    private $truth_service;

    /**
     * KeyPhraseService constructor.
     * @param KeyPhraseRepositoryInterface $repository
     * @param LearningService $learning_service
     * @param BotTruthService $truth_service
     */
    public function __construct(KeyPhraseRepositoryInterface $repository, LearningService $learning_service, BotTruthService $truth_service)
    {
        $this->repository = $repository;
        $this->learning_service = $learning_service;
        $this->truth_service = $truth_service;
    }

    /**
     * リポジトリ取得
     * @return KeyPhraseRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * キーフレーズデータ→表示用
     * @param array $row キーフレーズDB配列行
     * @return string
     */
    public function keyPhraseRowToDisplayWord($row)
    {
        return empty($row['replace_word']) ? $row['word'] : $row['replace_word'] . "({$row['word']})";
    }

    /**
     * キーフレーズIDから学習データを取得
     * @param integer $key_phrase_id キーフレーズID
     * @return \Generator
     */
    public function getLearningDataFromKeyPhraseId($key_phrase_id)
    {
        $truth_gen = $this->truth_service->getDbService()->getRepositoryTruth()->setParams(['key_phrase_id' => $key_phrase_id])->filterByParams()->getDbResult()->getGenerator();
        $api_ids = [];
        foreach ($truth_gen as $row) {
            $api_ids[] = $row['api_id'];
        }
        $learning_gen = empty($api_ids) ? [] : $this->learning_service->getRepository()->filterKeyPhraseList()
            ->setParams(['api_ids' => $api_ids])->filterByParams()->getDbResult()->getGenerator();
        $result = [];
        foreach ($learning_gen as $row) {
            if (!isset($result[$row['api_id']])) {
                $result[$row['api_id']] = $row;
                $result[$row['api_id']]['words'] = [];
                $result[$row['api_id']]['words_disp'] = [];
                $result[$row['api_id']]['count'] = 0;
            }
            $result[$row['api_id']]['count']++;
            $word = empty($row['replace_word']) ? $row['word'] : $row['replace_word'];
            $result[$row['api_id']]['words'][] = $word;
            if ($key_phrase_id == $row['key_phrase_id']) {
                $word = "<b>{$word}</b>";
            }
            if ($row['disabled'] == config('const.common.disabled.yes.id')) {
                $word = "<span style='text-decoration: line-through;'>{$word}</span>";
            }

            $result[$row['api_id']]['words_disp'][] = $word;
        }
        return $result;
    }

    /**
     * API_IDからキーフレーズを取得
     * @param integer $api_id API_ID
     * @return array
     */
    public function getKeyPhraseFromApiId($api_id)
    {
        $key_phrase_data = [];
        $key_phrase_gen = $this->getTruthDbService()->getRepositoryTruth()->setParams(['api_id' => $api_id])->filterByParams()->getDbResult()->getGenerator();
        foreach ($key_phrase_gen as $row) {
            $row['key_phrase'] = $this->keyPhraseRowToDisplayWord($row);
            $key_phrase_data[] = $row;
        }
        return $key_phrase_data;
    }

    /**
     * キーフレーズを一つだけ持っているかチェック
     * @param array $data 学習データ配列
     * @return bool
     */
    public function checkHasOneKeyPhrase($data)
    {
        foreach ($data as $row)
            if ($row['count'] == 1 &&
                ($row['disabled'] != config('const.common.disabled.yes.id')))
                return true;
        return false;
    }

    /**
     * 学習データインポート用キーフレーズ登録
     * @param integer $api_id API_ID
     * @param array $params インポートパラメータ
     */
    public function importKeyPhraseLearning($api_id, $params)
    {
        $repository_truth = $this->truth_service->getDbService()->getRepositoryTruth();
        //現在の真理表を取得
        $truth_gen = $repository_truth->setParams(['api_id' => $api_id])->filterByParams()->getDbResult()->getGenerator();
        $now_truth_data = [];
        foreach ($truth_gen as $row) {
            $now_truth_data[$row['key_phrase_id']] = $row;
        }
        //入力されたパラメータでセット
        foreach ($params as $row) {
            $auto_priority_disabled = ($row['priority'] == '') ? 0 : 1;
            if (isset($row['word'])) {
                $row['id'] = $this->repository->findOrSave($row['word'], config('const.truth.key_phrase_type.user_add.id'));
            }
            if (isset($now_truth_data[$row['id']])) {
                //key_phrase_id使用済み
                $update = ['auto_key_phrase_priority_disabled' => $auto_priority_disabled, 'count' => 1];
                if ($auto_priority_disabled == 1) $update['key_phrase_priority'] = $row['priority'];
                $repository_truth->update($now_truth_data[$row['id']]['truth_id'], $update);
                unset($now_truth_data[$row['id']]);
            } else {
                //key_phrase_id未使用
                $create = [
                    'api_id' => $api_id,
                    'key_phrase_id' => $row['id'],
                    'auto_key_phrase_priority_disabled' => $auto_priority_disabled,
                    'count' => 1,
                ];
                if ($auto_priority_disabled == 1) $create['key_phrase_priority'] = $row['priority'];
                $repository_truth->create($create);
            }
        }
        if (!empty($now_truth_data)) {
            //残っている場合、削除
            $repository_truth->setParams(['api_id' => $api_id, 'key_phrase_ids' => array_keys($now_truth_data)])->filterByParams()->deleteByQuery();
        }
    }

    /**
     * 学習データ手動キーフレーズ更新
     * @param integer $api_id API_ID
     * @param array $params フォームパラメータ
     */
    public function updateKeyPhraseLearning($api_id, $params)
    {
        //旧データ取得[truth_id => row]
        $now_key_phrase_data = [];
        foreach ($this->getKeyPhraseFromApiId($api_id) as $row) {
            $row = (array)$row;
            $now_key_phrase_data[$row['truth_id']] = $row;
        }
        foreach ($params['truth_data'] as $row) {
            if (isset($row['key_phrase_id'])) {
                $key_phrase_id = $row['key_phrase_id'];
            } else {
                $key_phrase_id = $this->repository->findOrSave($row['key_phrase'], config('const.truth.key_phrase_type.user_add.id'));
            }
            $auto_priority_disabled = $row['auto_key_phrase_priority_disabled'] ?? 0;
            if (empty($row['truth_id'])) {
                //追加
                $create = [
                    'api_id' => $api_id,
                    'key_phrase_id' => $key_phrase_id,
                    'auto_key_phrase_priority_disabled' => $auto_priority_disabled,
                ];
                if ($auto_priority_disabled) $create['key_phrase_priority'] = $row['key_phrase_priority'];
                $this->truth_service->getDbService()->getRepositoryTruth()->create($create);
            } else {
                //変更
                $update = [
                    'auto_key_phrase_priority_disabled' => $auto_priority_disabled,
                ];
                if ($auto_priority_disabled) $update['key_phrase_priority'] = $row['key_phrase_priority'];
                $truth_id = $row['truth_id'];
                $now_key_phrase_row = $now_key_phrase_data[$truth_id];
                if ($now_key_phrase_row['key_phrase'] == $row['key_phrase']) {
                    //なし
                } else {
                    //あり
                    $update['key_phrase_id'] = $key_phrase_id;
                }
                //状態はどれでもアップデート
                $this->truth_service->getDbService()->getRepositoryTruth()->update($truth_id, $update);
                unset($now_key_phrase_data[$truth_id]);
            }
        }

        if (!empty($now_key_phrase_data)) {
            //削除
            foreach ($now_key_phrase_data as $truth_id => $row) {
                $this->truth_service->getDbService()->getRepositoryTruth()->deleteOneById($truth_id);
            }
        }
    }

    /**
     * 自動キーフレーズ登録
     * @param integer $api_id API_ID
     */
    public function autoSetKeyPhrase($api_id)
    {
        if (!config('bot.truth.enabled')) return;
        $learning_data = $this->learning_service->getRepository()->setParams(['api_id' => $api_id])->filterByParams()->getDbResult()->getOne();
        $word_counts = $this->getTruthMorphWordCounts($learning_data['question']);
        $this->truth_service->getDbService()->saveTruthTable($api_id, $word_counts);
    }

    /**
     * キーフレーズ毎の出現回数取得
     * @param string $question 文章
     * @return array [キーフレーズ=>出現回数,...]
     */
    public function getTruthMorphWordCounts($question)
    {
        return $this->truth_service->getMorphService()->setMessage($question)->execMorph()->getWordCounts();
    }

    /**
     * キーフレーズ削除
     * @param string $id tbl_key_phrase.id
     * @return bool
     */
    public function deleteKeyPhrase($id)
    {
        $key_phrase_row = $this->repository->getOneById($id);
        $truth_gen = $this->truth_service->getDbService()->getRepositoryTruth()->setParams(['key_phrase_id' => $key_phrase_row['key_phrase_id']])->filterByParams()->getDbResult()->getGenerator();
        $api_ids = [];
        foreach ($truth_gen as $row) {
            $api_ids[] = $row['api_id'];
        }
        $counts = $this->truth_service->getDbService()->getRepositoryTruth()->filterWordCountPerApiId()->setParams(['api_ids' => $api_ids])->filterByParams()->getDbResult()->getArray();
        $counts = array_column($counts, 'cnt');
        if (in_array(1, $counts)) {
            //削除不可
            return false;
        } else {
            $this->truth_service->getDbService()->getRepositoryTruth()->setParams(['key_phrase_id' => $key_phrase_row['key_phrase_id']])->filterByParams()->deleteByQuery();
            $this->repository->deleteOneById($id);
            return true;
        }
    }

    /**
     * 類義語変更時、真理表を同期
     * @param array $option
     */
    public function syncTruthByChangeRelationWord($option = [])
    {
        if (function_exists('debugbar')) {
            debugbar()->disable();
        }
        $learning_gen = $this->learning_service->getRepository()->setParams(['auto_key_phrase' => true])->filterByParams()->getDbResult()->getGenerator();
        foreach ($learning_gen as $learning_row) {
            $truth_morph_service = $this->learning_service->getTruthService()->getMorphService()->setMessage($learning_row['question'])->execMorph();
            $replace_ids = $truth_morph_service->getMorphBaseService()->getReplaceIds();
            if (
                empty($option) ||
                (
                    in_array($option['variant_id'] ?? null, $replace_ids['variant']) ||
                    in_array($option['synonym_id'] ?? null, $replace_ids['synonym']) ||
                    in_array($option['proper_noun_id'] ?? null, $replace_ids['proper_noun'])
                )
            ) {
                $this->learning_service->getTruthService()->getDbService()->saveTruthTable($learning_row['api_id'], $truth_morph_service->getWordCounts());
            }
        }
        $this->learning_service->getCalcPriorityService()->calcAllTruthPriority()->updateAllTruthPriority();
    }

    /**
     * 学習データサービス取得
     * @return LearningService
     */
    public function getLearningService(): LearningService
    {
        return $this->learning_service;
    }

    /**
     * 真理表DBサービス取得
     * @return TruthDbService
     */
    public function getTruthDbService(): TruthDbService
    {
        return $this->truth_service->getDbService();
    }

}
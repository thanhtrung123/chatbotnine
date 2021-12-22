<?php

namespace App\Services\Admin;

use App\Repositories\Learning\LearningRepositoryInterface;
use App\Services\Bot\BotBaseService;
use App\Services\Bot\Truth\TruthCalcPriorityService;
use App\Services\RepositoryServiceInterface;
use App\Services\Bot\BotTruthService;
use Carbon\Carbon;

/**
 * 学習データサービス
 * Class LearningService
 * @package App\Services\Admin
 */
class LearningService implements RepositoryServiceInterface, AdminServiceInterface
{
    use LogTrait;
    /**
     * @var LearningRepositoryInterface
     */
    private $repository;
    /**
     * @var BotBaseService
     */
    private $base_service;
    /**
     * @var BotTruthService
     */
    private $truth_service;
    /**
     * @var CategoryService
     */
    private $category_service;
    /**
     * @var TruthCalcPriorityService
     */
    private $calc_priority_service;

    /**
     * LearningService constructor.
     * @param LearningRepositoryInterface $repository
     * @param BotTruthService $truth_service
     * @param BotBaseService $bot_base_service
     * @param CategoryService $category_service
     * @param TruthCalcPriorityService $calc_priority_service
     */
    public function __construct(LearningRepositoryInterface $repository, BotTruthService $truth_service, BotBaseService $bot_base_service, CategoryService $category_service, TruthCalcPriorityService $calc_priority_service)
    {
        $this->repository = $repository;
        $this->base_service = $bot_base_service;
        $this->truth_service = $truth_service;
        $this->category_service = $category_service;
        $this->calc_priority_service = $calc_priority_service;
    }

    /**
     * リポジトリ取得
     * @return LearningRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * 学習データ作成
     * @param array $params 学習データ配列
     * @return array $params
     */
    public function createLearning($params)
    {
        //形態素解析
        $params['question_morph'] = $this->morphMessage($params['question']);
        //API_ID取得
        $now = Carbon::now();
        $params['api_id'] = $this->repository->getNextApiId();
        $params['update_at'] = $now;
        $this->repository->create($params);
        return $params;
    }

    /**
     * 学習データ更新
     * @param integer $id tbl_learning.id
     * @param array $params 学習データ配列
     * @return mixed
     */
    public function updateLearning($id, $params)
    {
        //形態素解析
        $params['question_morph'] = $this->morphMessage($params['question']);
        $params['update_at'] = Carbon::now();
        return $this->repository->update($id, $params);
    }

    /**
     * 学習データ削除
     * @param integer $id tbl_learning.id
     * @return mixed
     */
    public function deleteLearning($id)
    {
        //消すデータを取得
        $data = $this->repository->getOneById($id);
        //真理表を削除
        $this->truth_service->getDbService()->getRepositoryTruth()->setParams(['api_id' => $data['api_id']])->filterByParams()->deleteByQuery();
        //消す
        return $this->repository->deleteOneById($id);
    }

    /**
     * プライオリティを計算
     * （入力パラメータに計算結果を追加して返却）
     * @param integer $api_id API_ID
     * @param array $params フォームデータ配列
     * @return array $params
     */
    public function calcPriority($api_id, $params)
    {
        $key_phrase_id_list = $interrupt_truth_data = [];
        foreach ($params['truth_data'] as $truth_row) {
            if (isset($truth_row['key_phrase_id'])) {
                $key_phrase_id = $truth_row['key_phrase_id'];
            } else {
                $key_phrase_id = $this->truth_service->getDbService()->getRepositoryKeyPhrase()->findOnly($truth_row['key_phrase']) ??
                    $this->truth_service->getDbService()->getRepositoryKeyPhrase()->getNextKeyPhraseId();
            }
            $interrupt_truth_data[] = [$key_phrase_id, 1];
            $key_phrase_id_list[$truth_row['key_phrase']] = $key_phrase_id;
        }
        $all_truth_priority = $this->calc_priority_service->setInterruptTruthData([$api_id => $interrupt_truth_data])->calcAllTruthPriority()->getAllTruthPriority();
        $result = $all_truth_priority[$api_id];
        foreach ($params['truth_data'] as $idx => $truth_row) {
            if (!empty($truth_row['auto_key_phrase_priority_disabled'])) continue; //手動のキーフレーズを含める（値は反映しない）
            if (!isset($key_phrase_id_list[$truth_row['key_phrase']])) continue;
            $params['truth_data'][$idx]['key_phrase_priority'] = $result[$key_phrase_id_list[$truth_row['key_phrase']]]['priority'];
        }
        return $params;
    }

    /**
     * 学習データ同期
     * （形態素解析→APIデータ削除→APIデータ追加）
     * @param string $mode モード
     * @return array
     */
    public function syncLearning($mode)
    {
        ini_set('max_execution_time', config('bot.common.sync_timeout', 600));
        if (env('APP_DEBUG')) {
            ini_set('memory_limit', config('bot.common.sync_memory', '512M'));
        }
        $api = $this->base_service->getBotApiService()->getApi();
        if (function_exists('debugbar')) {
            debugbar()->disable();
        }
        //現在DBに入っているデータを取得
        $data = $this->repository->getLearningData()->getArray();
        //同期
        switch ($mode) {
            case 'morph'://形態素解析
                foreach ($data as $row) {
                    $row['question_morph'] = $this->morphMessage($row['question']);
                    $this->repository->update($row['id'], $row);
                }
                break;
            case 'delete'://APIデータ削除
                if (config('bot.api.use') == 'demo') break;
                $data = $api->getLearningData()->current();
                if (empty($data)) break;
                $api->deleteLearningData();
                $api->publishLearningData();
                break;
            case 'add'://APIデータ追加
                if (config('bot.api.use') == 'demo') break;
                if (config('bot.api.use_seq_duplicate_question')) {
                    $duplicate_questions = array_keys(array_filter(array_count_values(array_column($data, 'question_morph')), function ($cnt) {
                        return $cnt > 1;
                    }));
                    $qm_ary = [];
                    foreach ($data as $idx => $row) {
                        if (!in_array($row['question_morph'], $duplicate_questions)) continue;
                        $qm_ary[$row['question_morph']] = isset($qm_ary[$row['question_morph']]) ? $qm_ary[$row['question_morph']] + 1 : 1;
                        $data[$idx]['question_morph'] = $row['question_morph'] . ' ' . $qm_ary[$row['question_morph']];
                    }
                }
                $api->addLearningData($data);
                $api->publishLearningData();
                // Update time sysn db
                $date_time = Carbon::now();
                foreach ($data as $row) {
                    $this->repository->setParams(['ids' => $row['id']])->filterByParams()->updateByQuery([
                        'synced_at' => $date_time
                    ]);
                }
                break;
        }
        return [$mode];
    }

    /**
     * 質問文章を形態素解析する（QnA用キーフレーズ）
     * @param string $message 文章
     * @return string
     */
    public function morphMessage($message)
    {
        $morph = $this->base_service->getBotMorphService()->setMessage($message)->execMorph()->getMessage();
        return $morph;
    }

    /**
     * 真理表を保存
     * @param integer $api_id API_ID
     * @param string $question 文章（質問文）
     * @return bool
     */
    public function saveTruthTable($api_id, $question)
    {
        if (!config('bot.truth.enabled')) return false;
        $word_counts = $this->truth_service->getMorphService()->setMessage($question)->execMorph()->getWordCounts();
        $this->truth_service->getDbService()->saveTruthTable($api_id, $word_counts);
        return true;
    }

    /**
     * チャットボット基本サービス取得
     * @return BotBaseService
     */
    public function getBaseService(): BotBaseService
    {
        return $this->base_service;
    }

    /**
     * 真理表サービス取得
     * @return BotTruthService
     */
    public function getTruthService(): BotTruthService
    {
        return $this->truth_service;
    }

    /**
     * カテゴリサービス取得
     * @return CategoryService
     */
    public function getCategoryService(): CategoryService
    {
        return $this->category_service;
    }

    /**
     * プライオリティ計算サービス取得
     * @return TruthCalcPriorityService
     */
    public function getCalcPriorityService(): TruthCalcPriorityService
    {
        return $this->calc_priority_service;
    }

}
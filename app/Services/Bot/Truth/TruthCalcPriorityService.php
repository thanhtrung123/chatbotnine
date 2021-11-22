<?php

namespace App\Services\Bot\Truth;
/**
 * プライオリティ計算サービス
 * Class TruthCalcPriorityService
 * @package App\Services\Bot\Truth
 */
class TruthCalcPriorityService
{
    /**
     * @var TruthDbService
     */
    private $truth_db_service;
    /**
     * @var array 真理表データ
     */
    private $truth_data;
    /**
     * @var array 真理表IDリスト
     */
    private $truth_id_list;
    /**
     * @var array 現状プライオリティデータ
     */
    private $now_priority_data;
    /**
     * @var array API_ID配列
     */
    private $api_ids;
    /**
     * @var array 割り込み真理表データ
     */
    private $interrupt_truth_data;
    /**
     * @var array 真理表プライオリティデータ
     */
    private $truth_priority_data;

    /**
     * TruthCalcPriorityService constructor.
     * @param TruthDbService $truth_db_service
     */
    public function __construct(TruthDbService $truth_db_service)
    {
        $this->truth_db_service = $truth_db_service;
        $this->interrupt_truth_data = [];
    }

    /**
     * 全真理表のプライオリティを計算
     * @return $this
     */
    public function calcAllTruthPriority()
    {
        $this->assignTruthData();
        $data = array_values($this->truth_data);
        $tfidf_ary = calc_tf_idf($data); //PHP
        $this->truth_priority_data = [];
        foreach ($this->api_ids as $idx => $api_id) {
            foreach ($data[$idx] as $input_idx => $input_row) {
                $tfidf_idx = array_search($input_row[0], array_column($tfidf_ary[$idx], 0));
                if ($tfidf_idx !== FALSE) {
                    $tfidf_row = $tfidf_ary[$idx][$tfidf_idx];
                    $priority = (integer)floor($tfidf_row[1] * 100);
                    $key_phrase_id = $tfidf_row[0];
                } else {
                    $key_phrase_id = $input_row[0];
                    $priority = 0;
                }
                $truth_id = $this->truth_id_list["{$api_id}_{$key_phrase_id}"];
                $this->truth_priority_data[$api_id][$key_phrase_id] = [
                    'truth_id' => $truth_id,
                    'priority' => $priority,
                ];
            }
        }
        return $this;
    }

    /**
     * 全真理表のプライオリティを取得
     * @return mixed
     */
    public function getAllTruthPriority()
    {
        return $this->truth_priority_data;
    }

    /**
     * 全真理表のプライオリティを更新
     */
    public function updateAllTruthPriority()
    {
        $update_data = [];
        foreach ($this->truth_priority_data as $api_id => $row) {
            foreach ($row as $key_phrase_id => $ary) {
                if (
                    $this->now_priority_data[$ary['truth_id']]['key_phrase_priority'] === $ary['priority'] ||
                    $this->now_priority_data[$ary['truth_id']]['auto_key_phrase_priority_disabled'] == 1
                ) continue;
                $update_data[$ary['priority']][] = $ary['truth_id'];
            }
        }
        foreach ($update_data as $priority => $ids) {
            $this->truth_db_service->getRepositoryTruth()->setParams(['ids' => $ids])->filterByParams()->updateByQuery([
                'key_phrase_priority' => $priority,
            ]);
        }
    }

    /**
     * 割り込み真理表データセット
     * @param array $interrupt_truth_data
     * @return $this
     */
    public function setInterruptTruthData($interrupt_truth_data)
    {
        $this->interrupt_truth_data = $interrupt_truth_data;
        return $this;
    }

    /**
     * 真理表データをアサイン
     */
    private function assignTruthData()
    {
        $truth_gen = $this->truth_db_service->getRepositoryTruth()->getDbResult()->getGenerator();
        $this->truth_data = $this->api_ids = $this->truth_id_list = $this->now_priority_data = [];
        foreach ($truth_gen as $row) {
            if (!isset($this->truth_data[$row['api_id']])) $this->truth_data[$row['api_id']] = [];
            $count = empty($row['count']) ? 1 : $row['count'];
            $this->truth_data[$row['api_id']][] = [$row['key_phrase_id'], $count];
            $this->truth_id_list["{$row['api_id']}_{$row['key_phrase_id']}"] = $row['truth_id'];
            $this->now_priority_data[$row['truth_id']] = [
                'key_phrase_priority' => $row['key_phrase_priority'],
                'auto_key_phrase_priority_disabled' => $row['auto_key_phrase_priority_disabled'],
            ];
        }
        foreach ($this->interrupt_truth_data as $api_id => $truth_row) {
            $this->truth_data[$api_id] = $truth_row;
            foreach ($truth_row as $row) {
                $this->truth_id_list["{$api_id}_{$row[0]}"] = $this->truth_id_list["{$api_id}_{$row[0]}"] ?? null;
            }
        }
        ksort($this->truth_data);
        foreach (array_keys($this->truth_data) as $api_id) {
            $this->api_ids[] = $api_id;
        }
    }

}
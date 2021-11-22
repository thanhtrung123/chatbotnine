<?php

namespace App\Services\Bot\Api;

use App\Services\Admin\LearningService;
use GuzzleHttp\Client;
use Util;

/**
 * デモAPIサービス
 * Class DemoService
 * @package App\Services\Bot\Api
 */
class DemoService extends ApiAbstract implements ApiInterface
{
    private $top;
    /* @var LearningService */
    private $service;

    /**
     * 学習データサービスセット
     * @param LearningService $service
     */
    public function setLearningService(LearningService $service)
    {
        $this->service = $service;
    }

    /**
     * 問い合わせ
     * @param array $options
     * @return \App\Services\Bot\Api\ApiInterface
     */
    public function inquiry(array $options = []): ApiInterface
    {
        $this->top = isset($this->params['json']['top']) ? $this->params['json']['top'] : 1;
        $question = $this->params['json']['question'];
        $data = $this->service->getRepository()->searchAnswer($question);
        foreach ($data as $idx => $row) {
            $data[$idx]['metadata'] = [
                [
                    'name' => 'meta',
                    'value' => $row['metadata'],
                ],
            ];
            if (config('bot.api.answer_is_id')) {
                $data[$idx]['answer'] = $row['api_id'];
            } else {
                $data[$idx]['answer'] = $row['answer'];
                $data[$idx]['metadata'][] = [
                    'name' => 'id',
                    'value' => $row['api_id'],
                ];
            }
            //質問文(morph)と入力された質問のレーベンシュタイン距離を取得
            $score = levenshteinNormalizedUtf8($question, $row['question_morph']) * 100;
            $data[$idx]['score'] = $score;
            $data[$idx]['question_str'] = $row['question'];
            $data[$idx]['question'] = $row['question_morph'];
        }
        array_multisort(array_column($data, 'score'), SORT_DESC, $data);
        $data = array_slice($data, 0, $this->top);
        $this->result = $data;
        return $this;
    }

    /**
     * APIバージョン(dummy)
     * @return array
     */
    public function getVersion()
    {
        return ['demo', 'demo'];
    }


    /**
     * 学習データ取得
     * @return \Generator
     */
    public function getLearningData(): \Generator
    {
        foreach ($this->service->getRepository()->getDbResult()->getGenerator() as $row) {
            yield $row['id'] => [
                'id' => $row['api_id'],
                'questions' => [$row['question_morph']],
                'question_str' => $row['question'],
                'answer' => config('bot.api.answer_is_id') ? $row['api_id'] : $row['answer'],
            ];
        }
    }

    /**
     * 学習データ削除(dummy)
     * @param array $params
     * @return array|mixed
     */
    public function deleteLearningData($params = [])
    {
        return [];
    }

    /**
     * 学習データ追加(dummy)
     * @param $learning_data
     * @return mixed|void
     */
    public function addLearningData($learning_data)
    {

    }

    /**
     * 学習データ公開(dummy)
     * @return mixed
     */
    public function publishLearningData()
    {
        return [];
    }

    /**
     * パラメータセット
     * @param array $params
     * @return \App\Services\Bot\Api\ApiInterface
     */
    public function setParams(array $params = [], $content = 'json'): ApiInterface
    {
        $this->params[$content] = $params;
        return $this;
    }

    /**
     * 問い合わせ結果
     * @param array $options
     * @return array
     */
    public function getResult(array $options = []): array
    {
        $result = [];
        foreach ($this->result as $idx => $answer) {
            $answer['id'] = $answer['api_id'];
            if ($this->converter) {
                $answer = $this->converter->convertRow($answer, $idx);
            }
            $result[] = new ApiResult($answer);
        }
        if (!empty($this->top) && count($result) < $this->top) {
            for ($i = count($result) - 1; $i < $this->top; $i++) {
                $result[] = new ApiResult();
            }
        }
        return $result;
    }

}
<?php

namespace App\Services\Bot\Api;

use Util;

/**
 * チャットボットAPIサービス[QnA Maker]
 * Class QnaService
 * @package App\Services\Bot\Api
 */
class QnaService extends ApiAbstract implements ApiInterface
{
    private $top;

    /**
     * 問い合わせ
     * @param array $options
     * @return \App\Services\Bot\Api\ApiInterface
     */
    public function inquiry(array $options = []): ApiInterface
    {
        $this->params['headers'] = [
            'Content-Type' => 'application/json',
            'Authorization' => 'EndpointKey ' . config('bot.api.qna.knowledge.endpoint'),
        ];
        $this->top = isset($this->params['json']['top']) ? $this->params['json']['top'] : 0;
        $url = config('bot.api.qna.knowledge.host') . config('bot.api.qna.knowledge.answer');
        Util::execTimeStart('qna_inq');
        $response = $this->client->post($url, $this->params);
        Util::execTimeStop('qna_inq');

        $res_json = json_decode($response->getBody(), true);
        if (isset($res_json['answers'])) {
            $this->result = $res_json['answers'];
        }
        return $this;
    }

    /**
     * バージョン取得
     * @return array
     */
    public function getVersion()
    {
        // TODO: Implement getVersion() method.
        $this->setSubscriptionHeader();
        $url = config('bot.api.qna.endpoint') . config('bot.api.qna.knowledge.version');
        $response = $this->client->get($url, $this->params);
        $result = json_decode($response->getBody(), true);
        return [
            $result['installedVersion'],
            $result['lastStableVersion'],
        ];
    }

    /**
     * 学習データ取得
     * @return \Generator
     */
    public function getLearningData(): \Generator
    {
        $this->setSubscriptionHeader();
        $url = config('bot.api.qna.endpoint') . config('bot.api.qna.knowledge.download');
        $response = $this->client->get($url, $this->params);
        $result = json_decode($response->getBody(), true);
        foreach ($result['qnaDocuments'] as $row) {
            yield $row['id'] => [
                'id' => $row['id'],
                'questions' => $row['questions'],
                'answer' => $row['answer'],
                'metadata' => array_column($row['metadata'], 'value', 'name')
            ];
        }
    }

    /**
     * 学習データ追加
     * @param $learning_datas
     * @return mixed
     */
    public function addLearningData($learning_datas)
    {
        $ret = [];
        foreach (array_chunk($learning_datas, config('bot.api.qna.knowledge.chunk_size')) as $learning_data) {
            $this->buildAddParams($learning_data);
            $ret[] = $this->updateLearningData();
            $this->clearParams();
        }
        return $ret;
    }

    /**
     * 学習データ追加用パラメータ作成
     * @param $learning_data
     */
    private function buildAddParams($learning_data)
    {
        $qnaList = [];
        foreach ($learning_data as $row) {
            $qnaRow = [
                'id' => 0,
                'source' => config('app.name'),
                'questions' => [
                    //TODO:複数の質問の場合？
                    $row['question_morph'],
                ],
                'metadata' => [],
            ];
            if (!empty($row['metadata'])) {
                $qnaRow['metadata'][] = [
                    'name' => 'meta',
                    'value' => $row['metadata'],
                ];
            }
            if (config('bot.api.answer_is_id')) {
                $qnaRow['answer'] = $row['api_id'];
            } else {
                $qnaRow['answer'] = $row['answer'];
                $qnaRow['metadata'][] = [
                    'name' => 'id',
                    'value' => $row['api_id'],
                ];
            }
            $qnaList[] = $qnaRow;
        }
        $this->params['json'] = [
            'add' => [
                'qnaList' => $qnaList,
                'urls' => [],
                'files' => [],
            ],
        ];
    }

    /**
     * 学習データ削除
     * @return mixed
     */
    public function deleteLearningData()
    {
        $this->buildDeleteParams();
        $ret = $this->updateLearningData();
        $this->clearParams();
        return $ret;
    }

    /**
     * 学習データ削除用パラメータ作成
     */
    private function buildDeleteParams()
    {
        $this->params['json'] = [
            'delete' => [
                'sources' => [
                    config('app.name'),
                ]
            ],
        ];
    }

    /**
     * 学習データ更新
     * @return mixed
     */
    private function updateLearningData()
    {
        $this->setSubscriptionHeader();
        $url = config('bot.api.qna.endpoint') . config('bot.api.qna.knowledge.publish');
        $response = $this->client->patch($url, $this->params);
        $this->waitOperation($response->getHeader('Location')[0]);
        $result = json_decode($response->getBody(), true);
        return $result;
    }

    /**
     * 学習データ公開
     * @return mixed
     */
    public function publishLearningData()
    {
        $this->setSubscriptionHeader();
        $this->params['json'] = [];
        $url = config('bot.api.qna.endpoint') . config('bot.api.qna.knowledge.publish');
        $response = $this->client->post($url, $this->params);
        $result = json_decode($response->getBody(), true);
        return $result;
    }

    /**
     * サブスクリプションキーをヘッダーにセット
     */
    private function setSubscriptionHeader()
    {
        $this->params['headers'] = [
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => config('bot.api.qna.knowledge.subscription'),
        ];
    }

    /**
     * 処理待ち
     * @param $location
     */
    private function waitOperation($location)
    {
        $this->setSubscriptionHeader();
        $url = config('bot.api.qna.endpoint') . $location;
        $response = $this->client->get($url, $this->params);
        $result = json_decode($response->getBody(), true);
        if ($result['operationState'] == 'Running' || $result['operationState'] == 'NotStarted') {
            $retry = $response->getHeader('Retry-After');
            // 待ち時間の設定
            $wait_sec = 30;
            // 指定が無い場合
            if (!empty($retry)) {
                // 数値の場合はそのまま設定する
                if (is_numeric($retry[0])) {
                    $wait_sec = $retry[0];
                }
                // 時、分の指定がある場合は秒に変換して設定する
                else if (preg_match('/^\d{1,2}:\d{1,2}:\d{1,2}$/', $retry[0])) {
                    $retry_time = explode(':', $retry[0]);
                    $wait_sec = $retry_time[0] * 3600 + $retry_time[1] * 60 + $retry_time[2];
                }
            }
            sleep($wait_sec);
        }
    }

    /**
     * パラメータセット
     * @param array $params
     * @param string $content
     * @return ApiInterface
     */
    public function setParams(array $params = [], $content = 'json'): ApiInterface
    {
        $this->params[$content] = $params;
        return $this;
    }

    /**
     * パラメータクリア
     */
    private function clearParams()
    {
        $this->params = [];
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
            if (!isset($answer['questions'][0])) break;
            $answer['question_str'] = $answer['questions'][0];
            if (config('bot.api.answer_is_id')) {
                $answer['id'] = $answer['answer'];
            } else {
                $meta = array_column($answer['metadata'], 'value', 'name');
                $answer['id'] = $meta['id'] ?? -1;
            }
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
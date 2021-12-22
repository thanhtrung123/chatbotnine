<?php

namespace App\Imports\Admin;

use App\Imports\Traits\DuplicateTrait;
use App\Models\Learning;
use App\Services\Admin\KeyPhraseService;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use App\Services\Admin\LearningService;
use Carbon\Carbon;
/**
 * 学習データインポート
 * Class LearningImport
 * @package App\Imports\Admin
 */
class LearningImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable, SkipsFailures, DuplicateTrait;
    /**
     * @var LearningService
     */
    private $learning_service;
    /**
     * @var KeyPhraseService
     */
    private $key_phrase_service;

    /**
     * LearningImport constructor.
     * @param LearningService $learning_service
     * @param KeyPhraseService $key_phrase_service
     */
    public function __construct(LearningService $learning_service, KeyPhraseService $key_phrase_service)
    {
        $this->learning_service = $learning_service;
        $this->key_phrase_service = $key_phrase_service;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $key_phrase = $row['key_phrase'];
        if (empty($key_phrase)) {
            $this->learning_service->saveTruthTable($row['id'], $row['question']);
        } else {
            $this->key_phrase_service->importKeyPhraseLearning($row['id'], $this->parseKeyPhraseSetting($key_phrase));
        }
        return new Learning([
            'api_id' => $row['id'],
            'question' => $row['question'],
            'answer' => $row['answer'],
            'metadata' => $row['metadata'],
            'question_morph' => $this->learning_service->morphMessage($row['question']),
            'auto_key_phrase_disabled' => !empty($key_phrase),
            'update_at' => Carbon::now()
        ]);
    }

    /**
     * バリデート
     * @param $validator
     * @param $row
     * @return array|void
     */
    public function validateRow($validator, $row)
    {
        $error = [];
        if (isset($row['id']) && $this->duplicateCheck('id', $row)) {
            $error['id'][] = $this->customValidationAttributes()['id'] . __("が重複しています。");
        }
        if (isset($row['question']) && $this->duplicateCheck('question', $row)) {
            $error['question'][] = $this->customValidationAttributes()['question'] . config('validation.duplicate');
        }
        $attr_name = $this->customValidationAttributes()['key_phrase'];
        if (!empty($row['key_phrase'])) {
            //手動
            $key_phrase_setting = $this->parseKeyPhraseSetting($row['key_phrase']);
            if (empty($key_phrase_setting)) {
                $error['key_phrase'][] = "{$attr_name}のフォーマットに誤りがあります。";
            } else {
                foreach ($key_phrase_setting as $setting) {
                    if (!empty($setting['priority'])) {
                        if (!is_numeric($setting['priority'])) {
                            $error['key_phrase'][] = "{$attr_name}の優先度が数値ではありません。[{$setting['priority']}]";
                        } else if ($setting['priority'] < 0 || $setting['priority'] > 100) {
                            $error['key_phrase'][] = "{$attr_name}の優先度が設定可能範囲を超えています。[{$setting['priority']}]";
                        }
                    }
                    if (isset($setting['id']) && empty($this->learning_service->getTruthService()->getDbService()->getRepositoryKeyPhrase()->findOneBy(['key_phrase_id' => $setting['id']]))) {
                        $error['key_phrase'][] = "{$attr_name}のキーフレーズID[{$setting['id']}]が存在しません。";
                    }
                    if (isset($setting['word'])) {
                        if ($this->learning_service->getTruthService()->getDbService()->isStopWord($setting['word'])) {
                            $error['key_phrase'][] = 'ストップワードが指定されています。' . "[{$setting['word']}]";
                        }
                        $setting['id'] = $this->key_phrase_service->getRepository()->findOnly($setting['word']);
                    }
                    if (!empty($setting['id']) && $this->duplicateCheck("key_phrase_id_{$row['id']}", $setting['id'])) {
                        $error['key_phrase_id'][] = 'キーフレーズID' . __("が重複しています。") . "[{$setting['id']}]";
                    }
                }
            }
        } else {
            //自動
            $words = $this->learning_service->getTruthService()->getMorphService()->setMessage($row['question'])->execMorph()->getWords();
            if (empty($words)) {
                $error['question'][] = $this->customValidationAttributes()['question'] . 'にキーフレーズとなるワードが存在しません。';
            }
        }
        return $error;
    }

    /**
     * キーフレーズ設定のパース
     * @param $key_phrase
     * @return array
     */
    private function parseKeyPhraseSetting($key_phrase)
    {
        $result = [];
        if (strpos($key_phrase, ':') === FALSE) return $result;
        foreach (explode(',', $key_phrase) as $line) {
            list($word, $priority) = explode(':', $line);
            $row = ['priority' => $priority];
            if (preg_match('/^\[(.*)\]$/', $word, $match)) {
                $row['id'] = $match[1] ?? 0;
            } else {
                $row['word'] = $word;
            }
            $result[] = $row;
        }
        return $result;
    }

    /**
     * ルール
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'question' => 'required|string',
            'answer' => 'required|string',
            'metadata' => 'nullable|string',
        ];
    }

    /**
     * カスタムバリデート属性
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'id' => __('学習データ') . 'ID',
            'question' => '質問文章',
            'answer' => '回答文章',
            'metadata' => 'メタデータ',
            'key_phrase' => 'キーフレーズ設定',
        ];
    }

    /**
     * ヘッダー行
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * チャンクサイズ
     * @return int
     */
    public function chunkSize(): int
    {
        return 50;
    }

    /**
     * 処理サイズ
     * @return int
     */
    public function batchSize(): int
    {
        return 50;
    }
}
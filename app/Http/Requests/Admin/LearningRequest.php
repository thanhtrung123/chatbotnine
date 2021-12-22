<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Validator;

/**
 * 学習データリクエスト
 * Class LearningRequest
 * @package App\Http\Requests\Admin
 */
class LearningRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //特殊バリデート
        Validator::extend('valid1', function ($attribute, $value, $parameters, $validator) {
            /**
             * @var \Illuminate\Validation\Validator $validator
             */
            $childValidator = Validator::make($value, []);
            $childValidator->setAttributeNames(['key_phrase_priority' => '優先度']);
            $childValidator->sometimes('key_phrase_priority', 'required|integer|min:0|max:100', function ($data) {
                return $data['auto_key_phrase_priority_disabled'] == 1;
            });
            foreach ($childValidator->errors()->getMessages() as $key => $error) {
                foreach ($error as $idx => $val) {
                    $validator->getMessageBag()->add("{$attribute}.{$key}", $val);
                }
            }
            if (!$childValidator->passes()) {
                return false;
            }
            return true;
        });
        $diff_rules = [];
        if ($this->method() == 'PUT') {
            //update
            $diff_rules['question'] = 'required|string|unique:tbl_learning,question,' . $this->route('learning');
        } else {
            //create
            $diff_rules['question'] = 'required|string|unique:tbl_learning';
        }
        $rules = [
            'answer' => 'required|string',
            'truth_data.*' => 'valid1',
        ];
        return $rules + $diff_rules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'カテゴリ',
            'question' => '質問文章',
            'answer' => '回答文章',
            'key_phrase.*' => __('キーフレーズ'),
            'truth_data.*.key_phrase' => __('キーフレーズ'),
            'key_phrase_priority.*' => '優先度',
            'auto_key_phrase_priority_disabled.*' => '優先度の指定',

        ];
    }
}
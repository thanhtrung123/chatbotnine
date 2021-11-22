<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * キーフレーズリクエスト
 * Class KeyPhraseRequest
 * @package App\Http\Requests\Admin
 */
class KeyPhraseRequest extends FormRequest
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
        $diff_rules = [
            'priority' => 'integer',
        ];
        if ($this->method() == 'PUT') {
            //update
            $diff_rules['word'] = 'required|string|max:255|unique:tbl_key_phrase,word,' . $this->route('key_phrase');
        } else {
            //create
            $diff_rules['word'] = 'required|string|max:255|unique:tbl_key_phrase';
        }
        return [
                'replace_word' => 'nullable|string|max:255',
            ] + $diff_rules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes(): array
    {
        return [
            'word' => __('キーフレーズ'),
            'replace_word' => '置換後文字',
            'status' => '状態',
            'priority' => '優先度',
        ];
    }

    /**
     * 上書き
     * @param null $keys
     * @return array
     */
    public function all($keys = null)
    {
        $results = parent::all($keys);
        if ($this->filled('disabled')) {
            //状態の型変更
            $results['disabled'] = (int)$this->input('disabled');
        }
        return $results;
    }


}
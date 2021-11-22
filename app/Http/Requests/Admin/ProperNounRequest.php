<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 固有名詞リクエスト
 * Class ProperNounRequest
 * @package App\Http\Requests\Admin
 */
class ProperNounRequest extends FormRequest
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

        ];
        if ($this->method() == 'PUT') {
            //update
            $diff_rules['word'] = 'required|string|max:255|unique:tbl_proper_noun,word,' . $this->route('proper_noun');
        } else {
            //create
            $diff_rules['word'] = 'required|string|max:255|unique:tbl_proper_noun';
        }
        return [
            ] + $diff_rules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes(): array
    {
        return [
            'word' => __('固有名詞'),
        ];
    }

}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * シナリオリクエスト
 * Class CategoryRequest
 * @package App\Http\Requests\Admin
 */
class ScenarioRequest extends FormRequest
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
            'order' => 'nullable|integer',
            'api_ids' => 'nullable|regex:/^[0-9]+(\s*\,\s*[0-9]+)*$/',
            'parent_ids' => 'nullable|regex:/^[0-9]+(\s*\,\s*[0-9]+)*$/',
        ];
        if ($this->method() == 'PUT') {
            //update
        } else {
            //create
        }
        return $diff_rules;

    }

    /**
     * 属性
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'シナリオ',
            'order' => '表示順',
            'prent_ids' => '親シナリオID',
            'api_ids' => 'API_ID',

        ];
    }
}
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 関連質問リクエスト
 * Class LearningRelationRequest
 * @package App\Http\Requests\Admin
 */
class LearningRelationRequest extends FormRequest
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
            'id' => 'integer',
            'name' => 'required|string',
            'api_id' => 'required|integer',
            'relation_api_id' => 'required|integer',
            'order' => 'nullable|integer',
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
            'id' => 'ID',
            'name' => '関連質問名',
            'api_id' => 'API_ID',
            'relation_api_id' => '関連API_ID',
            'order' => '表示順',
        ];
    }
}
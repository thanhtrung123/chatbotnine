<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'relation_api_id' => 'required|integer',
            'order' => 'nullable|integer',
        ];
        if ($this->method() == 'PUT') {
            //update
            $diff_rules['name'] = [
                'required',
                'string',
                Rule::unique('tbl_learning_relation')
                    ->ignore($this->learning_relation)
                    ->where('api_id', $this->api_id)
            ];
            $diff_rules['api_id'] = [
                'required',
                'integer',
                Rule::unique('tbl_learning_relation')
                    ->ignore($this->learning_relation)
                    ->where('name', $this->name)
            ];
        } else {
            //create
            $diff_rules['name'] = [
                'required',
                'string',
                Rule::unique('tbl_learning_relation')
                    ->where('api_id', $this->api_id)
            ];
            $diff_rules['api_id'] = [
                'required',
                'integer',
                Rule::unique('tbl_learning_relation')
                    ->where('name', $this->name)
            ];
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

    public function messages()
    {
        return [
            'name.unique' => config('validation.unique_combine'),
            'api_id.unique' => config('validation.unique_combine'),
        ];
    }
}
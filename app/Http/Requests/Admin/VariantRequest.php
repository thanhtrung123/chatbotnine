<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 異表記管理リクエスト
 * Class VariantRequest
 * @package App\Http\Requests\Admin
 */
class VariantRequest extends FormRequest
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
        $diff_rules = [];
        if ($this->method() == 'PUT') {
            //update
            $diff_rules['noun_variant_text'] = 'required|string|unique:tbl_variant,noun_variant_text,' . $this->route('variant');
        } else {
            //create
            $diff_rules['noun_variant_text'] = 'required|string|unique:tbl_variant';
        }
        $rules = [
            'noun_text' => 'required|string',
        ];
        return $diff_rules + $rules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes(): array
    {
        return [
            'noun_variant_text' => '異表記文字',
            'noun_text' => '置換後文字',
        ];
    }
}
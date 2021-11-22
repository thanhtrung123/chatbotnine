<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * カテゴリリクエスト
 * Class CategoryRequest
 * @package App\Http\Requests\Admin
 */
class CategoryRequest extends FormRequest
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
            $diff_rules['name'] = 'required|string|max:255|unique:tbl_category,name,' . $this->route('category');
        } else {
            //create
            $diff_rules['name'] = 'required|string|max:255|unique:tbl_category';
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
            'name' => 'カテゴリ名',
        ];
    }
}
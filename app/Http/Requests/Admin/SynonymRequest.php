<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 類義語管理リクエスト
 * Class SynonymRequest
 * @package App\Http\Requests\Admin
 */
class SynonymRequest extends FormRequest
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
        //TODO:↓synonymのバリデートは一旦外す
        $diff_rules = [];
        if ($this->method() == 'PUT') {
            //update
            $diff_rules['keyword'] = 'required|string|max:255|unique:tbl_synonym,keyword,' . $this->route('synonym') . '|unique:tbl_synonym,synonym,' . $this->route('synonym');
//            $diff_rules['synonym'] = 'required|string|max:255|unique:tbl_synonym,keyword,' . $this->route('synonym');
        } else {
            //create
            $diff_rules['keyword'] = 'required|string|max:255|unique:tbl_synonym,keyword|unique:tbl_synonym,synonym';
//            $diff_rules['synonym'] = 'required|string|max:255|unique:tbl_synonym,keyword';
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
            'keyword' => '類義語文字',
            'synonym' => '置換後文字',
        ];
    }
}
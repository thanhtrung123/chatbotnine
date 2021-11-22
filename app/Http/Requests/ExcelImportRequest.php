<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Excelインポートリクエスト
 * Class ExcelImportRequest
 * @package App\Http\Requests
 */
class ExcelImportRequest extends FormRequest
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
        $rules = [];
        if ($this->method() == 'POST' && $this->get('store', false) === false) {
            $rules = [
                'excel' => 'file|mimes:xls,xlsx',
            ];
        }
        return $rules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes()
    {
        return ['excel' => 'Excelファイル'];
    }
}
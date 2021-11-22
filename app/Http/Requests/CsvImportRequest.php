<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CSVインポートリクエスト
 * Class CsvImportRequest
 * @package App\Http\Requests
 */
class CsvImportRequest extends FormRequest
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
                'csv' => 'file|mimetypes:text/plain|mimes:csv,txt',
            ];
        } else {

        }
        return $rules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes()
    {
        return ['csv' => 'CSVファイル'];
    }
}
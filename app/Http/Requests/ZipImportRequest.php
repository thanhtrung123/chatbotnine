<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Zipインポートリクエスト
 * Class ZipImportRequest
 * @package App\Http\Requests
 */
class ZipImportRequest extends FormRequest
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
                'zip' => 'file|mimes:zip|max:' . config('wysiwyg.config.post_max_size') . '' ,
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
        return ['zip' => 'Zipファイル'];
    }
}
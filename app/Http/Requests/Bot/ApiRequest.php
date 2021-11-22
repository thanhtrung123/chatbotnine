<?php

namespace App\Http\Requests\Bot;

use Illuminate\Foundation\Http\FormRequest;

/**
 * チャットボットAPIリクエスト
 * Class ApiRequest
 * @package App\Http\Requests\Bot
 */
class ApiRequest extends FormRequest
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
        return [
            'message' => 'required',
            'status' => 'required',
            'id' => 'required',
            'disp_id' => 'required',
        ];
    }
}

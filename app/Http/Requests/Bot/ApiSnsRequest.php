<?php

namespace App\Http\Requests\Bot;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * チャットボットAPIリクエスト
 * Class ApiRequest
 * @package App\Http\Requests\Bot
 */
class ApiSnsRequest extends FormRequest
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
            'id' => 'required|regex:/^[A-Za-z0-9]{40}$/',
            'message' => 'required',
            'status' => 'required',
            'channel' => 'required',
            'prev_talk_id' => '',
        ];
    }

    /**
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        $res = response()->json([
            'status' => 400,
            'error' => [
                'type' => 'RequestValidation',
                'message' => 'Request Validation Failed.',
                'validate_errors' => $validator->errors(),
            ],
        ], 400);
        throw new HttpResponseException($res);
    }
}

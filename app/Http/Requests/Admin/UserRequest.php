<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 利用者管理リクエスト
 * Class UserRequest
 * @package App\Http\Requests\Admin
 */
class UserRequest extends FormRequest
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
            $diff_rules['name'] = 'required|string|max:255|unique:lara_users,name,' . $this->route('user');
            $diff_rules['email'] = 'required|string|email|max:255|unique:lara_users,email,' . $this->route('user');
            $diff_rules['password'] = 'nullable|string|min:6|confirmed';
        } else {
            //create
            $diff_rules['name'] = 'required|string|max:255|unique:lara_users';
            $diff_rules['email'] = 'required|string|email|max:255|unique:lara_users';
            $diff_rules['password'] = 'required|string|min:6|confirmed';
        }
        return [
                'display_name' => 'required|string|max:255',
            ] + $diff_rules;
    }

    /**
     * 属性
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => __('ログインID'),
            'email' => __('メールアドレス'),
            'password' => __('パスワード'),
            'display_name' => __('表示名'),
        ];
    }
}
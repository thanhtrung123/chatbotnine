<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 権限管理リクエスト
 * Class RoleRequest
 * @package App\Http\Requests\Admin
 */
class RoleRequest extends FormRequest
{
    /**
     * Determine if the role is authorized to make this request.
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
            $diff_rules['name'] = 'required|string|max:255|unique:lib_roles,name,' . $this->route('role');
        } else {
            //create
            $diff_rules['name'] = 'required|string|max:255|unique:lib_roles';
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
            'name' => __('ロール名'),
            'display_name' => __('表示名'),
        ];
    }
}
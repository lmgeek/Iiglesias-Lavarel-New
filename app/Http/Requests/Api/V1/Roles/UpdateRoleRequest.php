<?php

namespace App\Http\Requests\Api\V1\Roles;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:roles,name,'.$this->route('role'),
            'guard_name' => 'sometimes|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }
}

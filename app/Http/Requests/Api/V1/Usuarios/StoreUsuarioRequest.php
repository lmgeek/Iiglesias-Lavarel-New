<?php

namespace App\Http\Requests\Api\V1\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'doc_number' => 'sometimes|string|max:255|unique:users,doc_number',
            'phone' => 'nullable|string|max:255',
            'sex' => 'nullable|string|max:2',
            'born_date' => 'nullable|string',
            'church' => 'nullable|string|max:255',
            'mentor' => 'nullable|string|max:255',
            'ministerial_range' => 'nullable|string|max:255',
            'celula' => 'nullable|integer',
            'lider_celula' => 'sometimes|in:Si,No',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id',
        ];
    }
}

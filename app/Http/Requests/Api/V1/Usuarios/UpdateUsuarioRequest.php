<?php

namespace App\Http\Requests\Api\V1\Usuarios;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,'.$this->route('user'),
            'doc_number' => 'sometimes|string|max:255|unique:users,doc_number,'.$this->route('user'),
            'phone' => 'nullable|string|max:255',
            'sex' => 'nullable|string|max:2',
            'born_date' => 'nullable|string',
            'church' => 'nullable|string|max:255',
            'mentor' => 'nullable|string|max:255',
            'ministerial_range' => 'nullable|string|max:255',
            'celula' => 'nullable|integer',
            'lider_celula' => 'sometimes|in:Si,No',
            'password' => 'sometimes|string|min:6|confirmed',
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id',
        ];
    }
}

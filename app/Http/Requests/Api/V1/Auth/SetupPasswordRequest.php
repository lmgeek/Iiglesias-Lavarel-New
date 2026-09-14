<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SetupPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname' => 'sometimes|required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\Config;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChurchConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'church_name' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|string',
            'favicon' => 'nullable|string',
            'login_bg' => 'nullable|string',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\Temas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classname' => 'sometimes|required|string|max:255',
            'image' => 'nullable|string',
            'youtube' => 'nullable|string|max:255',
        ];
    }
}

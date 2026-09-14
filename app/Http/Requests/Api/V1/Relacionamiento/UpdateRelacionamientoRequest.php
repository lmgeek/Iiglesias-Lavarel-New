<?php

namespace App\Http\Requests\Api\V1\Relacionamiento;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRelacionamientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'disciple_id' => 'sometimes|required|integer|exists:users,id',
            'f_meet' => 'sometimes|required|date',
            'suspended' => 'sometimes|in:Si,No',
            'why_suspended' => 'nullable|string',
            'theme_meetings_id' => 'nullable|integer|exists:meetings_themes,id',
            'other_theme' => 'nullable|string|max:255',
            'culminate' => 'nullable|string|max:255',
            'initiative' => 'nullable|string|max:255',
            'reading' => 'nullable|string|max:255',
            'testimonials' => 'nullable|string|max:255',
            'pray_together' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}

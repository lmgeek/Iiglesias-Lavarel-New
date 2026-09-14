<?php

namespace App\Http\Requests\Api\V1\Informes;

use Illuminate\Foundation\Http\FormRequest;

class StoreInformeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'f_meet' => 'required|date',
            'celula' => 'required|string|max:255',
            'suspended' => 'sometimes|in:Si,No',
            'why_suspended' => 'nullable|string',
            'lider' => 'required|string|max:255',
            'message_title' => 'nullable|string|max:255',
            'who_meet' => 'nullable|string|max:255',
            'format' => 'nullable|string|max:255',
            'people_qty' => 'nullable|integer|min:0',
            'new_people_qty' => 'nullable|integer|min:0',
            'mentoring' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
        ];
    }
}

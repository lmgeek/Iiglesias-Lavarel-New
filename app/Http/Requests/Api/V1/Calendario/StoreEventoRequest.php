<?php

namespace App\Http\Requests\Api\V1\Calendario;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|date|after_or_equal:start_date',
            'recurring_days' => 'nullable|array',
            'recurring_days.*' => 'integer|between:0,6',
            'color' => 'nullable|string|max:7',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
        'recurring_days',
        'color',
        'all_day',
        'location',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'recurring_end_date' => 'date',
        'recurring_days' => 'array',
        'all_day' => 'boolean',
        'color' => 'string',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

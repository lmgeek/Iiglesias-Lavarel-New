<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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

    public static function occurrencesInRange(Carbon $start, Carbon $end): Collection
    {
        return static::whereNull('deleted_at')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
                    ->orWhere(fn ($recurring) => $recurring->where('is_recurring', true)->where('start_date', '<=', $end->copy()->endOfDay()));
            })
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get()
            ->flatMap(fn (CalendarEvent $event) => $event->expandRecurring($start, $end))
            ->sortBy(fn (CalendarEvent $occurrence) => $occurrence->start_date?->format('Y-m-d').' '.($occurrence->start_time ?? '99:99'))
            ->values();
    }

    public function expandRecurring(Carbon $start, Carbon $end): Collection
    {
        if (! $this->is_recurring) {
            return collect([
                $this->cloneForDate($this->start_date),
            ])->filter(fn (CalendarEvent $occurrence) => $occurrence->start_date->between($start->copy()->startOfDay(), $end->copy()->endOfDay()));
        }

        $step = match ($this->recurring_frequency) {
            'daily' => fn (Carbon $date) => $date->addDay(),
            'weekly' => fn (Carbon $date) => $date->addWeek(),
            'biweekly' => fn (Carbon $date) => $date->addDays(14),
            'monthly' => fn (Carbon $date) => $date->addMonth(),
            'yearly' => fn (Carbon $date) => $date->addYear(),
            default => fn (Carbon $date) => $date->addDay(),
        };

        $occurrence = $this->start_date->copy();
        $stop = $this->recurring_end_date?->copy() ?? $end->copy();
        $rangeStart = $start->copy()->startOfDay();
        $rangeEnd = $end->copy()->endOfDay();
        $occurrences = collect();

        while ($occurrence->lte($stop)) {
            if ($occurrence->between($rangeStart, $rangeEnd)) {
                $occurrences->push($this->cloneForDate($occurrence));
            }

            $occurrence = $step($occurrence);

            if ($occurrence->gt($stop->copy()->addMonths(24))) {
                break;
            }
        }

        return $occurrences;
    }

    private function cloneForDate(Carbon $date): CalendarEvent
    {
        $clone = clone $this;
        $clone->start_date = $date->copy();

        return $clone;
    }
}

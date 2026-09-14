<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Relationship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
    private array $editRoles = ['Admin', 'Supervisor', 'Lider', 'Pastor'];

    public function index(Request $request)
    {
        $month = (int) ($request->get('month') ?? now()->month);
        $year = (int) ($request->get('year') ?? now()->year);

        $start = now()->create($year, $month, 1);

        if ($month < 1 || $month > 12 || $year < 2020 || $year > 2100) {
            $start = now()->startOfMonth();
            $month = $start->month;
            $year = $start->year;
        }

        $gridStart = $start->copy()->subDays($start->dayOfWeek); // lunes
        $gridEnd = $gridStart->copy()->addDays(41);

        $events = CalendarEvent::whereNull('deleted_at')
            ->where(function ($query) use ($gridStart, $gridEnd) {
                $query->whereBetween('start_date', [$gridStart, $gridEnd])
                    ->orWhere(fn ($recurring) => $recurring->where('is_recurring', true)->where('start_date', '<=', $gridEnd));
            })
            ->orderBy('start_date')
            ->get()
            ->flatMap(fn ($event) => $this->expandRecurring($event, $gridStart, $gridEnd))
            ->groupBy(fn ($event) => $event->start_date?->format('Y-m-d'));

        // Build 42 cells (6 weeks)
        $cells = [];
        for ($i = 0; $i < 42; $i++) {
            $date = $gridStart->copy()->addDays($i);
            $cells[] = [
                'date' => $date,
                'isCurrentMonth' => $date->month === $month,
                'isToday' => $date->isToday(),
                'dayNumber' => $date->day,
                'events' => $events[$date->format('Y-m-d')] ?? collect(),
            ];
        }

        $meetings = Relationship::with('disciple')
            ->where('mentor_id', auth()->id())
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->where('f_meet', '>=', now()->subDays(7))
            ->orderBy('f_meet')
            ->get()
            ->groupBy(fn ($r) => $r->f_meet?->format('Y-m-d'));

        return view('calendario.index', [
            'cells' => $cells,
            'events' => $events,
            'meetings' => $meetings,
            'month' => $month,
            'year' => $year,
            'monthName' => $start->locale('es')->translatedFormat('F Y'),
            'canEdit' => $this->canEdit(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeEdit();

        $data = $this->validateEvent($request);
        $data['created_by'] = Auth::id();
        $data['color'] = $data['color'] ?? '#c57125';
        $data['all_day'] = $request->boolean('all_day');
        $data['is_recurring'] = $request->boolean('is_recurring');

        CalendarEvent::create($data);

        return $this->backToCalendar(now()->create($data['start_date']), 'Evento creado correctamente');
    }

    public function update(Request $request, CalendarEvent $evento)
    {
        $this->authorizeEdit();

        $data = $this->validateEvent($request);
        $data['color'] = $data['color'] ?? '#c57125';
        $data['all_day'] = $request->boolean('all_day');
        $data['is_recurring'] = $request->boolean('is_recurring');

        $evento->update($data);

        return $this->backToCalendar(now()->create($request->start_date), 'Evento actualizado correctamente');
    }

    public function destroy(Request $request, CalendarEvent $evento)
    {
        $this->authorizeEdit();

        $monthYear = $evento->start_date?->format('Y-m');
        $evento->delete();

        return back()->with('success', 'Evento eliminado');
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,biweekly,weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|date|after_or_equal:start_date',
            'recurring_days' => 'nullable|array',
            'recurring_days.*' => 'integer|between:0,6',
            'color' => 'nullable|string|max:7',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
        ]);
    }

    private function expandRecurring(CalendarEvent $event, Carbon $start, Carbon $end): Collection
    {
        if (! $event->is_recurring) {
            return collect([$event]);
        }

        $step = match ($event->recurring_frequency) {
            'daily' => fn (Carbon $date) => $date->addDay(),
            'weekly' => fn (Carbon $date) => $date->addWeek(),
            'biweekly' => fn (Carbon $date) => $date->addDays(14),
            'monthly' => fn (Carbon $date) => $date->addMonth(),
            'yearly' => fn (Carbon $date) => $date->addYear(),
            default => fn (Carbon $date) => $date->addDay(),
        };

        $occurrence = $event->start_date->copy();
        $stop = $event->recurring_end_date?->copy() ?? $end->copy();
        $occurrences = collect();

        while ($occurrence->lte($stop)) {
            if ($occurrence->between($start, $end)) {
                $clone = clone $event;
                $clone->start_date = $occurrence->copy();
                $occurrences->push($clone);
            }

            $occurrence = $step($occurrence);

            if ($occurrence->gt($stop->copy()->addMonths(24))) {
                break;
            }
        }

        return $occurrences;
    }

    private function backToCalendar(Carbon $date, string $message)
    {
        return redirect()->route('calendario.index', [
            'month' => $date->month,
            'year' => $date->year,
        ])->with('success', $message);
    }

    private function canEdit(): bool
    {
        $user = Auth::user();

        return $user->roles->pluck('name')->intersect($this->editRoles)->isNotEmpty()
            || $user->can('Configuración.edit');
    }

    private function authorizeEdit(): void
    {
        abort_unless($this->canEdit(), 403, 'No tienes permisos para modificar eventos.');
    }
}

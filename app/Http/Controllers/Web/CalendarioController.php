<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\CalendarLog;
use App\Models\Relationship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
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

        $gridStart = $start->copy()->subDays($start->dayOfWeek); // domingo
        $gridEnd = $gridStart->copy()->addDays(41);

        $events = CalendarEvent::occurrencesInRange($gridStart, $gridEnd)
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
        $this->authorizeCreate();

        $data = $this->validateEvent($request);
        $data['created_by'] = Auth::id();
        $data['color'] = $data['color'] ?? '#c57125';
        $data['all_day'] = $request->boolean('all_day');
        $data['is_recurring'] = $request->boolean('is_recurring');

        $event = CalendarEvent::create($data);

        CalendarLog::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'action' => 'create',
            'details' => $this->formatDetails($event),
        ]);

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

        CalendarLog::create([
            'event_id' => $evento->id,
            'user_id' => Auth::id(),
            'action' => 'update',
            'details' => $this->formatDetails($evento->fresh()),
        ]);

        return $this->backToCalendar(now()->create($request->start_date), 'Evento actualizado correctamente');
    }

    public function destroy(Request $request, CalendarEvent $evento)
    {
        $this->authorizeDelete();

        CalendarLog::create([
            'event_id' => $evento->id,
            'user_id' => Auth::id(),
            'action' => 'delete',
            'details' => $this->formatDetails($evento),
        ]);

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

    private function backToCalendar(Carbon $date, string $message)
    {
        return redirect()->route('calendario.index', [
            'month' => $date->month,
            'year' => $date->year,
        ])->with('success', $message);
    }

    private function canEdit(): bool
    {
        return Auth::user()->can('Calendario.edit');
    }

    private function authorizeCreate(): void
    {
        abort_unless(Auth::user()->can('Calendario.create'), 403, 'No tienes permisos para agregar eventos.');
    }

    private function authorizeEdit(): void
    {
        abort_unless(Auth::user()->can('Calendario.edit'), 403, 'No tienes permisos para modificar eventos.');
    }

    private function authorizeDelete(): void
    {
        abort_unless(Auth::user()->can('Calendario.delete'), 403, 'No tienes permisos para eliminar eventos.');
    }

    private function formatDetails(CalendarEvent $event): string
    {
        $start = $event->start_date?->format('d/m/Y') ?? '';

        if ($event->start_time) {
            $start .= ' '.$event->start_time;
        }

        $parts = [$event->title ?: 'Sin título', $start];

        if ($event->location) {
            $parts[] = $event->location;
        }

        return implode(' · ', $parts);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Calendario\StoreEventoRequest;
use App\Http\Requests\Api\V1\Calendario\UpdateEventoRequest;
use App\Http\Resources\Api\V1\CalendarEventCollection;
use App\Http\Resources\Api\V1\CalendarEventResource;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index(Request $request)
    {
        $query = CalendarEvent::with('creator')->whereNull('deleted_at');

        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('start_date', [$request->start, $request->end]);
        }

        if ($request->has('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $perPage = $request->get('per_page', 50);
        $events = $query->orderBy('start_date')->paginate($perPage);

        return new CalendarEventCollection($events);
    }

    public function store(StoreEventoRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $event = CalendarEvent::create($data);

        return new CalendarEventResource($event);
    }

    public function show(CalendarEvent $evento)
    {
        return new CalendarEventResource($evento);
    }

    public function update(UpdateEventoRequest $request, CalendarEvent $evento)
    {
        $evento->update($request->validated());

        return new CalendarEventResource($evento);
    }

    public function destroy(CalendarEvent $evento)
    {
        $evento->delete();

        return response()->json(['message' => 'Evento eliminado']);
    }

    public function notifications(Request $request)
    {
        // Lógica para notificaciones de eventos próximos
        return response()->json([]);
    }
}

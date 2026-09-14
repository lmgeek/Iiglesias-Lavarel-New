<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Intercesion;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;

class OracionController extends Controller
{
    public function index()
    {
        $intercesiones = Intercesion::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('calendar_day')
            ->get();

        $prayerRequests = PrayerRequest::whereNull('deleted_at')
            ->orderBy('is_answered')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('oracion.index', [
            'intercesiones' => $intercesiones,
            'prayerRequests' => $prayerRequests,
        ]);
    }

    public function lista()
    {
        $intercesiones = Intercesion::whereNull('deleted_at')
            ->orderBy('calendar_day')
            ->paginate(15);

        return view('oracion.lista', ['intercesiones' => $intercesiones]);
    }

    public function storePedido(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'request' => 'required|string|max:2000',
        ]);

        $data['user_id'] = auth()->id();

        PrayerRequest::create($data);

        return back()->with('success', 'Pedido de oración registrado correctamente');
    }

    public function togglePedido(Request $request, PrayerRequest $pedido)
    {
        $pedido->update([
            'is_answered' => ! $pedido->is_answered,
            'answered_at' => $pedido->is_answered ? null : now(),
        ]);

        return back()->with('success', $pedido->is_answered
            ? 'Pedido marcado como respondido'
            : 'Pedido marcado como pendiente');
    }
}

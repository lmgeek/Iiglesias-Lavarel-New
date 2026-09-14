<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\MeetingsTheme;
use App\Models\Relationship;
use App\Models\ReportCelula;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalDiscipulos = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->count();

        $twelveDaysAgo = now()->subDays(12);
        $relacionesVencidas = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->where('f_meet', '<', $twelveDaysAgo)
            ->count();

        $elevenDaysAgo = now()->subDays(11);
        $eightDaysAgo = now()->subDays(8);
        $relacionesPorVencer = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->whereBetween('f_meet', [$eightDaysAgo, $elevenDaysAgo])
            ->count();

        $informesEsteMes = ReportCelula::where('mentor_id', $user->id)
            ->whereMonth('f_meet', now()->month)
            ->whereYear('f_meet', now()->year)
            ->whereNull('deleted_at')
            ->count();

        $ultimoTema = MeetingsTheme::whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->first();

        $weekStart = now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();

        $semanaEventos = CalendarEvent::occurrencesInRange($weekStart, $weekEnd);

        return view('dashboard.index', [
            'stats' => [
                'totalDiscipulos' => $totalDiscipulos,
                'relacionesVencidas' => $relacionesVencidas,
                'relacionesPorVencer' => $relacionesPorVencer,
                'informesEsteMes' => $informesEsteMes,
            ],
            'ultimoTema' => $ultimoTema,
            'semanaEventos' => $semanaEventos,
            'semanaInicio' => $weekStart,
            'semanaFin' => $weekEnd->copy()->addDays(6)->startOfDay(),
        ]);
    }
}

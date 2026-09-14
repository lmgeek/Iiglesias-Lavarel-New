<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MeetingsTheme;
use App\Models\Relationship;
use App\Models\ReportCelula;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

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

        return response()->json([
            'totalDiscipulos' => $totalDiscipulos,
            'relacionesVencidas' => $relacionesVencidas,
            'relacionesPorVencer' => $relacionesPorVencer,
            'informesEsteMes' => $informesEsteMes,
            'ultimoTema' => $ultimoTema,
        ]);
    }
}

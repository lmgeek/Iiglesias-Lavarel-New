<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Relationship;
use App\Models\ReportCelula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Stats del dashboard
        $myDisciples = Relationship::where('mentor_id', $user->id)->whereNull('deleted_at')->count();
        $overdueRelationships = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->where('f_meet', '<', now()->subDays(12))
            ->count();

        $pendingRelationships = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->where('f_meet', '>=', now()->subDays(12))
            ->where('f_meet', '<', now()->subDays(8))
            ->count();

        $myReportsThisMonth = ReportCelula::where('mentor_id', $user->id)
            ->whereMonth('f_meet', now()->month)
            ->whereYear('f_meet', now()->year)
            ->whereNull('deleted_at')
            ->count();

        // Alertas
        $alerts = [];
        if ($overdueRelationships > 0) {
            $alerts[] = "Tienes {$overdueRelationships} relacionamientos vencidos";
        }
        if ($pendingRelationships > 0) {
            $alerts[] = "Tienes {$pendingRelationships} relacionamientos por vencer";
        }

        // Último tema
        $lastTheme = DB::table('meetings_themes')
            ->join('relationships', 'relationships.theme_meetings_id', '=', 'meetings_themes.id')
            ->where('relationships.mentor_id', $user->id)
            ->whereNull('relationships.deleted_at')
            ->orderBy('relationships.f_meet', 'desc')
            ->select('meetings_themes.classname', 'relationships.f_meet')
            ->first();

        return response()->json([
            'totalDiscipulos' => $myDisciples,
            'relacionesVencidas' => $overdueRelationships,
            'relacionesPorVencer' => $pendingRelationships,
            'informesEsteMes' => $myReportsThisMonth,
            'alertas' => $alerts,
            'ultimoTema' => $lastTheme,
        ]);
    }
}

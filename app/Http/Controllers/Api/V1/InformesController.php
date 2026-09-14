<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Informes\StoreInformeRequest;
use App\Http\Resources\Api\V1\ReportCelulaCollection;
use App\Http\Resources\Api\V1\ReportCelulaResource;
use App\Models\ReportCelula;
use Illuminate\Http\Request;

class InformesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = ReportCelula::with('mentor')
            ->where('mentor_id', $user->id)
            ->whereNull('deleted_at');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('f_meet', [$request->start_date, $request->end_date]);
        }

        $perPage = $request->get('per_page', 15);
        $reports = $query->orderBy('f_meet', 'desc')->paginate($perPage);

        return new ReportCelulaCollection($reports);
    }

    public function store(StoreInformeRequest $request)
    {
        $data = $request->validated();
        $data['mentor_id'] = $request->user()->id;

        $report = ReportCelula::create($data);

        return new ReportCelulaResource($report);
    }

    public function show(Request $request, ReportCelula $informe)
    {
        if ($informe->mentor_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return new ReportCelulaResource($informe);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = $request->user();
        $reports = ReportCelula::with('mentor')
            ->where('mentor_id', $user->id)
            ->whereBetween('f_meet', [$request->start_date, $request->end_date])
            ->orderBy('f_meet', 'desc')
            ->get();

        return response()->json([
            'reports' => $reports,
            'total' => $reports->count(),
            'period' => [
                'start' => $request->start_date,
                'end' => $request->end_date,
            ],
        ]);
    }
}

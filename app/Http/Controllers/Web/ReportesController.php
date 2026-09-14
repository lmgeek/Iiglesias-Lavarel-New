<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Relationship;
use App\Models\ReportCelula;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $this->authorizeView();

        $from = $request->get('from') ?? now()->startOfYear()->format('Y-m-d');
        $to = $request->get('to') ?? now()->format('Y-m-d');

        $reportQuery = ReportCelula::with('mentor')
            ->whereNull('deleted_at')
            ->whereBetween('f_meet', [$from, $to]);

        $reports = $reportQuery->orderBy('f_meet', 'desc')->get();

        $overdue = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->where('f_meet', '<', now()->subDays(12))
            ->count();

        $pending = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->whereBetween('f_meet', [now()->subDays(12), now()->subDays(8)])
            ->count();

        $reportsThisMonth = ReportCelula::where('mentor_id', $user->id)
            ->whereMonth('f_meet', now()->month)
            ->whereYear('f_meet', now()->year)
            ->whereNull('deleted_at')
            ->count();

        $reportsByMonth = ReportCelula::whereNull('deleted_at')
            ->where('f_meet', '>=', now()->startOfYear())
            ->get()
            ->groupBy(fn ($r) => $r->f_meet?->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $relacionesByMonth = Relationship::whereNull('deleted_at')
            ->where('f_meet', '>=', now()->startOfYear())
            ->get()
            ->groupBy(fn ($r) => $r->f_meet?->format('Y-m'))
            ->map(fn ($group) => $group->count());

        $chartData = [];
        for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++) {
            $key = now()->format('Y').'-'.str_pad((string) $monthNumber, 2, '0', STR_PAD_LEFT);

            $chartData[] = [
                'label' => Carbon::createFromDate(now()->year, $monthNumber, 1)->locale('es')->isoFormat('MMM'),
                'conexion' => $reportsByMonth[$key] ?? 0,
                'discipulado' => $relacionesByMonth[$key] ?? 0,
            ];
        }

        $totalConexion = ReportCelula::whereNull('deleted_at')->count();
        $totalDiscipulos = Relationship::whereNull('deleted_at')->count();

        $alerts = [];
        if ($overdue > 0) {
            $alerts[] = "Tienes {$overdue} relacionamientos vencidos";
        }
        if ($pending > 0) {
            $alerts[] = "Tienes {$pending} relacionamientos por vencer";
        }

        // Secciones por mentor
        $mentorStats = $reports->groupBy(fn ($r) => $r->mentor_id)
            ->map(function ($group) {
                $mentor = $group->first()->mentor;

                return [
                    'mentor' => $mentor,
                    'total' => $group->count(),
                    'suspendidos' => $group->where('suspended', 'Si')->count(),
                    'concretados' => $group->where('suspended', '!=', 'Si')->count(),
                    'personas' => $group->sum('people_qty'),
                    'nuevos' => $group->sum('new_people_qty'),
                    'reports' => $group->sortByDesc('f_meet'),
                ];
            })
            ->sortByDesc('total');

        return view('reportes.index', [
            'totalDiscipulos' => $totalDiscipulos,
            'relacionesVencidas' => $overdue,
            'relacionesPorVencer' => $pending,
            'informesEsteMes' => $reportsThisMonth,
            'alertas' => $alerts,
            'reportsByMonth' => $reportsByMonth,
            'chartData' => $chartData,
            'totalConexion' => $totalConexion,
            'mentorStats' => $mentorStats,
            'from' => $from,
            'to' => $to,
            'totalPeriodo' => $reports->count(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorizeView();

        $from = $request->get('from') ?? now()->startOfYear()->format('Y-m-d');
        $to = $request->get('to') ?? now()->format('Y-m-d');

        $reports = ReportCelula::with('mentor')
            ->whereNull('deleted_at')
            ->whereBetween('f_meet', [$from, $to])
            ->orderBy('f_meet', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Fecha', 'Mentor', 'Célula', 'Suspendido', 'Motivo Suspensión',
            'Líder', 'Título', 'Quién Reunió', 'Formato', 'Cantidad Personas',
            'Nuevas Personas', 'Mentoreo', 'Observaciones',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:'.chr(64 + count($headers)).'1')->getFont()->setBold(true);

        $row = 2;
        foreach ($reports as $report) {
            $sheet->fromArray([
                $report->f_meet?->format('d/m/Y'),
                $report->mentor?->fullname,
                $report->celula,
                $report->suspended,
                $report->why_suspended,
                $report->lider,
                $report->message_title,
                $report->who_meet,
                $report->format,
                $report->people_qty,
                $report->new_people_qty,
                $report->mentoring,
                $report->observations,
            ], null, 'A'.$row);
            $row++;
        }

        foreach (range('A', chr(64 + count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'informes_'.$from.'_a_'.$to.'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    public function conexion(Request $request)
    {
        $this->authorizeView();

        $query = ReportCelula::with('mentor')->whereNull('deleted_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('f_meet', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('celula', 'like', "%{$search}%")
                    ->orWhere('lider', 'like', "%{$search}%")
                    ->orWhere('message_title', 'like', "%{$search}%")
                    ->orWhereHas('mentor', fn ($m) => $m->where('fullname', 'like', "%{$search}%"));
            });
        }

        $reports = $query->orderBy('f_meet', 'desc')->paginate(15)->withQueryString();

        return view('reportes.conexion', [
            'reports' => $reports,
            'total' => ReportCelula::whereNull('deleted_at')->count(),
            'thisMonth' => ReportCelula::whereNull('deleted_at')
                ->whereMonth('f_meet', now()->month)
                ->whereYear('f_meet', now()->year)
                ->count(),
        ]);
    }

    public function discipulado(Request $request)
    {
        $this->authorizeView();

        $query = Relationship::with(['disciple', 'mentor', 'theme'])->whereNull('deleted_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('f_meet', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = mb_strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereHas('disciple', fn ($d) => $d->whereRaw('LOWER(fullname) LIKE ?', ["%{$search}%"]))
                    ->orWhereHas('mentor', fn ($m) => $m->whereRaw('LOWER(fullname) LIKE ?', ["%{$search}%"]));
            });
        }

        $relationships = $query->orderBy('f_meet', 'desc')->paginate(15)->withQueryString();

        return view('reportes.discipulado', [
            'relationships' => $relationships,
            'total' => Relationship::whereNull('deleted_at')->count(),
            'thisMonth' => Relationship::whereNull('deleted_at')
                ->whereMonth('f_meet', now()->month)
                ->whereYear('f_meet', now()->year)
                ->count(),
        ]);
    }

    private function authorizeView(): void
    {
        abort_unless(auth()->user()->can('Reportes.view'), 403, 'No tienes permisos para ver reportes.');
    }
}

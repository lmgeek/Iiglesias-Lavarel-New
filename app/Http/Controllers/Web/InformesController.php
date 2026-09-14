<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ReportCelula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InformesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ReportCelula::with('mentor')
            ->where('mentor_id', $user->id)
            ->whereNull('deleted_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('f_meet', [$request->start_date, $request->end_date]);
        }

        $reports = $query->orderBy('f_meet', 'desc')->paginate(15)->withQueryString();

        return view('informes.index', [
            'reports' => $reports,
            'total' => ReportCelula::where('mentor_id', $user->id)->whereNull('deleted_at')->count(),
            'thisMonth' => ReportCelula::where('mentor_id', $user->id)
                ->whereMonth('f_meet', now()->month)
                ->whereYear('f_meet', now()->year)
                ->whereNull('deleted_at')
                ->count(),
        ]);
    }

    public function create()
    {
        return view('informes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'f_meet' => 'required|date',
            'celula' => 'nullable|string|max:255',
            'suspended' => 'nullable|in:Si,No',
            'why_suspended' => 'nullable|string',
            'lider' => 'nullable|string|max:255',
            'message_title' => 'nullable|string|max:255',
            'who_meet' => 'nullable|string',
            'format' => 'nullable|string|max:255',
            'people_qty' => 'nullable|integer|min:0',
            'new_people_qty' => 'nullable|integer|min:0',
            'mentoring' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $data['mentor_id'] = Auth::id();

        ReportCelula::create($data);

        return redirect()->route('informes.index')
            ->with('success', 'Informe registrado correctamente');
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\ChurchConfig;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MiembrosController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('mentorUser', 'roles')
            ->whereNotNull('church')
            ->whereNull('deleted_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('doc_number', 'like', "%{$search}%")
                    ->orWhere('church', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('fullname')->paginate(15)->withQueryString();

        return view('miembros.index', [
            'members' => $members,
            'total' => User::whereNotNull('church')->whereNull('deleted_at')->count(),
        ]);
    }

    public function create()
    {
        $sedes = Sede::whereNull('deleted_at')->orderBy('name')->get();

        return view('miembros.create', [
            'sedes' => $sedes,
            'church' => ChurchConfig::getConfig()->church_name,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'doc_number' => 'required|string|max:255|unique:users,doc_number',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'phone' => 'required|string|max:255',
            'born_date' => 'required|date',
            'sex' => 'required|string|in:M,F',
            'sede_id' => 'required|integer|exists:sedes,id',
            'is_active' => 'boolean',
            'lider_celula' => ['required', 'string', Rule::in(['Si', 'No'])],
        ]);

        $sede = Sede::whereNull('deleted_at')->findOrFail($data['sede_id']);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['lider_celula'] = $data['lider_celula'] ?? 'No';
        $data['church'] = $sede->name;
        $data['uuid'] = (string) Str::uuid();

        $password = Str::password(12, true, true, false);
        $data['password'] = $password;
        $data['must_change_password'] = true;

        $user = User::create($data);

        try {
            Mail::to($user->email)->send(new WelcomeMail($user, $password));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('miembros.index')
            ->with('success', 'Miembro registrado correctamente. Se envió un correo de bienvenida con sus datos de acceso.');
    }
}

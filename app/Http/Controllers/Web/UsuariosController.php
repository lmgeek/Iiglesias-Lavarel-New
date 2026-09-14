<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'ministry', 'sede')->whereNull('deleted_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('fullname')->paginate(15)->withQueryString();

        return view('usuarios.index', [
            'users' => $users,
            'total' => User::whereNull('deleted_at')->count(),
            'active' => User::whereNull('deleted_at')->where('is_active', true)->count(),
        ]);
    }

    public function edit(User $user)
    {
        $user->load('roles');

        return view('usuarios.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
            'ministries' => Ministry::orderBy('name')->get(),
            'sedes' => Sede::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'doc_number' => [
                'nullable', 'string', 'max:255',
                Rule::unique('users', 'doc_number')
                    ->whereNull('deleted_at')
                    ->ignore($user->id),
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:255',
            'born_date' => 'nullable|date',
            'sex' => 'nullable|string|max:2',
            'church' => 'nullable|string|max:255',
            'ministry_id' => 'nullable|integer|exists:ministries,id',
            'sede_id' => 'nullable|integer|exists:sedes,id',
            'lider_celula' => ['nullable', 'string', Rule::in(['Si', 'No'])],
            'is_active' => 'boolean',
            'roles' => 'sometimes|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['lider_celula'] = $data['lider_celula'] ?? 'No';
        unset($data['roles']);

        $user->update($data);

        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario');
        }

        $user->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente');
    }
}

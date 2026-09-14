<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class LideresController extends Controller
{
    public function index()
    {
        $leaders = User::with('ministry', 'roles')
            ->where('is_leader', true)
            ->whereNull('deleted_at')
            ->orderBy('fullname')
            ->get();

        $candidates = User::where('is_leader', false)
            ->whereNull('deleted_at')
            ->orderBy('fullname')
            ->get();

        $ministries = Ministry::whereNull('deleted_at')->orderBy('name')->get();

        return view('lideres.index', [
            'leaders' => $leaders,
            'candidates' => $candidates,
            'ministries' => $ministries,
            'canManage' => $this->canManage(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'ministry_id' => 'required|integer|exists:ministries,id',
        ]);

        $user = User::findOrFail($data['user_id']);

        if ($user->is_leader) {
            return back()->with('error', 'Esta persona ya es líder de un ministerio.');
        }

        $user->update([
            'is_leader' => true,
            'ministry_id' => $data['ministry_id'],
        ]);

        $role = Role::findByName('Lider', 'web');
        if ($role && ! $user->hasRole('Lider')) {
            $user->assignRole($role);
        }

        return back()->with('success', "{$user->fullname} ahora es líder del ministerio «{$user->ministry?->name}».");
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'ministry_id' => 'required|integer|exists:ministries,id',
        ]);

        $oldMinistry = $user->ministry?->name ?? 'Sin ministerio';
        $user->update(['ministry_id' => $data['ministry_id']]);

        return back()->with('success', "{$user->fullname} ahora lidera «{$user->ministry?->name}» (antes: {$oldMinistry}).");
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeManage();

        $name = $user->fullname;
        $user->update(['is_leader' => false]);

        if ($user->hasRole('Lider')) {
            $user->removeRole('Lider');
        }

        return back()->with('success', "{$name} ya no es líder de ningún ministerio.");
    }

    private function canManage(): bool
    {
        return in_array(optional(auth()->user()->roles->first())->name, ['Admin', 'Supervisor', 'Pastor']);
    }

    private function authorizeManage(): void
    {
        if (! $this->canManage()) {
            abort(403, 'No tienes permisos para gestionar líderes.');
        }
    }
}
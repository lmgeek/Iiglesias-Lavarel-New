<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Usuarios\StoreUsuarioRequest;
use App\Http\Requests\Api\V1\Usuarios\UpdateUsuarioRequest;
use App\Http\Resources\Api\V1\UserCollection;
use App\Http\Resources\Api\V1\UserResource;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'ministry'])->whereNull('deleted_at');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('doc_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->orderBy('fullname')->paginate($perPage);

        return new UserCollection($users);
    }

    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();
        $data['uuid'] = (string) Str::uuid();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['must_change_password'] = true;

        $user = User::create($data);

        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->assignRole($roles);
        }

        Mail::to($user->email)->send(new WelcomeMail($user));

        $user->load('roles');

        return new UserResource($user);
    }

    public function show(User $user)
    {
        $user->load('roles', 'ministry');

        return new UserResource($user);
    }

    public function update(UpdateUsuarioRequest $request, User $user)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);
        }

        $user->load('roles');

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }
}

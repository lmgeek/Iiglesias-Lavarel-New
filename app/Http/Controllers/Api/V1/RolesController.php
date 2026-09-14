<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Roles\StoreRoleRequest;
use App\Http\Requests\Api\V1\Roles\UpdateRoleRequest;
use App\Http\Resources\Api\V1\RoleCollection;
use App\Http\Resources\Api\V1\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate($request->get('per_page', 15));

        return new RoleCollection($roles);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->givePermissionTo($permissions);
        }

        $role->load('permissions');

        return new RoleResource($role);
    }

    public function show(Role $role)
    {
        $role->load('permissions');

        return new RoleResource($role);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        $role->load('permissions');

        return new RoleResource($role);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(['message' => 'Rol eliminado']);
    }
}

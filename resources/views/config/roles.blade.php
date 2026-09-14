@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Roles</h1>
        <p class="page-sub">Roles del sistema y sus permisos asignados</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success mb-24">{{ session('success') }}</div>
@endif

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado de roles</h2>
            <p class="card-sub">Marcá los permisos que querés asignar a cada rol</p>
        </div>
    </div>

    @if ($roles->isEmpty())
        <div class="empty">
            <div class="empty-title">Sin roles</div>
            <p>No hay roles configurados.</p>
        </div>
    @else
        @foreach ($roles as $role)
            <div style="padding:16px 0;border-bottom:1px solid var(--border-light)">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <span class="avatar" style="background:rgba(212,138,48,0.15);color:var(--cat-700)" >{{ mb_substr($role->name, 0, 1) }}</span>
                        <div>
                            <div style="font-weight:500">{{ $role->name }}</div>
                            <div style="font-size:12px;color:var(--text-tertiary)">{{ $role->permissions->count() }} permisos</div>
                        </div>
                    </div>
                    <span class="badge badge-gray">guard: {{ $role->guard_name }}</span>
                </div>

                <form method="POST" action="{{ route('config.roles.permissions', $role) }}">
                    @csrf
                    @if ($role->name === 'Admin')
                        <div style="font-size:12.5px;color:var(--text-tertiary)">Administrador: tiene acceso total a todos los permisos del sistema.</div>
                    @else
                        <div class="form-group">
                            <label style="display:block;margin-bottom:8px;font-weight:500;color:var(--text-secondary)">Permisos del rol</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px">
                                @foreach ($permissions as $permission)
                                    <label class="badge" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;padding:7px 11px;border:1px solid var(--border-light);user-select:none;font-weight:500">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, $role->permissions->pluck('id')->all())) style="width:13px;height:13px">
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-8">
                            <button type="submit" class="btn btn-sm btn-secondary">Guardar permisos</button>
                        </div>
                    @endif
                </form>
            </div>
        @endforeach
    @endif
</div>
@endsection
@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Roles</h1>
        <p class="page-sub">Roles del sistema y sus permisos asignados</p>
    </div>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado de roles</h2>
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
                @if ($role->permissions->isNotEmpty())
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        @foreach ($role->permissions as $permission)
                            <span class="badge badge-green" style="font-size:11px">{{ $permission->name }}</span>
                        @endforeach
                    </div>
                @else
                    <div style="font-size:12.5px;color:var(--text-tertiary)">Sin permisos asignados</div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
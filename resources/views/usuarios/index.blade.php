@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Usuarios</h1>
        <p class="page-sub">{{ $active }} activos de {{ $total }} usuarios totales</p>
    </div>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado de usuarios</h2>
        </div>
        <form method="GET" action="{{ route('usuarios.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o email..." class="search-input">
        </form>
    </div>

    @if ($users->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="empty-title">Sin usuarios</div>
            <p>No hay usuarios registrados.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Ministerio</th>
                        <th>Sede</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="avatar avatar-sm">{{ mb_substr($user->fullname ?? 'U', 0, 1) }}</span>
                                    <div>
                                        <div style="font-weight:500">{{ $user->fullname }}</div>
                                        <div class="td-muted" style="font-size:12px">{{ $user->doc_number ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="td-muted">{{ $user->email }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="badge badge-cat">{{ $role->name }}</span>
                                @empty
                                    <span class="badge badge-gray">Sin rol</span>
                                @endforelse
                            </td>
                            <td>{{ $user->ministry?->name ?? '—' }}</td>
                            <td>{{ $user->sede?->name ?? '—' }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge badge-green">Activo</span>
                                @else
                                    <span class="badge badge-gray">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:8px">
                                    <a href="{{ route('usuarios.edit', $user) }}" class="btn btn-sm btn-secondary" title="Editar usuario">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('usuarios.destroy', $user) }}" onsubmit="return confirm('¿Eliminar a {{ addslashes($user->fullname) }}? Esta acción es reversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar usuario">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16" style="display:flex;justify-content:center">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
@extends('layouts.app')

@section('title', 'Intercesores')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Intercesores</h1>
        <p class="page-sub">Todos los registros de la cadena de oración</p>
    </div>
    <a href="{{ route('oracion.index') }}" class="btn btn-secondary">Cadena activa</a>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Registro completo</h2>
        </div>
    </div>

    @if ($intercesiones->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="empty-title">Sin intercesores</div>
            <p>Aún no hay registros en la cadena de oración.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Intercesor</th>
                        <th>Email</th>
                        <th>Notificaciones</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($intercesiones as $intercesion)
                        <tr>
                            <td><span class="badge badge-cat">Día {{ $intercesion->calendar_day }}</span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="avatar avatar-sm">{{ mb_substr($intercesion->email ?? 'I', 0, 1) }}</span>
                                    <div style="font-weight:500">{{ $intercesion->email ?? 'Intercesor' }}</div>
                                </div>
                            </td>
                            <td>{{ $intercesion->email }}</td>
                            <td>
                                @if ($intercesion->notifications)
                                    <span class="badge badge-green">Activadas</span>
                                @else
                                    <span class="badge badge-gray">Desactivadas</span>
                                @endif
                            </td>
                            <td>
                                @if ($intercesion->is_active)
                                    <span class="badge badge-green">Activo</span>
                                @else
                                    <span class="badge badge-gray">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16" style="display:flex;justify-content:center">
            {{ $intercesiones->links() }}
        </div>
    @endif
</div>
@endsection
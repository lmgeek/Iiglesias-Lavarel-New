@extends('layouts.app')

@section('title', 'Informes de grupos de conexión')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Informes de grupos de conexión</h1>
        <p class="page-sub">Todos los informes registrados en el sistema</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total informes</div>
        <div class="stat-value stat-tone-default">{{ $total }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Este mes</div>
        <div class="stat-value stat-tone-cat">{{ $thisMonth }}</div>
    </div>
</div>

<form method="GET" action="{{ route('reportes.conexion') }}" class="card animate-fade-in-up mb-24">
    <div class="form-row" style="flex-wrap:wrap">
        <div class="form-group">
            <label for="start_date">Desde</label>
            <input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}" class="input">
        </div>
        <div class="form-group">
            <label for="end_date">Hasta</label>
            <input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}" class="input">
        </div>
        <div class="form-group">
            <label for="search">Buscar</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Líder, célula o título..." class="input">
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
        </div>
    </div>
</form>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado</h2>
            <p class="card-sub">Informes de todos los mentores</p>
        </div>
    </div>

    @if ($reports->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="empty-title">Sin informes</div>
            <p>No hay informes registrados con los filtros indicados.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Mentor</th>
                        <th>Líder</th>
                        <th>Mensaje</th>
                        <th>Asistentes</th>
                        <th>Nuevos</th>
                        <th>Suspensión</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->f_meet?->format('d/m/Y') }}</td>
                            <td>{{ $report->mentor?->fullname ?? '—' }}</td>
                            <td>{{ $report->lider ?? '—' }}</td>
                            <td>{{ $report->message_title ?? '—' }}</td>
                            <td>{{ $report->people_qty ?? 0 }}</td>
                            <td>{{ $report->new_people_qty ?? 0 }}</td>
                            <td>
                                @if ($report->suspended === 'Si')
                                    <span class="badge badge-gray">Suspendida</span>
                                @else
                                    <span class="badge badge-green">Activa</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16" style="display:flex;justify-content:center">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
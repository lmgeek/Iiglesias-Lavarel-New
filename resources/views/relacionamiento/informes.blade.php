@extends('layouts.app')

@section('title', 'Informes de discipulado')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Informes de discipulado</h1>
        <p class="page-sub">Informes de tus discipulados</p>
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

<form method="GET" action="{{ route('relacionamiento.informes') }}" class="card animate-fade-in-up mb-24">
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
            <label for="search">Discípulo</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre..." class="input">
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
            <p class="card-sub">Todos tus informes de discipulado registrados</p>
        </div>
    </div>

    @if ($relationships->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="empty-title">Sin informes</div>
            <p>Registra tu primer informe de discipulado.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Discípulo</th>
                        <th>Tema</th>
                        <th>Estado</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($relationships as $rel)
                        @php
                            $isOverdue = $rel->f_meet && $rel->f_meet->lt(now()->subDays(12));
                        @endphp
                        <tr>
                            <td>{{ $rel->f_meet?->format('d/m/Y') }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="avatar avatar-sm">{{ mb_substr($rel->disciple?->fullname ?? '?', 0, 1) }}</span>
                                    <div>
                                        <div style="font-weight:500">{{ $rel->disciple?->fullname ?? 'Sin usuario' }}</div>
                                        <div class="td-muted" style="font-size:12px">{{ $rel->disciple?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $rel->theme?->name ?? ($rel->other_theme ?: 'Sin tema') }}</td>
                            <td>
                                @if ($rel->suspended === 'Si')
                                    <span class="badge badge-gray">Suspendido</span>
                                @elseif ($isOverdue)
                                    <span class="badge badge-red">Vencido</span>
                                @else
                                    <span class="badge badge-green">Vigente</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <a href="{{ route('relacionamiento.informe.create', $rel->disciple_id) }}" class="btn btn-primary btn-sm">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    Nuevo informe
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16" style="display:flex;justify-content:center">
            {{ $relationships->links() }}
        </div>
    @endif
</div>
@endsection
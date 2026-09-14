@extends('layouts.app')

@section('title', 'Log de calendario')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Log de calendario</h1>
        <p class="page-sub">Quién agregó, modificó o eliminó eventos del calendario</p>
    </div>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Actividad reciente</h2>
        </div>
    </div>

    @if ($logs->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="empty-title">Sin actividad</div>
            <p>Todavía no hay registros de eventos del calendario.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Evento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td style="white-space:nowrap">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span class="avatar avatar-sm" style="flex-shrink:0">{{ mb_substr($log->user?->fullname ?? '?', 0, 1) }}</span>
                                    <div>
                                        <div style="font-weight:500">{{ $log->user?->fullname ?? 'Usuario eliminado' }}</div>
                                        <div class="td-muted" style="font-size:12px">{{ $log->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($log->action === 'create')
                                    <span class="badge badge-green">Agregó</span>
                                @elseif ($log->action === 'update')
                                    <span class="badge badge-cat">Modificó</span>
                                @else
                                    <span class="badge badge-red">Eliminó</span>
                                @endif
                            </td>
                            <td style="max-width:340px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $log->details }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16" style="display:flex;justify-content:center">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
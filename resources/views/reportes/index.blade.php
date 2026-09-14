@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Reportes</h1>
        <p class="page-sub">Resumen de informes por mentor y descarga en Excel</p>
    </div>
</div>

@if (!empty($alertas))
    <div class="alert alert-error" style="margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:10px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div>
                @foreach ($alertas as $alerta)
                    <div>{{ $alerta }}</div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total discípulos</div>
        <div class="stat-value stat-tone-default">{{ $totalDiscipulos }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Relaciones vencidas</div>
        <div class="stat-value stat-tone-ember">{{ $relacionesVencidas }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Por vencer</div>
        <div class="stat-value stat-tone-cat">{{ $relacionesPorVencer }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Informes este mes</div>
        <div class="stat-value stat-tone-sacred">{{ $informesEsteMes }}</div>
    </div>
</div>

<div class="report-cards">
    <div class="card animate-fade-in-up report-card">
        <div class="report-access">
            <div class="report-access-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div style="flex:1;min-width:160px">
                <h2 class="card-title">Informes de grupo de conexión</h2>
                <p class="card-sub">{{ $totalConexion }} informes de todo el sistema</p>
            </div>
            <a href="{{ route('reportes.conexion') }}" class="btn btn-primary">Ver informes</a>
        </div>
    </div>
    <div class="card animate-fade-in-up report-card">
        <div class="report-access">
            <div class="report-access-icon" style="background:var(--sacred-600)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg>
            </div>
            <div style="flex:1;min-width:160px">
                <h2 class="card-title">Informes de discipulado</h2>
                <p class="card-sub">{{ $totalDiscipulos }} informes de todo el sistema</p>
            </div>
            <a href="{{ route('reportes.discipulado') }}" class="btn btn-primary">Ver informes</a>
        </div>
    </div>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Informes por mes (año actual)</h2>
            <p class="card-sub">Cantidad de informes registrados por mes</p>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" id="reportChartToggle">
            <svg id="reportChartToggleIcon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
            <span id="reportChartToggleText">Mostrar gráficas</span>
        </button>
    </div>

    @if ($reportsByMonth->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18"/><path d="M8 17v-4M13 17V9M18 17V5"/></svg>
            </div>
            <div class="empty-title">Sin datos este año</div>
            <p>Los informes registrados aparecerán aquí mes a mes.</p>
        </div>
    @else
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            @foreach ($reportsByMonth as $month => $count)
                <div style="flex:1;min-width:90px;background:var(--bg-tertiary);border-radius:12px;padding:14px;text-align:center">
                    <div style="font-size:22px;font-weight:500;font-family:'Jost',sans-serif;color:var(--cat-700)">{{ $count }}</div>
                    <div style="font-size:12px;color:var(--text-tertiary);text-transform:capitalize;margin-top:2px">
                        {{ \Carbon\Carbon::parse($month . '-01')->locale('es')->translatedFormat('M') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @php($chartMax = max(1, ...array_merge(array_column($chartData, 'conexion'), array_column($chartData, 'discipulado'))))
    <div id="reportCharts" class="report-charts" style="display:none">
        <div class="report-chart">
            <div class="report-chart-title">
                <span class="report-chart-dot" style="background:var(--cat-600)"></span>
                Grupo de conexión
            </div>
            <div class="report-chart-bars">
                @foreach ($chartData as $data)
                    <div class="report-chart-col" title="{{ $data['label'] }}: {{ $data['conexion'] }}">
                        <span class="report-chart-num">{{ $data['conexion'] }}</span>
                        <div class="report-chart-bar" style="height:{{ round(($data['conexion'] / $chartMax) * 100) }}%;background:var(--cat-600)"></div>
                        <span class="report-chart-label">{{ $data['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="report-chart">
            <div class="report-chart-title">
                <span class="report-chart-dot" style="background:var(--sacred-600)"></span>
                Discipulado (relacionamiento)
            </div>
            <div class="report-chart-bars">
                @foreach ($chartData as $data)
                    <div class="report-chart-col" title="{{ $data['label'] }}: {{ $data['discipulado'] }}">
                        <span class="report-chart-num">{{ $data['discipulado'] }}</span>
                        <div class="report-chart-bar" style="height:{{ round(($data['discipulado'] / $chartMax) * 100) }}%;background:var(--sacred-600)"></div>
                        <span class="report-chart-label">{{ $data['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Informes por mentor</h2>
            <p class="card-sub">Detalle del período {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('reportes.export', ['from' => $from, 'to' => $to]) }}" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Descargar Excel
        </a>
    </div>

    <form method="GET" action="{{ route('reportes.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px">
        <div class="form-group" style="margin:0">
            <label for="from">Desde</label>
            <input id="from" type="date" name="from" value="{{ $from }}" class="input">
        </div>
        <div class="form-group" style="margin:0">
            <label for="to">Hasta</label>
            <input id="to" type="date" name="to" value="{{ $to }}" class="input">
        </div>
        <button type="submit" class="btn btn-secondary">Filtrar</button>
    </form>

    @if ($mentorStats->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18"/><path d="M8 17v-4M13 17V9M18 17V5"/></svg>
            </div>
            <div class="empty-title">Sin informes en el período</div>
            <p>No hay informes registrados entre las fechas indicadas.</p>
        </div>
    @else
        @foreach ($mentorStats as $stat)
            <div style="border:1px solid var(--border-light);border-radius:14px;margin-bottom:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--bg-tertiary);flex-wrap:wrap">
                    <span class="avatar">{{ $stat['mentor'] ? mb_substr($stat['mentor']->fullname ?? 'M', 0, 1) : 'M' }}</span>
                    <div style="flex:1;min-width:140px">
                        <div style="font-weight:500">{{ $stat['mentor']->fullname ?? 'Mentor eliminado' }}</div>
                        <div style="font-size:12.5px;color:var(--text-tertiary)">{{ $stat['mentor']->email ?? '' }}</div>
                    </div>
                    <span class="badge badge-cat">{{ $stat['concretados'] }} concretado(s)</span>
                    <span class="badge badge-gray">{{ $stat['suspendidos'] }} suspendido(s)</span>
                    <span class="badge badge-gray">{{ $stat['nuevos'] }} nuevo(s)</span>
                    <span class="badge badge-gray">{{ $stat['total'] }} informe(s)</span>
                    <button type="button" class="btn btn-secondary btn-sm mentor-detail-toggle" data-target="mentor-detail-{{ $loop->index }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:5px"><polyline points="9 18 15 12 9 6"/></svg>
                        Ver detalle
                    </button>
                </div>
                <div class="mentor-detail" id="mentor-detail-{{ $loop->index }}" hidden>
                    <div class="table-wrap">
                    <table class="rows">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Célula</th>
                                <th>Líder</th>
                                <th>Mensaje</th>
                                <th>Asistentes</th>
                                <th>Nuevos</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stat['reports'] as $report)
                                <tr>
                                    <td>{{ $report->f_meet?->format('d/m/Y') }}</td>
                                    <td>{{ $report->celula ?? '—' }}</td>
                                    <td>{{ $report->lider ?? '—' }}</td>
                                    <td>{{ $report->message_title ?? '—' }}</td>
                                    <td>{{ $report->people_qty }}</td>
                                    <td>{{ $report->new_people_qty }}</td>
                                    <td>
                                        @if ($report->suspended === 'Si')
                                            <span class="badge badge-gray">Suspendido</span>
                                        @else
                                            <span class="badge badge-sacred">Concretado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@push('styles')
<style>
    .report-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }
    .report-cards .report-card {
        margin: 0;
    }
    .report-access {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }
    .report-access-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: var(--cat-600);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .report-charts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid var(--border-light);
    }
    .report-chart-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 12px;
    }
    .report-chart-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        display: inline-block;
    }
    .report-chart-bars {
        display: flex;
        align-items: flex-end;
        gap: 5px;
        height: 150px;
        padding-bottom: 0;
    }
    .report-chart-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: center;
        height: 100%;
        gap: 4px;
        min-width: 0;
    }
    .report-chart-bar {
        width: 68%;
        max-width: 30px;
        border-radius: 5px 5px 0 0;
        min-height: 2px;
        transition: filter .15s;
    }
    .report-chart-col:hover .report-chart-bar {
        filter: brightness(1.1);
    }
    .report-chart-num {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .report-chart-label {
        font-size: 10px;
        color: var(--text-tertiary);
        text-transform: capitalize;
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        var chartToggle = document.getElementById('reportChartToggle');
        var charts = document.getElementById('reportCharts');
        if (chartToggle && charts) {
            chartToggle.addEventListener('click', function () {
                var visible = charts.style.display !== 'none';
                charts.style.display = visible ? 'none' : 'grid';
                document.getElementById('reportChartToggleText').textContent = visible ? 'Mostrar gráficas' : 'Ocultar gráficas';
            });
        }

        document.querySelectorAll('.mentor-detail-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = document.getElementById(btn.dataset.target);
                if (!target) return;
                var hidden = target.hidden;
                target.hidden = !hidden;
                btn.querySelector('svg').innerHTML = hidden
                    ? '<polyline points="18 15 12 9 6 15"/>'
                    : '<polyline points="9 18 15 12 9 6"/>';
            });
        });
    })();
</script>
@endpush
@endsection
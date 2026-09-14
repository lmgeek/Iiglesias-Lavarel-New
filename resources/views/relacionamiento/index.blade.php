@extends('layouts.app')

@section('title', 'Mis discípulos')

@push('styles')
<style>
    .combobox { position: relative; }
    .combobox-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 30;
        max-height: 280px;
        overflow: auto;
        background: var(--bg-secondary);
        border: 1px solid var(--border-medium);
        border-radius: 12px;
        box-shadow: 0 12px 32px rgba(26, 23, 20, 0.14);
    }
    .combobox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        cursor: pointer;
    }
    .combobox-item:hover { background: var(--bg-tertiary); }
    .combobox-item + .combobox-item { border-top: 1px solid var(--border-light); }
    .combobox-empty {
        padding: 14px 16px;
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.6;
    }
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Mis discípulos</h1>
        <p class="page-sub">{{ $total }} discípulos · {{ $overdue }} con informe vencido</p>
    </div>
    <form method="GET" action="{{ route('relacionamiento.index') }}" class="form-row">
        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre..." class="search-input">
    </form>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total discípulos</div>
        <div class="stat-value stat-tone-default">{{ $total }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Vencidos (+12 días)</div>
        <div class="stat-value stat-tone-ember">{{ $overdue }}</div>
    </div>
</div>

<div class="card animate-fade-in-up mb-24">
    <div class="card-header">
        <div>
            <h2 class="card-title">Nuevo discípulo</h2>
            <p class="card-sub">Busca un miembro del mismo sexo o regístralo si aún no existe</p>
        </div>
    </div>
    <form method="POST" action="{{ route('relacionamiento.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group full">
                <label for="disciple_search">Discípulo *</label>
                <div class="combobox" id="disciple_combobox">
                    <input type="text" id="disciple_search" class="input @error('disciple_id') input-error @enderror" placeholder="Escribí el nombre del miembro para buscarlo..." autocomplete="off">
                    <input type="hidden" name="disciple_id" id="disciple_id" value="{{ old('disciple_id') }}">
                    <div class="combobox-menu" id="disciple_menu" hidden></div>
                </div>
                <p class="td-muted" style="font-size:12px;margin-top:6px">Al asignar el discípulo se registra el primer informe con la fecha de hoy.</p>
                @error('disciple_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mt-16">
            <button type="submit" class="btn btn-primary">Asignar discípulo</button>
        </div>
    </form>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado de discípulos</h2>
            <p class="card-sub">Último informe de relacionamiento de cada discípulo</p>
        </div>
    </div>

    @if ($relationships->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="empty-title">Sin discípulos aún</div>
            <p>Asigna tu primer discípulo desde el formulario de arriba.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Discípulo</th>
                        <th>Último informe</th>
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
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="avatar avatar-sm">{{ mb_substr($rel->disciple?->fullname ?? '?', 0, 1) }}</span>
                                    <div>
                                        <div style="font-weight:500">{{ $rel->disciple?->fullname ?? 'Sin usuario' }}</div>
                                        <div class="td-muted" style="font-size:12px">{{ $rel->disciple?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:500">{{ $rel->f_meet?->format('d/m/Y') }}</div>
                                <div class="td-muted" style="font-size:12px">
                                    {{ $rel->theme?->name ?? ($rel->other_theme ?: 'Sin tema') }}
                                    @if ($rel->initiative) · {{ $rel->initiative }} @endif
                                </div>
                            </td>
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

@push('scripts')
<script>
    (function () {
        var combobox = document.getElementById('disciple_combobox');
        var input = document.getElementById('disciple_search');
        var hidden = document.getElementById('disciple_id');
        var menu = document.getElementById('disciple_menu');
        if (!combobox || !input || !hidden || !menu) return;

        var canClose = true;
        var searchUrl = '{{ route('relacionamiento.buscar') }}';
        var createUrl = '{{ route('miembros.create') }}';

        function show(html) {
            menu.innerHTML = html;
            menu.hidden = false;
        }

        function hide() {
            menu.hidden = true;
        }

        var timer = null;
        input.addEventListener('input', function () {
            hidden.value = '';
            canClose = false;
            clearTimeout(timer);
            var q = input.value.trim();
            if (q === '') {
                hide();
                canClose = true;
                return;
            }
            timer = setTimeout(function () {
                fetch(searchUrl + '?search=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        canClose = true;
                        if (!data.length) {
                            show('<div class="combobox-empty">No se encontró a «' + q + '» entre los miembros. ' +
                                '<a href="' + createUrl + '?fullname=' + encodeURIComponent(q) + '" style="color:var(--cat-600);font-weight:500">Registrar miembro</a></div>');
                            return;
                        }
                        hidden.value = '';
                        show(data.map(function (u) {
                            return '<div class="combobox-item" data-id="' + u.id + '" data-name="' + u.fullname.replace(/"/g, '&quot;') + '">' +
                                '<span class="avatar avatar-sm">' + u.avatar + '</span>' +
                                '<div><div style="font-weight:500">' + u.fullname + '</div>' +
                                '<div class="td-muted" style="font-size:12px">' + u.email + '</div></div>' +
                                '<span class="badge badge-cat" style="margin-left:auto">' + (u.sex === 'M' ? 'M' : 'F') + '</span>' +
                                '</div>';
                        }).join(''));
                    })
                    .catch(function () {
                        canClose = true;
                        hide();
                    });
            }, 250);
        });

        menu.addEventListener('click', function (e) {
            var item = e.target.closest('.combobox-item');
            if (!item) return;
            hidden.value = item.dataset.id;
            input.value = item.dataset.name;
            hide();
        });

        document.addEventListener('click', function (e) {
            if (!canClose || !e.target.closest('#disciple_combobox')) hide();
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hide();
        });
    })();
</script>
@endpush
@extends('layouts.app')

@section('title', 'Calendario')

@push('styles')
<style>
    .cal-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .cal-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cal-nav-title {
        font-family: var(--font-serif);
        font-size: 18px;
        font-weight: 500;
        min-width: 150px;
        text-align: center;
        text-transform: capitalize;
    }
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        overflow: hidden;
        background: var(--card-bg);
        min-height: 560px;
    }
    .cal-header {
        padding: 10px 8px;
        text-align: center;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-light);
        background: var(--bg-tertiary);
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .cal-cell {
        border-right: 1px solid var(--border-light);
        border-bottom: 1px solid var(--border-light);
        padding: 6px;
        min-height: 92px;
        cursor: pointer;
        background: var(--card-bg);
        transition: background .15s;
        position: relative;
    }
    .cal-cell:hover { background: var(--bg-tertiary); }
    .cal-cell:nth-child(7n) { border-right: none; }
    .cal-cell.other-month { background: var(--bg-secondary); }
    .cal-cell.other-month .cal-day-num { color: var(--text-tertiary); }
    .cal-day-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
    .cal-day-num {
        width: 24px; height: 24px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12.5px; font-weight: 500; color: var(--text-primary);
        border-radius: 50%;
    }
    .cal-cell.today .cal-day-num { background: var(--cat-500); color: #fff; }
    .cal-event {
        display: block;
        margin: 2px 0;
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 500;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.6;
    }
    .cal-event:hover { filter: brightness(1.1); }
    .cal-meet-dot {
        display: inline-block;
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #22c55e;
        margin-right: 4px;
        vertical-align: middle;
    }
    .cal-more {
        font-size: 11px;
        color: var(--text-tertiary);
        padding: 1px 6px;
        cursor: pointer;
    }
    .cal-more:hover { color: var(--cat-600); }
    @media (max-width: 768px) {
        .cal-grid { grid-template-columns: repeat(7, 1fr); overflow-x: auto; min-height: 0; }
        .cal-cell { min-height: 70px; min-width: 60px; }
        .cal-event { font-size: 10px; }
        .cal-header { font-size: 10px; }
    }
</style>
@endpush

@section('content')
@php
    $timeOptions = '';
    for ($slot = 0; $slot < 96; $slot++) {
        $hh = intdiv($slot, 4);
        $mm = ($slot % 4) * 15;
        $timeOptions .= sprintf('<option value="%02d:%02d">%02d:%02d</option>', $hh, $mm, $hh, $mm);
    }
@endphp
<div class="page-head">
    <div>
        <h1 class="page-title">Calendario</h1>
        <p class="page-sub">Eventos de la iglesia y encuentros de discipulado</p>
    </div>
</div>

<div class="card animate-fade-in-up">
    <div class="cal-toolbar">
        <div class="cal-nav">
            <a href="{{ route('calendario.index', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}" class="btn btn-icon" title="Mes anterior">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <button type="button" class="btn" onclick="window.location='{{ route('calendario.index') }}'">Hoy</button>
            <a href="{{ route('calendario.index', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}" class="btn btn-icon" title="Mes siguiente">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <span class="cal-nav-title">{{ $monthName }}</span>
        </div>
        @if ($canEdit)
            <button type="button" class="btn btn-primary" onclick="openEventModal()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Agregar evento
            </button>
        @endif
    </div>

    <div class="cal-grid">
        <div class="cal-header">Dom</div>
        <div class="cal-header">Lun</div>
        <div class="cal-header">Mar</div>
        <div class="cal-header">Mié</div>
        <div class="cal-header">Jue</div>
        <div class="cal-header">Vie</div>
        <div class="cal-header">Sáb</div>

        @foreach ($cells as $cell)
            <div class="cal-cell {{ !$cell['isCurrentMonth'] ? 'other-month' : '' }} {{ $cell['isToday'] ? 'today' : '' }}"
                 onclick="openDay('{{ $cell['date']->format('Y-m-d') }}')"
            >
                <div class="cal-day-head">
                    <span class="cal-day-num">{{ $cell['dayNumber'] }}</span>
                </div>
                @foreach ($cell['events']->take(3) as $ev)
                    <span class="cal-event" style="background:{{ $ev->color ?? '#c57125' }}"
                          title="{{ $ev->title }} {{ $ev->start_time ? '· '.$ev->start_time : '' }}"
                          @if ($canEdit)
                            onclick="event.stopPropagation(); openEventModal(undefined, undefined, {!! $ev->toJson() !!})"
                          @endif
                    >{{ $ev->title }}</span>
                @endforeach
                @if ($cell['events']->count() > 3)
                    <span class="cal-more" onclick="event.stopPropagation(); openDay('{{ $cell['date']->format('Y-m-d') }}')">{{ $cell['events']->count() - 3 }} más</span>
                @endif
                @if (isset($meetings[$cell['date']->format('Y-m-d')]))
                    <span class="cal-event" style="background:#16a34a" title="Encuentro de discipulado">&#128214; {{ $meetings[$cell['date']->format('Y-m-d')]->count() }} encuentro(s)</span>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Modal: eventos del día -->
<div class="modal-overlay" id="dayModal" style="display:none">
    <div class="modal-card" style="max-width:520px;max-height:85vh;overflow-y:auto">
        <div class="modal-head">
            <div>
                <h3 id="dayModalTitle">Eventos del día</h3>
                <div id="dayModalSub" class="page-sub" style="margin-top:2px"></div>
            </div>
            <button type="button" class="modal-close" onclick="closeDayModal()">&times;</button>
        </div>
        <div id="dayModalBody"></div>
    </div>
</div>

@push('styles')
<style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: var(--stone-900);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 22px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(12, 10, 9, .45);
        border: 1px solid var(--border-light);
    }
    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }
    .modal-head h3 { font-size: 17px; }
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        color: var(--text-tertiary);
    }
    .modal-close:hover { color: var(--text-primary); }
    .day-event {
        background: var(--bg-tertiary);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 8px;
    }
    .day-event-title { font-weight: 500; }
    .day-event-meta { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
    .day-section-title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-tertiary);
        margin: 14px 0 8px;
    }
</style>
@endpush

@if ($canEdit)
<div class="modal-overlay" id="eventModal" style="display:none">
    <div class="modal-card" style="max-width:620px;max-height:90vh;overflow-y:auto">
        <div class="modal-head">
            <h3 id="eventModalTitle">Agregar evento</h3>
            <button type="button" class="modal-close" onclick="closeEventModal()">&times;</button>
        </div>
        <form id="eventForm" method="POST" action="{{ route('calendario.store') }}">
            @csrf
            <input type="hidden" name="_method" id="eventMethod" value="POST">
            <input type="hidden" name="id" id="eventId">

            <div class="form-group">
                <label for="title">Título *</label>
                <input id="title" type="text" name="title" required class="input" placeholder="Ej: Culto especial">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="start_date">Fecha inicio *</label>
                    <input id="start_date" type="date" name="start_date" required class="input">
                </div>
                <div class="form-group">
                    <label for="all_day">Todo el día</label>
                    <select id="all_day" name="all_day" class="input">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>
            </div>

            <div class="form-grid" id="timeRow">
                <div class="form-group">
                    <label for="start_time">Hora inicio</label>
                    <select id="start_time" name="start_time" class="input">
                        <option value="">—</option>
                        {!! $timeOptions !!}
                    </select>
                </div>
                <div class="form-group">
                    <label for="end_time">Hora fin</label>
                    <select id="end_time" name="end_time" class="input">
                        <option value="">—</option>
                        {!! $timeOptions !!}
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="location">Lugar</label>
                <input id="location" type="text" name="location" class="input" placeholder="Salón, dirección, online...">
            </div>

            <div class="form-group">
                <label for="color">Color</label>
                <div id="colorPalette" class="color-palette">
                    <input type="hidden" name="color" id="color" value="#c57125">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea id="description" name="description" rows="3" class="input" placeholder="Detalles del evento"></textarea>
            </div>

            <div class="form-group">
                <label for="is_recurring">Recurrencia</label>
                <select id="is_recurring" name="is_recurring" class="input">
                    <option value="0">No se repite</option>
                    <option value="1">Diaria</option>
                    <option value="1" data-freq="weekly">Semanal</option>
                    <option value="1" data-freq="biweekly">Cada 15 días</option>
                    <option value="1" data-freq="monthly">Mensual</option>
                    <option value="1" data-freq="yearly">Anual</option>
                </select>
                <input type="hidden" name="recurring_frequency" id="recurring_frequency" value="">
            </div>

            <div class="form-row mt-16">
                <button type="submit" class="btn btn-primary" id="eventSubmit">Guardar evento</button>
                @if ($canEdit)
                    <button type="button" class="btn btn-danger" id="eventDeleteBtn" style="display:none" onclick="deleteEvent()">Eliminar</button>
                @endif
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .color-palette {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 4px;
    }
    .color-swatch {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        border: 2.5px solid transparent;
        transition: transform .12s;
    }
    .color-swatch:hover { transform: scale(1.12); }
    .color-swatch.active { border-color: #1c1917; box-shadow: 0 0 0 2px #fff inset; }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        var paletteColors = ['#c57125', '#166534', '#1d4ed8', '#7c3aed', '#be123c', '#0e7490', '#a16207', '#4b5563'];
        var palette = document.getElementById('colorPalette');
        var colorInput = document.getElementById('color');
        paletteColors.forEach(function (c) {
            var sw = document.createElement('span');
            sw.className = 'color-swatch' + (c === colorInput.value ? ' active' : '');
            sw.style.background = c;
            sw.addEventListener('click', function () {
                document.querySelectorAll('.color-swatch').forEach(function (s) { s.classList.remove('active'); });
                sw.classList.add('active');
                colorInput.value = c;
            });
            palette.appendChild(sw);
        });

        function toDateInputValue(value) {
            if (!value) return '';
            var d = new Date(value);
            if (isNaN(d.getTime())) return '';
            var mm = ('0' + (d.getMonth() + 1)).slice(-2);
            var dd = ('0' + d.getDate()).slice(-2);
            return d.getFullYear() + '-' + mm + '-' + dd;
        }

        function setTimeSelect(select, value) {
            select.value = '';
            if (!value) return;
            var found = false;
            Array.prototype.forEach.call(select.options, function (o) {
                if (o.value === value) found = true;
            });
            if (found) {
                select.value = value;
                return;
            }
            var opt = document.createElement('option');
            opt.value = value;
            opt.textContent = value;
            select.appendChild(opt);
            select.value = value;
        }

        window.openEventModal = function (date, ev, eventObj) {
            var form = document.getElementById('eventForm');
            var method = document.getElementById('eventMethod');
            var id = document.getElementById('eventId');
            var title = 'Agregar evento';

            form.reset();
            id.value = '';
            method.value = 'POST';
            form.action = '{{ route('calendario.store') }}';
            document.getElementById('color').value = '#c57125';
            document.querySelectorAll('.color-swatch').forEach(function (s) {
                s.classList.toggle('active', s.style.background === '#c57125');
            });
            document.getElementById('eventDeleteBtn').style.display = 'none';
            document.getElementById('all_day').value = '0';
            document.getElementById('is_recurring').value = '0';
            document.getElementById('recurring_frequency').value = '';
            document.getElementById('start_time').disabled = false;
            document.getElementById('end_time').disabled = false;

            if (eventObj) {
                title = 'Editar evento';
                id.value = eventObj.id;
                method.value = 'PUT';
                form.action = '{{ route('calendario.update', ['evento' => '__ID__']) }}'.replace('__ID__', eventObj.id);
                document.getElementById('title').value = eventObj.title || '';
                document.getElementById('description').value = eventObj.description || '';
                document.getElementById('start_date').value = toDateInputValue(eventObj.start_date);
                setTimeSelect(document.getElementById('start_time'), eventObj.start_time || '');
                setTimeSelect(document.getElementById('end_time'), eventObj.end_time || '');
                document.getElementById('location').value = eventObj.location || '';
                document.getElementById('all_day').value = eventObj.all_day ? '1' : '0';
                document.getElementById('color').value = eventObj.color || '#c57125';
                document.querySelectorAll('.color-swatch').forEach(function (s) {
                    s.classList.toggle('active', s.style.background === (eventObj.color || '#c57125'));
                });
                document.getElementById('recurring_frequency').value = eventObj.recurring_frequency || '';
                if (eventObj.is_recurring) {
                    document.getElementById('is_recurring').value = '1';
                }
                document.getElementById('eventDeleteBtn').style.display = 'inline-block';
            } else if (date) {
                document.getElementById('start_date').value = date;
            }

            document.getElementById('eventModalTitle').textContent = title;
            document.getElementById('eventModal').style.display = 'flex';
            if (!ev) return;
            ev.preventDefault();
        };

        document.getElementById('is_recurring').addEventListener('change', function () {
            var sel = this.selectedOptions[0];
            document.getElementById('recurring_frequency').value = sel.dataset.freq || (sel.value === '1' ? 'daily' : '');
        });

        document.getElementById('all_day').addEventListener('change', function () {
            var disabled = this.value === '1';
            document.getElementById('start_time').disabled = disabled;
            document.getElementById('end_time').disabled = disabled;
        });

        window.closeEventModal = function () {
            document.getElementById('eventModal').style.display = 'none';
        };

        window.deleteEvent = function () {
            var id = document.getElementById('eventId').value;
            if (!id || !confirm('¿Seguro que deseas eliminar este evento?')) return;
            var form = document.getElementById('eventForm');
            form.method = 'POST';
            form.action = '{{ route('calendario.destroy', ['evento' => '__ID__']) }}'.replace('__ID__', id);
            var methodField = document.getElementById('eventMethod');
            methodField.value = 'DELETE';
            form.submit();
        };

        document.getElementById('eventModal').addEventListener('click', function (e) {
            if (e.target === this) closeEventModal();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeEventModal();
        });
    })();
</script>
@endpush
@endif

@push('scripts')
<script>
    window.DAY_EVENTS = {!! $events->map(function ($evs, $dateKey) {
        return $evs->map(function ($ev) {
            return [
                'id' => $ev->id,
                'title' => $ev->title,
                'description' => $ev->description,
                'location' => $ev->location,
                'color' => $ev->color,
                'start_date' => $ev->start_date?->format('Y-m-d'),
                'start_time' => $ev->start_time,
                'end_time' => $ev->end_time,
                'all_day' => (bool) $ev->all_day,
                'is_recurring' => (bool) $ev->is_recurring,
                'recurring_frequency' => $ev->recurring_frequency,
            ];
        })->values();
    })->toJson() !!};

    window.DAY_MEETINGS = {!! $meetings->map(function ($ms, $dateKey) {
        return $ms->map(function ($m) {
            return [
                'disciple' => $m->disciple?->fullname ?? 'Discípulo',
            ];
        })->values();
    })->toJson() !!};

    window.dayModalTitle = document.getElementById('dayModalTitle');
    window.dayModalSub = document.getElementById('dayModalSub');
    window.dayModalBody = document.getElementById('dayModalBody');
    window.CAN_EDIT = @json($canEdit);

    function fmtDayLabel(dateStr) {
        var d = new Date(dateStr + 'T00:00:00');
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    function openDay(dateStr) {
        var events = (window.DAY_EVENTS && window.DAY_EVENTS[dateStr]) ? window.DAY_EVENTS[dateStr] : [];
        var meets = (window.DAY_MEETINGS && window.DAY_MEETINGS[dateStr]) ? window.DAY_MEETINGS[dateStr] : [];

        dayModalTitle.textContent = 'Eventos del día';
        dayModalSub.textContent = fmtDayLabel(dateStr);
        dayModalBody.innerHTML = '';

        if (!events.length && !meets.length) {
            dayModalBody.innerHTML = '<div class="empty"><div class="empty-title">Sin eventos</div><p>Este día no tiene eventos ni encuentros programados.</p></div>';
        } else {
            var html = '';

            if (events.length) {
                events.forEach(function (ev) {
                    var time = ev.all_day ? 'Todo el día' : ((ev.start_time || '') + (ev.end_time ? ' – ' + ev.end_time : ''));
                    var meta = [];
                    if (time) meta.push(time);
                    if (ev.location) meta.push(ev.location);
                    html += '<div class="day-event" style="border-left:4px solid ' + (ev.color || '#c57125') + '">';
                    html += '<div class="day-event-title">' + escHtml(ev.title) + '</div>';
                    if (meta.length) html += '<div class="day-event-meta">' + meta.map(escHtml).join(' · ') + '</div>';
                    if (ev.is_recurring) html += '<div class="day-event-meta">Repite: ' + escHtml(ev.recurring_frequency || 'Sí') + '</div>';
                    if (window.CAN_EDIT) html += '<button type="button" class="btn btn-sm btn-secondary" onclick="editFromDay(' + ev.id + ')" style="margin-top:6px">Ver / Editar</button>';
                    html += '</div>';
                });
            }

            if (meets.length) {
                html += '<div class="day-section-title">Encuentros de discipulado</div>';
                meets.forEach(function (m) {
                    html += '<div class="day-event" style="border-left:4px solid #16a34a">';
                    html += '<div class="day-event-title">' + escHtml(m.disciple) + '</div>';
                    html += '</div>';
                });
            }

            dayModalBody.innerHTML = html;
        }

        document.getElementById('dayModal').style.display = 'flex';
    }

    window.closeDayModal = function () {
        document.getElementById('dayModal').style.display = 'none';
    };

    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function editFromDay(eventId) {
        var ev = null;
        for (var k in window.DAY_EVENTS) {
            (window.DAY_EVENTS[k] || []).forEach(function (e) { if (e.id === eventId) ev = e; });
        }
        closeDayModal();
        if (window.openEventModal && ev) {
            openEventModal(undefined, undefined, ev);
        }
    }

    document.getElementById('dayModal').addEventListener('click', function (e) {
        if (e.target === this) closeDayModal();
    });

    document.addEventListener('keydown', function (e) {
        var dm = document.getElementById('dayModal');
        if (e.key === 'Escape' && dm && dm.style.display === 'flex') closeDayModal();
    });
</script>
@endpush
@endsection
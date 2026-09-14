@extends('layouts.app')

@section('title', 'Cadena de oración')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Cadena de oración</h1>
        <p class="page-sub">Intercesores activos y pedidos de oración</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('prayerModal').style.display='flex'">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Agregar pedido de oración
    </button>
    <a href="{{ route('oracion.lista') }}" class="btn btn-secondary">Ver todos los intercesores</a>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Cadena activa</h2>
            <p class="card-sub">{{ $intercesiones->count() }} intercesores activos</p>
        </div>
    </div>

    @if ($intercesiones->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></svg>
            </div>
            <div class="empty-title">Cadena vacía</div>
            <p>No hay intercesores activos en este momento.</p>
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
    @endif
</div>

<div class="card animate-fade-in-up mt-24" style="margin-top:24px">
    <div class="card-header">
        <div>
            <h2 class="card-title">Pedidos de oración</h2>
            <p class="card-sub">{{ $prayerRequests->count() }} pedido(s) para intercesión</p>
        </div>
    </div>

    @if ($prayerRequests->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></svg>
            </div>
            <div class="empty-title">Sin pedidos de oración</div>
            <p>Aún no hay pedidos. Agregá el primero para que los intercesores oren.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:12px">
            @foreach ($prayerRequests as $pedido)
                <div style="border:1px solid var(--border-light);border-radius:14px;padding:16px;background:var(--bg-secondary)">
                    <div style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap">
                        <span class="avatar" style="flex-shrink:0">{{ mb_substr($pedido->name ?? $pedido->user?->fullname ?? 'A', 0, 1) }}</span>
                        <div style="flex:1;min-width:200px">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <span style="font-weight:500">{{ $pedido->name ?? ($pedido->user?->fullname ?? 'Anónimo') }}</span>
                                @if ($pedido->is_answered)
                                    <span class="badge badge-green">Respondido</span>
                                @else
                                    <span class="badge badge-sacred">Pendiente</span>
                                @endif
                            </div>
                            <p style="margin-top:6px;font-size:13.5px;white-space:pre-line">{{ $pedido->request }}</p>
                            <div style="font-size:11.5px;color:var(--text-tertiary);margin-top:6px">
                                {{ $pedido->created_at?->format('d/m/Y') }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('oracion.pedidos.toggle', $pedido) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secondary btn-sm">
                                {{ $pedido->is_answered ? 'Reabrir' : 'Marcar respondido' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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
        background: var(--bg-secondary);
        border-radius: 16px;
        padding: 22px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 60px rgba(12, 10, 9, 0.45);
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
</style>
@endpush

<div class="modal-overlay" id="prayerModal" style="display:none">
    <div class="modal-card">
        <div class="modal-head">
            <h3>Agregar pedido de oración</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('prayerModal').style.display='none'">&times;</button>
        </div>
        <form method="POST" action="{{ route('oracion.pedidos.store') }}">
            @csrf
            <div class="form-group">
                <label for="prayer_name">Nombre</label>
                <input id="prayer_name" type="text" name="name" value="{{ old('name', auth()->user()->fullname ?? '') }}" class="input" placeholder="Quién pide la oración">
            </div>
            <div class="form-group">
                <label for="prayer_request">Pedido de oración *</label>
                <textarea id="prayer_request" name="request" rows="4" required class="input" placeholder="Escribí el pedido que los intercesores verán...">{{ old('request') }}</textarea>
            </div>
            <div class="form-row mt-16">
                <button type="submit" class="btn btn-primary">Publicar pedido</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('prayerModal').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var modal = document.getElementById('prayerModal');
        modal.addEventListener('click', function (e) {
            if (e.target === this) this.style.display = 'none';
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') modal.style.display = 'none';
        });
    })();
</script>
@endpush
@endsection
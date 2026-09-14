@extends('layouts.app')

@section('title', 'Líderes')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Líderes de la iglesia</h1>
        <p class="page-sub">Líderes de ministerios y su cargo asignado</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success mb-24">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-error mb-24">{{ session('error') }}</div>
@endif

@if ($canManage)
<div class="card animate-fade-in-up mb-24" style="max-width:700px">
    <div class="card-header">
        <div>
            <h2 class="card-title">Asignar líder</h2>
            <p class="card-sub">Elegí una persona y su cargo (ministerio). Se le asignará el rol de Lider automáticamente.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('lideres.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="user_id">Persona *</label>
                <select id="user_id" name="user_id" required class="input">
                    <option value="">Seleccionar persona...</option>
                    @foreach ($candidates as $candidate)
                        <option value="{{ $candidate->id }}" @selected(old('user_id') == $candidate->id)>{{ $candidate->fullname }} {!! $candidate->email ? '· '.$candidate->email : '' !!}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="ministry_id">Cargo (Ministerio) *</label>
                <select id="ministry_id" name="ministry_id" required class="input">
                    <option value="">Seleccionar ministerio...</option>
                    @foreach ($ministries as $ministry)
                        <option value="{{ $ministry->id }}" @selected(old('ministry_id') == $ministry->id)>{{ $ministry->name }}</option>
                    @endforeach
                </select>
                @error('ministry_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mt-16">
            <button type="submit" class="btn btn-primary">Asignar como líder</button>
        </div>
    </form>
</div>
@endif

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado de líderes</h2>
            <p class="card-sub">{{ $leaders->count() }} líderes asignados</p>
        </div>
    </div>

    @if ($leaders->isEmpty())
        <div class="empty">
            <div class="empty-title">Sin líderes</div>
            <p>No hay líderes asignados todavía.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cargo (Ministerio)</th>
                        <th>Contacto</th>
                        @if ($canManage)<th style="width:200px"></th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaders as $leader)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="avatar avatar-sm" style="background:rgba(212,138,48,0.15);color:var(--cat-700)">{{ mb_substr($leader->fullname, 0, 1) }}</span>
                                    {{ $leader->fullname }}
                                </div>
                            </td>
                            <td>
                                <div class="leader-ministry">
                                    <span class="badge" style="background:rgba(212,138,48,0.12);color:var(--cat-700)">{{ $leader->ministry?->name ?? 'Sin ministerio' }}</span>
                                    @if ($canManage)
                                    <form method="POST" action="{{ route('lideres.update', $leader) }}" class="leader-edit-form" style="display:none;">
                                        @csrf
                                        @method('PUT')
                                        <select name="ministry_id" class="input" required style="min-width:200px">
                                            @foreach ($ministries as $ministry)
                                                <option value="{{ $ministry->id }}" @selected($leader->ministry_id === $ministry->id)>{{ $ministry->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="mt-8" style="display:flex;gap:6px">
                                            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                            <button type="button" class="btn btn-sm leader-edit-cancel">Cancelar</button>
                                        </div>
                                    </form>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-size:13px">{{ $leader->phone }}</div>
                                <div class="td-muted" style="font-size:12px">{{ $leader->email }}</div>
                            </td>
                            @if ($canManage)
                            <td>
                                <div style="display:flex;gap:6px;justify-content:flex-end">
                                    <button type="button" class="btn btn-sm leader-edit-toggle" data-fullname="{{ $leader->fullname }}">Editar</button>
                                    <form method="POST" action="{{ route('lideres.destroy', $leader) }}" onsubmit="return confirm('¿Quitar a {{ $leader->fullname }} como líder? Se le quitará el rol de Lider.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Quitar</button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.leader-edit-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        row.querySelector('.leader-edit-form').style.display = 'block';
        row.querySelector('.badge').style.display = 'none';
        btn.style.display = 'none';
    });
});
document.querySelectorAll('.leader-edit-cancel').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        row.querySelector('.leader-edit-form').style.display = 'none';
        row.querySelector('.badge').style.display = 'inline-block';
        row.querySelector('.leader-edit-toggle').style.display = 'inline-block';
    });
});
</script>
@endpush
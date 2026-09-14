@extends('layouts.app')

@section('title', 'Sedes')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Sedes</h1>
        <p class="page-sub">Locaciones de la iglesia</p>
    </div>
</div>

<div class="card animate-fade-in-up mb-24" style="max-width:600px">
    <div class="card-header">
        <div>
            <h2 class="card-title">Nueva sede</h2>
        </div>
    </div>
    <form method="POST" action="{{ route('config.sedes.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nombre *</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input">
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="address">Dirección</label>
                <input id="address" type="text" name="address" value="{{ old('address') }}" class="input">
            </div>
        </div>
        <div class="mt-16">
            <button type="submit" class="btn btn-primary">Crear sede</button>
        </div>
    </form>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado</h2>
            <p class="card-sub">{{ $sedes->count() }} sedes registradas</p>
        </div>
    </div>

    @if ($sedes->isEmpty())
        <div class="empty">
            <div class="empty-title">Sin sedes</div>
            <p>Registra la primera sede desde el formulario de arriba.</p>
        </div>
    @else
        @foreach ($sedes as $sede)
            <div class="list-item">
                <span class="avatar" style="background:rgba(190,24,93,0.15);color:var(--sacred-600)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <div class="list-item-main">
                    <div class="list-item-title">{{ $sede->name }}</div>
                    <div class="list-item-sub">{{ $sede->address ?? 'Sin dirección' }}</div>
                </div>
                <form method="POST" action="{{ route('config.sedes.destroy', $sede) }}" onsubmit="return confirm('¿Eliminar esta sede?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </div>
        @endforeach
    @endif
</div>
@endsection
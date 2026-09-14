@extends('layouts.app')

@section('title', 'Ministerios')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Ministerios</h1>
        <p class="page-sub">Departamentos ministeriales de la iglesia</p>
    </div>
</div>

<div class="card animate-fade-in-up mb-24" style="max-width:600px">
    <div class="card-header">
        <div>
            <h2 class="card-title">Nuevo ministerio</h2>
        </div>
    </div>
    <form method="POST" action="{{ route('config.ministries.store') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nombre *</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input">
                @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <input id="description" type="text" name="description" value="{{ old('description') }}" class="input">
            </div>
        </div>
        <div class="mt-16">
            <button type="submit" class="btn btn-primary">Crear ministerio</button>
        </div>
    </form>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado</h2>
            <p class="card-sub">{{ $ministries->count() }} ministerios registrados</p>
        </div>
    </div>

    @if ($ministries->isEmpty())
        <div class="empty">
            <div class="empty-title">Sin ministerios</div>
            <p>Registra el primer ministerio desde el formulario de arriba.</p>
        </div>
    @else
        @foreach ($ministries as $ministry)
            <div class="list-item">
                <span class="avatar" style="background:rgba(212,138,48,0.15);color:var(--cat-700)">{{ mb_substr($ministry->name, 0, 1) }}</span>
                <div class="list-item-main">
                    <div class="list-item-title">{{ $ministry->name }}</div>
                    <div class="list-item-sub">{{ $ministry->description ?? 'Sin descripción' }}</div>
                </div>
                <form method="POST" action="{{ route('config.ministries.destroy', $ministry) }}" onsubmit="return confirm('¿Eliminar este ministerio?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </div>
        @endforeach
    @endif
</div>
@endsection
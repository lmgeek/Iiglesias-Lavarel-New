@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Editar usuario</h1>
        <p class="page-sub">Actualiza los datos de {{ $user->fullname }}</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-error mb-16">Revisa los campos marcados en el formulario.</div>
@endif

<form method="POST" action="{{ route('usuarios.update', $user) }}">
    @csrf
    @method('PUT')

    <div class="card animate-fade-in-up mb-24" style="max-width:860px">
        <div class="card-header">
            <div>
                <h2 class="card-title">Datos personales</h2>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="fullname">Nombre completo *</label>
                <input id="fullname" type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" required class="input">
                @error('fullname')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="doc_number">DNI</label>
                <input id="doc_number" type="text" name="doc_number" value="{{ old('doc_number', $user->doc_number) }}" class="input">
                @error('doc_number')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="input">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input">
            </div>
            <div class="form-group">
                <label for="born_date">Fecha de nacimiento</label>
                <input id="born_date" type="date" name="born_date" value="{{ old('born_date', $user->born_date?->format('Y-m-d')) }}" class="input">
                @error('born_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="sex">Sexo</label>
                <select id="sex" name="sex" class="input">
                    <option value="">Seleccionar</option>
                    <option value="M" @selected(old('sex', $user->sex) === 'M')>Masculino</option>
                    <option value="F" @selected(old('sex', $user->sex) === 'F')>Femenino</option>
                </select>
            </div>
            <div class="form-group">
                <label for="church">Iglesia</label>
                <input id="church" type="text" name="church" value="{{ old('church', $user->church) }}" class="input">
            </div>
        </div>
    </div>

    <div class="card animate-fade-in-up mb-24" style="max-width:860px">
        <div class="card-header">
            <div>
                <h2 class="card-title">Ministerio, sede y célula</h2>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="ministry_id">Ministerio</label>
                <select id="ministry_id" name="ministry_id" class="input">
                    <option value="">Sin ministerio</option>
                    @foreach ($ministries as $ministry)
                        <option value="{{ $ministry->id }}" @selected(old('ministry_id', $user->ministry_id) == $ministry->id)>{{ $ministry->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="sede_id">Sede</label>
                <select id="sede_id" name="sede_id" class="input">
                    <option value="">Sin sede</option>
                    @foreach ($sedes as $sede)
                        <option value="{{ $sede->id }}" @selected(old('sede_id', $user->sede_id) == $sede->id)>{{ $sede->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="lider_celula">¿Líder de célula?</label>
                <select id="lider_celula" name="lider_celula" class="input">
                    <option value="No" @selected(old('lider_celula', $user->lider_celula ?? 'No') === 'No')>No</option>
                    <option value="Si" @selected(old('lider_celula', $user->lider_celula) === 'Si')>Sí</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card animate-fade-in-up mb-24" style="max-width:860px">
        <div class="card-header">
            <div>
                <h2 class="card-title">Roles y estado</h2>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label>Roles</label>
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:6px">
                @foreach ($roles as $role)
                    <label class="badge" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;padding:8px 12px;border:1px solid var(--border-light);user-select:none;font-weight:500">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(in_array($role->id, old('roles', $user->roles->pluck('id')->all()))) style="width:14px;height:14px">
                        {{ $role->name }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form-row">
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $user->is_active)) style="width:16px;height:16px">
            <label for="is_active" style="font-weight:400;color:var(--text-secondary)">Usuario activo</label>
        </div>
    </div>

    <div class="mt-24">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</form>
@endsection
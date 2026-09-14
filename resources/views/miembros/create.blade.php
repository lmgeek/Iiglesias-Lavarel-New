@extends('layouts.app')

@section('title', 'Registrar miembro')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Registrar miembro</h1>
        <p class="page-sub">Carga un nuevo miembro de la iglesia</p>
    </div>
</div>

<div class="card animate-fade-in-up" style="max-width:860px">
    <form method="POST" action="{{ route('miembros.store') }}">
        @csrf

        <h3 style="margin-bottom:16px">Datos personales</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="fullname">Nombre completo *</label>
                <input id="fullname" type="text" name="fullname" value="{{ old('fullname', request('fullname')) }}" required class="input">
                @error('fullname')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="doc_number">DNI *</label>
                <input id="doc_number" type="text" name="doc_number" value="{{ old('doc_number') }}" required class="input">
                @error('doc_number')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="born_date">Fecha de nacimiento *</label>
                <input id="born_date" type="date" name="born_date" value="{{ old('born_date') }}" required class="input">
                @error('born_date')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="sex">Sexo *</label>
                <select id="sex" name="sex" required class="input">
                    <option value="" disabled selected>Seleccionar</option>
                    <option value="M" @selected(old('sex') === 'M')>Masculino</option>
                    <option value="F" @selected(old('sex') === 'F')>Femenino</option>
                </select>
                @error('sex')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <h3 class="mt-24" style="margin-bottom:16px">Contacto e iglesia</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="email">Email *</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="phone">Teléfono *</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required class="input">
                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="sede_id">Iglesia *</label>
                <select id="sede_id" name="sede_id" required class="input">
                    <option value="" disabled selected>Selecciona una sede</option>
                    @foreach ($sedes as $sede)
                        <option value="{{ $sede->id }}" @selected(old('sede_id') == $sede->id)>{{ $sede->name }}</option>
                    @endforeach
                </select>
                @error('sede_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="lider_celula">¿Líder de célula? *</label>
                <select id="lider_celula" name="lider_celula" required class="input">
                    <option value="No" @selected(old('lider_celula', 'No') === 'No')>No</option>
                    <option value="Si" @selected(old('lider_celula') === 'Si')>Sí</option>
                </select>
                @error('lider_celula')<div class="field-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-24" style="background:var(--bg-tertiary);border-radius:12px;padding:14px 16px;font-size:13px;color:var(--text-secondary);display:flex;gap:10px;align-items:flex-start">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div>Al registrar, se genera un usuario automáticamente y se envía un correo de bienvenida a la casilla indicada con las credenciales de acceso al sitio.</div>
        </div>

        <div class="mt-24 form-row">
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true)) style="width:16px;height:16px">
            <label for="is_active" style="font-weight:400;color:var(--text-secondary)">Miembro activo</label>
        </div>

        <div class="mt-24">
            <button type="submit" class="btn btn-primary">Registrar miembro</button>
        </div>
    </form>
</div>
@endsection
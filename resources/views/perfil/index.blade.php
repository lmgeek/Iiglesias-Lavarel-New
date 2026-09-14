@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Mi perfil</h1>
        <p class="page-sub">Información personal y seguridad de tu cuenta</p>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr));margin-bottom:24px">
    <div class="card animate-fade-in-up">
        <div style="display:flex;align-items:center;gap:16px">
            <div class="avatar" style="width:56px;height:56px;font-size:22px">{{ mb_substr($user->fullname ?? 'U', 0, 1) }}</div>
            <div>
                <h2 style="font-size:18px;font-weight:500">{{ $user->fullname }}</h2>
                <div style="color:var(--text-tertiary);font-size:13px">{{ $user->email }}</div>
                <div style="margin-top:6px">
                    <span class="badge badge-cat">{{ optional($user->roles->first())->name ?? 'Usuario' }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="card animate-fade-in-up" style="animation-delay:0.05s">
        <div class="card-header">
            <div>
                <h2 class="card-title">Detalles</h2>
            </div>
        </div>
        <div class="form-grid" style="gap:10px">
            <div>
                <div style="font-size:12px;color:var(--text-tertiary)">Ministerio</div>
                <div style="font-weight:500;margin-top:2px">{{ $user->ministry?->name ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-tertiary)">Sede</div>
                <div style="font-weight:500;margin-top:2px">{{ $user->sede?->name ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-tertiary)">Rango ministerial</div>
                <div style="font-weight:500;margin-top:2px">{{ $user->ministerial_range ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-tertiary)">Célula</div>
                <div style="font-weight:500;margin-top:2px">{{ $user->celula ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card animate-fade-in-up mb-24" style="max-width:760px">
    <div class="card-header">
        <div>
            <h2 class="card-title">Editar información</h2>
        </div>
    </div>
    <form method="POST" action="{{ route('perfil.update') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="fullname">Nombre completo</label>
                <input id="fullname" type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" class="input">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="phone">Teléfono</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input">
            </div>
            <div class="form-group">
                <label for="church">Iglesia</label>
                <input id="church" type="text" name="church" value="{{ old('church', $user->church) }}" class="input">
            </div>
            <div class="form-group">
                <label for="mentor">Mentor</label>
                <input id="mentor" type="text" name="mentor" value="{{ old('mentor', $user->mentor) }}" class="input">
            </div>
            <div class="form-group">
                <label for="ministerial_range">Rango ministerial</label>
                <input id="ministerial_range" type="text" name="ministerial_range" value="{{ old('ministerial_range', $user->ministerial_range) }}" class="input">
            </div>
            <div class="form-group">
                <label for="celula">Célula</label>
                <input id="celula" type="number" name="celula" value="{{ old('celula', $user->celula) }}" class="input">
            </div>
        </div>
        <div class="mt-16">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>

<div class="card animate-fade-in-up" style="max-width:760px">
    <div class="card-header">
        <div>
            <h2 class="card-title">Cambiar contraseña</h2>
        </div>
    </div>
    <form method="POST" action="{{ route('perfil.update') }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="current_password">Contraseña actual</label>
                <input id="current_password" type="password" name="current_password" class="input">
                @error('current_password')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password">Nueva contraseña</label>
                <input id="password" type="password" name="password" class="input">
                @error('password')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="input">
            </div>
        </div>
        <div class="mt-16">
            <button type="submit" class="btn btn-secondary">Actualizar contraseña</button>
        </div>
    </form>
</div>
@endsection
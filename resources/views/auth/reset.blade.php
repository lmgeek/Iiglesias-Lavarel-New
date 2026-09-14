@extends('layouts.auth')

@section('title', 'Crear nueva contraseña')

@section('content')
<div class="auth-card animate-fade-in-up">
    <div class="text-center" style="margin-bottom:28px">
        <div class="auth-logo-circle" style="margin-bottom:24px">
            @if (!empty($config->logo))
                <img src="{{ $config->logo }}" alt="{{ $config->church_name }}" style="width:80px;height:80px;border-radius:50%;object-fit:contain;">
            @else
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--cat-600)" stroke-width="1.5">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @endif
        </div>
        <h1 class="auth-heading">Crear nueva contraseña</h1>
        <p class="auth-sub">Elegí una nueva contraseña para tu cuenta.</p>
    </div>

    @if (!$token)
        <div class="alert alert-error">El enlace no es válido o está incompleto. Solicita uno nuevo desde <a href="{{ route('password.request') }}">aquí</a>.</div>
    @else
        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required class="input @error('email') input-error @enderror" placeholder="tu@email.com">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password">Nueva contraseña</label>
                <div class="password-wrap">
                    <input id="password" type="password" name="password" required minlength="6" class="input @error('password') input-error @enderror" placeholder="••••••••">
                    <button type="button" class="password-toggle" tabindex="-1">
                        <span data-icon="eye">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </span>
                        <span data-icon="eye-off" style="display:none">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </span>
                    </button>
                </div>
                @error('password')<div class="field-error">{{ $message }}</div>@enderror
                @error('token')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirmar contraseña</label>
                <div class="password-wrap">
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="6" class="input" placeholder="••••••••">
                    <button type="button" class="password-toggle" tabindex="-1">
                        <span data-icon="eye">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </span>
                        <span data-icon="eye-off" style="display:none">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </span>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Guardar contraseña
            </button>
        </form>

        <p style="text-align:center;margin-top:22px;font-size:13px">
            <a href="{{ route('login') }}" style="color:var(--cat-600);font-weight:500">← Volver a iniciar sesión</a>
        </p>
    @endif
</div>
@endsection
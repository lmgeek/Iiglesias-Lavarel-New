@extends('layouts.auth')

@section('title', '¿Olvidaste tu contraseña?')

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
        <h1 class="auth-heading">¿Olvidaste tu contraseña?</h1>
        <p class="auth-sub">Ingresá tu email y te enviaremos un enlace para restablecerla.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input @error('email') input-error @enderror" placeholder="tu@email.com">
            @error('email')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>
            Enviar enlace
        </button>
    </form>

    <p style="text-align:center;margin-top:22px;font-size:13px">
        <a href="{{ route('login') }}" style="color:var(--cat-600);font-weight:500">← Volver a iniciar sesión</a>
    </p>
</div>
@endsection
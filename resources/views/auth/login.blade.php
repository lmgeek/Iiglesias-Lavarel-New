@extends('layouts.auth')

@section('title', $needsSetup ? 'Configurar administrador' : 'Iniciar sesión')

@section('content')
<div class="auth-card animate-fade-in-up">
    <div class="text-center" style="margin-bottom:32px">
        <div class="auth-logo-circle" style="margin-bottom:24px">
            @if (!empty($config->logo))
                <img src="{{ $config->logo }}" alt="{{ $config->church_name }}" style="width:80px;height:80px;border-radius:50%;object-fit:contain;">
            @else
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--cat-600)" stroke-width="1.5">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @endif
        </div>
        <h1 class="auth-heading">{{ $config->church_name ?? 'Catedral Cristiana' }}</h1>
        <p class="auth-sub">{{ $needsSetup ? 'Configura tu primer administrador' : 'Inicia sesión para continuar' }}</p>
    </div>

    @if ($needsSetup)
        <form method="POST" action="{{ route('setup') }}" class="auth-form">
            @csrf
            <div class="field">
                <label for="fullname">Nombre completo</label>
                <input id="fullname" type="text" name="fullname" value="{{ old('fullname') }}" required class="input @error('fullname') input-error @enderror" placeholder="Ej: Pastor Juan Pérez">
                @error('fullname')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input @error('email') input-error @enderror" placeholder="admin@iglesia.com">
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
            </div>

            <div class="field">
                <label for="password_confirmation">Confirmar contraseña</label>
                <div class="password-wrap">
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="6" class="input @error('password') input-error @enderror" placeholder="••••••••">
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
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                Crear administrador
            </button>
        </form>
    @else
        <form method="POST" action="{{ route('login.attempt') }}" class="auth-form">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="input @error('email') input-error @enderror" placeholder="tu@email.com">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <div class="password-wrap">
                    <input id="password" type="password" name="password" required class="input @error('password') input-error @enderror" placeholder="••••••••">
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
            </div>

            <div class="auth-actions">
                <a href="{{ route('password.request') }}" style="color:var(--cat-600);font-weight:500">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                Iniciar sesión
            </button>
        </form>

        <div id="google-login-wrap" class="google-divider" style="display:none">
            <div class="google-divider-line"><span>o continúa con</span></div>
            <a href="{{ route('auth.google') }}" class="btn-google btn-block">
                <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47a5.57 5.57 0 0 1-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09C3.26 21.31 7.31 24 12 24z"/><path fill="#FBBC05" d="M5.27 14.29a7.13 7.13 0 0 1 0-4.58V6.62H1.29a11.86 11.86 0 0 0 0 10.76l3.98-3.09z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.93 1.19 15.22 0 12 0 7.31 0 3.26 2.69 1.29 6.62l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75z"/></svg>
                Iniciar sesión con Google
            </a>
        </div>

        <script>
            (function () {
                var input = document.getElementById('email');
                var wrap = document.getElementById('google-login-wrap');
                if (!input || !wrap) return;
                function update() {
                    var v = input.value.trim().toLowerCase();
                    wrap.style.display = v.endsWith('@gmail.com') ? '' : 'none';
                }
                input.addEventListener('input', update);
                update();
            })();
        </script>
    @endif

    <div class="auth-card-footer">
        @include('layouts.partials.church-contact')
    </div>
</div>
@endsection
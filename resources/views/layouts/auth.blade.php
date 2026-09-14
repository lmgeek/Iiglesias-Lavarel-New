<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Iniciar sesión') · {{ $config->church_name ?? 'Catedral Cristiana' }}</title>
    @if (!empty($config->favicon))
        <link rel="icon" href="{{ $config->favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="auth-wrap" @if (!empty($config->login_bg)) style="--auth-bg:url('{{ $config->login_bg }}');" @endif>
        @if (!empty($config->login_bg))
            <div class="auth-mobile-overlay" aria-hidden="true"></div>
        @endif
        <div class="auth-panel" @if (!empty($config->login_bg)) style="background-image:url('{{ $config->login_bg }}');background-size:cover;background-position:center;" @endif>
            <div class="auth-panel-scrim" aria-hidden="true"></div>
            <div style="position:absolute;inset:0;opacity:0.04">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="sacred-grid" width="80" height="80" patternUnits="userSpaceOnUse">
                            <circle cx="40" cy="40" r="38" fill="none" stroke="white" stroke-width="0.5"/>
                            <circle cx="40" cy="40" r="28" fill="none" stroke="white" stroke-width="0.3"/>
                            <circle cx="40" cy="40" r="14" fill="none" stroke="white" stroke-width="0.3"/>
                            <line x1="2" y1="40" x2="78" y2="40" stroke="white" stroke-width="0.2"/>
                            <line x1="40" y1="2" x2="40" y2="78" stroke="white" stroke-width="0.2"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#sacred-grid)"/>
                </svg>
            </div>
            <div class="auth-panel-content">
                <div class="auth-logo-circle">
                    @if (!empty($config->logo))
                        <img src="{{ $config->logo }}" alt="{{ $config->church_name }}" style="width:80px;height:80px;border-radius:50%;object-fit:contain;">
                    @else
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--cat-600)" stroke-width="1.5">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
                </div>
                <h1 style="font-family:'Jost',sans-serif;font-size:36px;font-weight:300;letter-spacing:0.03em;margin:12px 0 0">
                    {{ $config->church_name ?? 'Catedral Cristiana' }}
                </h1>
                <p style="color:#d6cfc8;font-size:14px;margin-top:12px;max-width:340px;margin-left:auto;margin-right:auto;line-height:1.6">
                    Plataforma de discipulado y gestión ministerial
                </p>
                <blockquote class="auth-verse">
                    <p>Por tanto, vayan y hagan discípulos de todas las naciones, bautizándolos en el nombre del Padre y del Hijo y del Espíritu Santo, enseñándoles a obedecer todo lo que les he mandado a ustedes.</p>
                    <cite>Mateo 28:19-20 (NVI)</cite>
                </blockquote>
            </div>
        </div>

        <div class="auth-form-side">
            <div class="auth-form-scrim {{ !empty($config->login_bg) ? 'auth-form-scrim--dark' : '' }}">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.password-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = btn.closest('.password-wrap').querySelector('input');
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.querySelector('[data-icon=eye-off]').style.display = 'block';
                    btn.querySelector('[data-icon=eye]').style.display = 'none';
                } else {
                    input.type = 'password';
                    btn.querySelector('[data-icon=eye-off]').style.display = 'none';
                    btn.querySelector('[data-icon=eye]').style.display = 'block';
                }
            });
        });
    </script>
</body>
</html>
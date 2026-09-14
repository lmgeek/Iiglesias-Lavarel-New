<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Plataforma de Discipulado') · {{ $config['church_name'] ?? 'Catedral Cristiana' }}</title>
    @if (!empty($config->favicon))
        <link rel="icon" href="{{ $config->favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Sora:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        @include('layouts.partials.sidebar')

        <div class="app-main">
            @include('layouts.partials.header')

            <main class="app-content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </main>

            <footer style="padding:18px 24px;border-top:1px solid var(--border-light)">
                @include('layouts.partials.church-contact')
            </footer>
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

        document.querySelectorAll('.sidebar-link').forEach(function (link) {
            link.addEventListener('click', function () {
                document.querySelectorAll('.sidebar-link').forEach(function (l) { l.classList.remove('active'); });
                link.classList.add('active');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
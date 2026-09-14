<header class="app-header">
    <div style="display:flex;align-items:center;gap:10px">
        @if (!empty($config->logo))
            <img src="{{ $config->logo }}" alt="{{ $config->church_name }}" style="width:28px;height:28px;object-fit:contain;border-radius:6px">
        @endif
        <h1 class="header-title">@yield('title', 'Dashboard')</h1>
    </div>
    <div class="header-actions">
        <button class="icon-btn" title="Notificaciones" onclick="toggleNotifications(this)" style="position:relative">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
        </button>
        <a class="icon-btn" href="{{ route('perfil.index') }}" title="Mi perfil">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
            </svg>
        </a>
    </div>
</header>
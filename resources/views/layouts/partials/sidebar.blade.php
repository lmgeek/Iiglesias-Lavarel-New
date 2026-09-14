<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-logo">
            @if (!empty($config->logo))
                <img src="{{ $config->logo }}" alt="{{ $config->church_name }}" style="width:100%;height:100%;object-fit:contain;border-radius:10px;">
            @else
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            @endif
        </span>
        <span>
            <div class="brand-name">{{ $config->church_name ?? 'Catedral Cristiana' }}</div>
            <div class="brand-sub">Plataforma de discipulado</div>
        </span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">Menú</div>
        <a class="nav-link @if (request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="7" height="9" rx="1.5" /><rect x="14" y="3" width="7" height="5" rx="1.5" /><rect x="14" y="12" width="7" height="9" rx="1.5" /><rect x="3" y="16" width="7" height="5" rx="1.5" />
                </svg>
            </span>
            Dashboard
        </a>

        @can('Relacionamiento.view')
        <div class="nav-section-title">Discipulado</div>
        <a class="nav-link @if (request()->routeIs('relacionamiento.index')) active @endif" href="{{ route('relacionamiento.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </span>
            Mis discípulos
        </a>
        <a class="nav-link @if (request()->routeIs('relacionamiento.red')) active @endif" href="{{ route('relacionamiento.red') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z" />
                </svg>
            </span>
            Mi red
        </a>
        @endcan

        @can('Informes.view')
        <div class="nav-section-title">Grupos de Conexión</div>
        <a class="nav-link @if (request()->routeIs('informes.index')) active @endif" href="{{ route('informes.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" />
                </svg>
            </span>
            Mis informes
        </a>
        <a class="nav-link @if (request()->routeIs('informes.create')) active @endif" href="{{ route('informes.create') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />
                </svg>
            </span>
            Nuevo informe
        </a>
        @endcan

        @can('Miembros.view')
        <div class="nav-section-title">Miembros</div>
        <a class="nav-link @if (request()->routeIs('miembros.create')) active @endif" href="{{ route('miembros.create') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="8" r="4" /><path d="M4 21v-1a7 7 0 0 1 14 0v1" />
                </svg>
            </span>
            Registrar miembro
        </a>
        <a class="nav-link @if (request()->routeIs('miembros.index')) active @endif" href="{{ route('miembros.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <line x1="8" y1="6" x2="21" y2="6" /><line x1="8" y1="12" x2="21" y2="12" /><line x1="8" y1="18" x2="21" y2="18" /><line x1="3" y1="6" x2="3.01" y2="6" /><line x1="3" y1="12" x2="3.01" y2="12" /><line x1="3" y1="18" x2="3.01" y2="18" />
                </svg>
            </span>
            Lista de miembros
        </a>
        @endcan

        @can('Oración.view')
        <div class="nav-section-title">Oración</div>
        <a class="nav-link @if (request()->routeIs('oracion.index')) active @endif" href="{{ route('oracion.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z" />
                </svg>
            </span>
            Cadena de oración
        </a>
        <a class="nav-link @if (request()->routeIs('oracion.lista')) active @endif" href="{{ route('oracion.lista') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" />
                </svg>
            </span>
            Intercesores
        </a>
        @endcan

        <div class="nav-section-title">General</div>

        @can('Configuración.view')
        <a class="nav-link @if (request()->routeIs('calendario.index')) active @endif" href="{{ route('calendario.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="4" width="18" height="18" rx="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />
                </svg>
            </span>
            Calendario
        </a>
        @endcan

        @can('Reportes.view')
        <a class="nav-link @if (request()->routeIs('reportes.index')) active @endif" href="{{ route('reportes.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 3v18h18" /><path d="M8 17v-4M13 17V9M18 17V5" />
                </svg>
            </span>
            Reportes
        </a>
        @endcan

        @can('Biblioteca.view')
        <a class="nav-link @if (request()->routeIs('biblioteca.index')) active @endif" href="{{ route('biblioteca.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
            </span>
            Biblioteca
        </a>
        @endcan

        @can('Usuarios.view')
        <a class="nav-link @if (request()->routeIs('usuarios.index')) active @endif" href="{{ route('usuarios.index') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><circle cx="17" cy="11" r="3" /><path d="M21 21v-2a4 4 0 0 0-3-3.87" />
                </svg>
            </span>
            Usuarios
        </a>
        @endcan

        @can('Configuración.view')
        <div class="nav-section-title">Configuración</div>
        <a class="nav-link @if (request()->routeIs('config.roles')) active @endif" href="{{ route('config.roles') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" /><circle cx="12" cy="12" r="3" />
                </svg>
            </span>
            Roles
        </a>
        <a class="nav-link @if (request()->routeIs('config.temas')) active @endif" href="{{ route('config.temas') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <line x1="4" y1="21" x2="4" y2="14" /><line x1="4" y1="10" x2="4" y2="3" /><line x1="12" y1="21" x2="12" y2="12" /><line x1="12" y1="8" x2="12" y2="3" /><line x1="20" y1="21" x2="20" y2="16" /><line x1="20" y1="12" x2="20" y2="3" /><line x1="1" y1="14" x2="7" y2="14" /><line x1="9" y1="8" x2="15" y2="8" /><line x1="17" y1="16" x2="23" y2="16" />
                </svg>
            </span>
            Temas
        </a>
        <a class="nav-link @if (request()->routeIs('config.ajustes')) active @endif" href="{{ route('config.ajustes') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
            </span>
            Ajustes
        </a>
        <a class="nav-link @if (request()->routeIs('config.ministries')) active @endif" href="{{ route('config.ministries') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 21h18" /><path d="M5 21v-7M9 21v-7M15 21v-7M19 21v-7M3 14h18l-2-7H5z" />
                </svg>
            </span>
            Ministries
        </a>
        <a class="nav-link @if (request()->routeIs('config.sedes')) active @endif" href="{{ route('config.sedes') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" /><circle cx="12" cy="10" r="3" />
                </svg>
            </span>
            Sedes
        </a>
        <a class="nav-link @if (request()->routeIs('config.migracion')) active @endif" href="{{ route('config.migracion') }}">
            <span class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h5M18 14v6M14 17h4" />
                </svg>
            </span>
            Migración
        </a>
        @endcan
    </nav>

    <div class="sidebar-footer">
        @include('layouts.partials.church-contact', ['compact' => true])
        <div class="sidebar-user">
            <div class="avatar">{{ mb_substr(Auth::user()->fullname ?? 'U', 0, 1) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->fullname ?? 'Usuario' }}</div>
                <div class="sidebar-user-role">{{ optional(Auth::user()->roles->first())->name ?? 'Usuario' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="logout-btn" title="Cerrar sesión">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
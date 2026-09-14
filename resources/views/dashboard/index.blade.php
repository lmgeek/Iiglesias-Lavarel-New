@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Bienvenido, {{ explode(' ', Auth::user()->fullname ?? '') [0] ?: 'Pastor' }}</h1>
        <p class="page-sub">Resumen de tu discipulado ministerial</p>
    </div>
    <span class="badge badge-cat">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
    </span>
</div>

<div class="stats-grid">
    <div class="stat-card animate-fade-in-up" style="animation-delay:0s">
        <div class="stat-label">Total discípulos</div>
        <div class="stat-value stat-tone-default">{{ $stats['totalDiscipulos'] }}</div>
    </div>
    <div class="stat-card animate-fade-in-up" style="animation-delay:0.05s">
        <div class="stat-label">Relaciones vencidas</div>
        <div class="stat-value stat-tone-ember">{{ $stats['relacionesVencidas'] }}</div>
    </div>
    <div class="stat-card animate-fade-in-up" style="animation-delay:0.1s">
        <div class="stat-label">Por vencer</div>
        <div class="stat-value stat-tone-cat">{{ $stats['relacionesPorVencer'] }}</div>
    </div>
    <div class="stat-card animate-fade-in-up" style="animation-delay:0.15s">
        <div class="stat-label">Informes este mes</div>
        <div class="stat-value stat-tone-sacred">{{ $stats['informesEsteMes'] }}</div>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr));margin-bottom:0">
    <div class="card animate-fade-in-up" style="animation-delay:0.2s">
        <div class="card-header">
            <div>
                <h2 class="card-title">Último tema</h2>
                <p class="card-sub">Tema de reunión más reciente</p>
            </div>
            <span class="avatar">{{ $ultimoTema ? mb_substr($ultimoTema->name ?? 'T', 0, 1) : 'T' }}</span>
        </div>
        @if ($ultimoTema)
            <h3 style="font-size:18px;font-weight:500;margin-bottom:6px">{{ $ultimoTema->name }}</h3>
            <div style="margin-top:8px;font-size:12.5px;color:var(--text-tertiary)">Actualizado {{ $ultimoTema->updated_at?->diffForHumans() ?? 'recientemente' }}</div>

            @if ($ultimoTema->image)
                <div class="theme-image-wrap" style="margin-top:14px">
                    <img src="{{ $ultimoTema->image }}" alt="{{ $ultimoTema->name }}" class="theme-image"
                         style="width:100%;max-height:220px;object-fit:cover;border-radius:14px;border:1px solid var(--border-light)">
                    <a href="{{ $ultimoTema->image }}" download class="btn btn-sm btn-secondary" style="margin-top:10px"
                       title="Descargar imagen del tema">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Descargar imagen
                    </a>
                </div>
            @endif

            @if ($ultimoTema->youtubeEmbedUrl())
                <div class="theme-video-wrap" style="margin-top:14px;position:relative;padding-top:56.25%;border-radius:14px;overflow:hidden;border:1px solid var(--border-light);background:#000">
                    <iframe src="{{ $ultimoTema->youtubeEmbedUrl() }}" title="Video del tema" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            style="position:absolute;inset:0;width:100%;height:100%"></iframe>
                </div>
            @endif
        @else
            <div class="empty">
                <div class="empty-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
                </div>
                <div class="empty-title">Sin temas aún</div>
                <p>Agrega un tema desde Configuración → Temas</p>
            </div>
        @endif
    </div>

    <div class="card animate-fade-in-up" style="animation-delay:0.25s">
        <div class="card-header">
            <div>
                <h2 class="card-title">Eventos de esta semana</h2>
                <p class="card-sub">{{ $semanaInicio->locale('es')->translatedFormat('d M') }} – {{ $semanaFin->locale('es')->translatedFormat('d M Y') }}</p>
            </div>
            <a href="{{ route('calendario.index') }}" style="font-size:12.5px;color:var(--cat-600);font-weight:500">Ver calendario</a>
        </div>
        @forelse ($semanaEventos as $ev)
            <a href="{{ route('calendario.index') }}" style="text-decoration:none">
                <div class="list-item" style="border:1px solid var(--border-light);border-radius:12px;margin-bottom:6px;padding:10px 12px">
                    <span style="width:10px;height:10px;border-radius:50%;background:{{ $ev->color ?? '#c57125' }};flex-shrink:0"></span>
                    <div class="list-item-main">
                        <div class="list-item-title">{{ $ev->title }}</div>
                        <div class="list-item-sub">{{ $ev->start_date?->locale('es')->translatedFormat('l d M') }} {{ $ev->start_time ? '· '.$ev->start_time : '' }}</div>
                    </div>
                    @if ($ev->location)
                        <span class="badge badge-gray">{{ $ev->location }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="empty" style="padding:18px 0">
                <div class="empty-title" style="font-size:13px">Sin eventos esta semana</div>
                <p style="font-size:12.5px">Agrega eventos desde el calendario.</p>
            </div>
        @endforelse

        <div class="mt-16">
            <h2 class="card-title" style="margin-bottom:8px">Accesos rápidos</h2>
        </div>
        <div class="list-item">
            <span class="avatar avatar-sm" style="background:rgba(212,138,48,0.15);color:var(--cat-700)">D</span>
            <div class="list-item-main">
                <a href="{{ route('relacionamiento.index') }}" class="list-item-title" style="color:var(--text-primary)">Mis discípulos</a>
                <div class="list-item-sub">Gestiona tus relaciones de discipulado</div>
            </div>
        </div>
        <div class="list-item">
            <span class="avatar avatar-sm" style="background:rgba(190,24,93,0.15);color:var(--sacred-600)">I</span>
            <div class="list-item-main">
                <a href="{{ route('informes.create') }}" class="list-item-title" style="color:var(--text-primary)">Nuevo informe</a>
                <div class="list-item-sub">Registra un informe de grupo de conexión</div>
            </div>
        </div>
        <div class="list-item">
            <span class="avatar avatar-sm" style="background:rgba(249,115,22,0.15);color:var(--ember-600)">O</span>
            <div class="list-item-main">
                <a href="{{ route('oracion.index') }}" class="list-item-title" style="color:var(--text-primary)">Cadena de oración</a>
                <div class="list-item-sub">Consulta la cadena de intercesión</div>
            </div>
        </div>
    </div>
</div>
@endsection
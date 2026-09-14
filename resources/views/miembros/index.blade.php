@extends('layouts.app')

@section('title', 'Lista de miembros')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Lista de miembros</h1>
        <p class="page-sub">{{ $total }} miembros registrados en total</p>
    </div>
    <a href="{{ route('miembros.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a7 7 0 0 1 14 0v1"/></svg>
        Registrar miembro
    </a>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Miembros</h2>
        </div>
        <form method="GET" action="{{ route('miembros.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar nombre, email o DNI..." class="search-input">
        </form>
    </div>

    @if ($members->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a7 7 0 0 1 14 0v1"/></svg>
            </div>
            <div class="empty-title">Sin miembros</div>
            <p>Registra el primer miembro de la iglesia.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Miembro</th>
                        <th>Mentor / Facilitador</th>
                        <th>Nacimiento</th>
                        <th>Edad</th>
                        <th>Teléfono</th>
                        <th>Iglesia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $member)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="avatar avatar-sm">{{ mb_substr($member->fullname ?? 'M', 0, 1) }}</span>
                                    <div>
                                        <div style="font-weight:500">{{ $member->fullname }}</div>
                                        <div class="td-muted" style="font-size:12px">{{ $member->doc_number ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($member->mentorUser)
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <span style="font-weight:500">{{ $member->mentorUser->fullname }}</span>
                                    </div>
                                @else
                                    <span class="td-muted">—</span>
                                @endif
                                @if ($member->isFacilitador)
                                    <div class="mt-8"><span class="badge badge-cat">Facilitador</span></div>
                                @endif
                            </td>
                            <td>{{ $member->born_date?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $member->age ?? '—' }}</td>
                            <td>
                                @if ($member->phone)
                                    <a href="{{ $member->whatsapp }}" target="_blank" rel="noopener"
                                       class="btn btn-sm btn-secondary"
                                       title="Enviar WhatsApp a {{ $member->fullname }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        {{ $member->phone }}
                                    </a>
                                @else
                                    <span class="td-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $member->church ?? '—' }}</td>
                            <td>
                                @if ($member->is_active)
                                    <span class="badge badge-green">Activo</span>
                                @else
                                    <span class="badge badge-gray">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-16" style="display:flex;justify-content:center">
            {{ $members->links() }}
        </div>
    @endif
</div>
@endsection
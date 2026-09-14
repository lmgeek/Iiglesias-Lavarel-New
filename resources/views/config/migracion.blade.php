@extends('layouts.app')

@section('title', 'Migración')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Migración de datos</h1>
        <p class="page-sub">Importar datos desde una base de datos legacy</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Importar datos</h2>
            <p class="card-sub">Selecciona base de datos de origen y la iglesia a migrar</p>
        </div>
    </div>

    <form method="POST" action="{{ route('config.migracion.store') }}" class="form-grid" id="migracion-form">
        @csrf

        <div class="form-group">
            <label for="source_db">Base de datos de origen</label>
            <select name="source_db" id="source_db" class="input" required>
                <option value="">Seleccionar base...</option>
                @foreach ($databases as $db)
                    <option value="{{ $db['name'] }}" @if ($db['is_current']) disabled @endif>
                        {{ $db['name'] }}@if ($db['is_current']) (actual) @endif
                    </option>
                @endforeach
            </select>
            @error('source_db')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="church">Iglesia</label>
            <select name="church" id="church" class="input">
                <option value="">Todas las iglesias</option>
            </select>
        </div>

        <div class="form-group" style="align-self:end">
            <button type="submit" class="btn btn-primary btn-block" id="migrar-btn">
                Migrar datos (INSERT IGNORE)
            </button>
        </div>
    </form>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Registros por tabla</h2>
            <p class="card-sub">Cantidad de registros activos en el sistema actual</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="rows">
            <thead>
                <tr>
                    <th>Tabla</th>
                    <th>Registros activos</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($counts as $table => $count)
                    <tr>
                        <td><code style="background:var(--bg-tertiary);padding:3px 8px;border-radius:6px">{{ $table }}</code></td>
                        <td style="font-weight:500">{{ $count }}</td>
                        <td>
                            @if ($count > 0)
                                <span class="badge badge-green">Con datos</span>
                            @else
                                <span class="badge badge-gray">Vacía</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    const churchSelect = document.getElementById('church');
    const sourceDbSelect = document.getElementById('source_db');
    const form = document.getElementById('migracion-form');
    const btn = document.getElementById('migrar-btn');

    sourceDbSelect.addEventListener('change', function () {
        const db = this.value;
        churchSelect.innerHTML = '<option value="">Todas las iglesias</option>';

        if (!db) return;

        churchSelect.disabled = true;

        fetch('{{ route("config.migracion.churches") }}/?source_db=' + encodeURIComponent(db), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                (data.churches || []).forEach(church => {
                    const opt = document.createElement('option');
                    opt.value = church;
                    opt.textContent = church;
                    churchSelect.appendChild(opt);
                });
                churchSelect.disabled = false;
            })
            .catch(() => churchSelect.disabled = false);
    });

    form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.textContent = 'Migrando... (puede tardar unos minutos)';
    });
</script>
@endsection
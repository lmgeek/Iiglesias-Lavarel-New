@extends('layouts.app')

@section('title', 'Nuevo informe')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Nuevo informe de relacionamiento</h1>
        <p class="page-sub">Registra el encuentro realizado con tu discípulo</p>
    </div>
    <a href="{{ route('relacionamiento.index') }}" class="btn btn-secondary">Volver a mis discípulos</a>
</div>

@if (session('success'))
    <div class="alert alert-success mb-24">{{ session('success') }}</div>
@endif

<div class="card animate-fade-in-up" style="max-width:860px">
    <div class="card-header">
        <div style="display:flex;align-items:center;gap:12px">
            <span class="avatar">{{ mb_substr($disciple->fullname, 0, 1) }}</span>
            <div>
                <div class="card-title">{{ $disciple->fullname }}</div>
                <div class="card-sub">{{ $disciple->email }} · {{ $disciple->phone ?: 'Sin teléfono' }}</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('relacionamiento.informe.store', $disciple) }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="f_meet">Fecha del encuentro *</label>
                <input id="f_meet" type="date" name="f_meet" value="{{ old('f_meet') }}" required class="input @error('f_meet') input-error @enderror">
                @error('f_meet')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="suspended">¿Suspendido?</label>
                <select id="suspended" name="suspended" class="input">
                    <option value="No" @selected(old('suspended', 'No') === 'No')>No</option>
                    <option value="Si" @selected(old('suspended') === 'Si')>Sí</option>
                </select>
            </div>
            <div class="form-group full" id="why_suspended_group" style="display:none">
                <label for="why_suspended">Motivo de la suspensión</label>
                <textarea id="why_suspended" name="why_suspended" class="input" rows="2">{{ old('why_suspended') }}</textarea>
            </div>
            <div class="form-group">
                <label for="theme_meetings_id">Tema</label>
                <select id="theme_meetings_id" name="theme_meetings_id" class="input">
                    <option value="">Sin tema</option>
                    @foreach ($themes as $theme)
                        <option value="{{ $theme->id }}" @selected(old('theme_meetings_id') == $theme->id)>{{ $theme->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="other_theme">Otro tema</label>
                <input id="other_theme" type="text" name="other_theme" value="{{ old('other_theme') }}" class="input" placeholder="Si el tema no está en la lista">
            </div>
        </div>

        <h3 class="mt-24" style="margin-bottom:16px">Detalles del encuentro</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="initiative">Iniciativa</label>
                <input id="initiative" type="text" name="initiative" value="{{ old('initiative') }}" class="input" placeholder="Ejm: Levántate, Jesús sana">
            </div>
            <div class="form-group">
                <label for="reading">Lectura</label>
                <input id="reading" type="text" name="reading" value="{{ old('reading') }}" class="input" placeholder="Ejm: Juan 3:16">
            </div>
            <div class="form-group">
                <label for="testimonials">Testimonio</label>
                <input id="testimonials" type="text" name="testimonials" value="{{ old('testimonials') }}" class="input" placeholder="Testimonio del discípulo">
            </div>
            <div class="form-group">
                <label for="pray_together">¿Oraron juntos?</label>
                <input id="pray_together" type="text" name="pray_together" value="{{ old('pray_together') }}" class="input" placeholder="Ejm: Sí, por sanidad">
            </div>
            <div class="form-group">
                <label for="culminate">¿Cómo culminó?</label>
                <input id="culminate" type="text" name="culminate" value="{{ old('culminate') }}" class="input" placeholder="Estado final del encuentro">
            </div>
            <div class="form-group full">
                <label for="description">Descripción / observaciones</label>
                <textarea id="description" name="description" class="input" rows="3">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="mt-24">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><path d="M20 6L9 17l-5-5"/></svg>
                Guardar informe
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var suspended = document.getElementById('suspended');
        var group = document.getElementById('why_suspended_group');
        if (!suspended || !group) return;
        function toggle() {
            group.style.display = suspended.value === 'Si' ? '' : 'none';
        }
        suspended.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
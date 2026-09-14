@extends('layouts.auth')

@section('title', 'Completa tus datos')

@section('content')
<div class="auth-card animate-fade-in-up">
    <div class="text-center" style="margin-bottom:28px">
        <div class="auth-logo-circle" style="margin-bottom:20px">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="var(--cat-600)" stroke-width="1.5">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>
        <h1 class="auth-heading">Completa tus datos</h1>
        <p class="auth-sub">Antes de continuar necesitamos que actualices tu información obligatoria.</p>
    </div>

    @if (session('success'))
        <div class="alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('perfil.completar.store') }}" class="auth-form">
        @csrf

        <div class="field">
            <label for="fullname">Nombre completo *</label>
            <input id="fullname" type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" required class="input @error('fullname') input-error @enderror" placeholder="Ej: Juan Pérez">
            @error('fullname')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="doc_number">DNI *</label>
            <input id="doc_number" type="text" name="doc_number" inputmode="numeric" pattern="[0-9]*" value="{{ old('doc_number', $user->doc_number) }}" required class="input @error('doc_number') input-error @enderror" placeholder="Solo números">
            @error('doc_number')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="sex">Sexo *</label>
            <select id="sex" name="sex" required class="input @error('sex') input-error @enderror">
                <option value="">Selecciona…</option>
                <option value="M" {{ old('sex', $user->sex) === 'M' ? 'selected' : '' }}>Masculino</option>
                <option value="F" {{ old('sex', $user->sex) === 'F' ? 'selected' : '' }}>Femenino</option>
            </select>
            @error('sex')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="born_date">Fecha de nacimiento *</label>
            <input id="born_date" type="date" name="born_date" value="{{ old('born_date', optional($user->born_date)->format('Y-m-d')) }}" required max="{{ now()->subDay()->format('Y-m-d') }}" class="input @error('born_date') input-error @enderror">
            @error('born_date')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="phone">Teléfono *</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required class="input @error('phone') input-error @enderror" placeholder="Ej: +54 11 5555-5555">
            @error('phone')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 6L9 17l-5-5"/></svg>
            Guardar y continuar
        </button>
    </form>

    @php $isGmail = str_ends_with(strtolower((string) $user->email), '@gmail.com'); @endphp
    @if ($isGmail && empty($user->google_id))
        <div class="google-divider" style="margin-top:22px">
            <div class="google-divider-line"><span>o inicia sesión con Google</span></div>
            <a href="{{ route('auth.google') }}" class="btn-google btn-block">
                <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47a5.57 5.57 0 0 1-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09C3.26 21.31 7.31 24 12 24z"/><path fill="#FBBC05" d="M5.27 14.29a7.13 7.13 0 0 1 0-4.58V6.62H1.29a11.86 11.86 0 0 0 0 10.76l3.98-3.09z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.93 1.19 15.22 0 12 0 7.31 0 3.26 2.69 1.29 6.62l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75z"/></svg>
                Conectar cuenta de Google
            </a>
            <p class="auth-sub" style="margin-top:10px">Conectá tu cuenta para poder iniciar sesión con Google en el futuro.</p>
        </div>
    @endif
</div>
@endsection
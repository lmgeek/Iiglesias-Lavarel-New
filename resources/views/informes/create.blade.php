@extends('layouts.app')

@section('title', 'Nuevo informe')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Nuevo informe</h1>
        <p class="page-sub">Registra un informe de grupo de conexión</p>
    </div>
</div>

<div class="card animate-fade-in-up" style="max-width:760px">
    <form method="POST" action="{{ route('informes.store') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="f_meet">Fecha de reunión *</label>
                <input id="f_meet" type="date" name="f_meet" value="{{ old('f_meet', now()->format('Y-m-d')) }}" required class="input">
                @error('f_meet')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="celula">Célula</label>
                <input id="celula" type="text" name="celula" value="{{ old('celula') }}" class="input" placeholder="N° de célula">
            </div>
            <div class="form-group">
                <label for="lider">Líder</label>
                <input id="lider" type="text" name="lider" value="{{ old('lider') }}" class="input" placeholder="Nombre del líder">
            </div>
        </div>

        <div class="mt-24 form-grid">
            <div class="form-group">
                <label for="message_title">Título del mensaje</label>
                <input id="message_title" type="text" name="message_title" value="{{ old('message_title') }}" class="input" placeholder="Ejm: La fe que mueve montañas">
            </div>
            <div class="form-group">
                <label for="format">Formato</label>
                <input id="format" type="text" name="format" value="{{ old('format') }}" class="input" placeholder="Ejm: Presencial / Zoom">
            </div>
        </div>

        <div class="mt-24 form-grid">
            <div class="form-group">
                <label for="people_qty">Cantidad de asistentes</label>
                <input id="people_qty" type="number" name="people_qty" value="{{ old('people_qty') }}" min="0" class="input">
            </div>
            <div class="form-group">
                <label for="new_people_qty">Personas nuevas</label>
                <input id="new_people_qty" type="number" name="new_people_qty" value="{{ old('new_people_qty') }}" min="0" class="input">
            </div>
            <div class="form-group">
                <label for="mentoring">Discipulado</label>
                <input id="mentoring" type="text" name="mentoring" value="{{ old('mentoring') }}" class="input" placeholder="Ejm: En proceso">
            </div>
        </div>

        <div class="mt-24">
            <div class="form-group">
                <label for="who_meet">¿Quiénes se reunieron?</label>
                <textarea id="who_meet" name="who_meet" class="input" rows="2" placeholder="Nombres de los participantes">{{ old('who_meet') }}</textarea>
            </div>
        </div>

        <div class="mt-16">
            <div class="form-group">
                <label for="observations">Observaciones</label>
                <textarea id="observations" name="observations" class="input" rows="3" placeholder="Notas adicionales de la reunión">{{ old('observations') }}</textarea>
            </div>
        </div>

        <div class="mt-24 form-row">
            <div class="form-group" style="flex-direction:row;align-items:center;gap:8px">
                <input type="checkbox" id="suspended" name="suspended" value="Si" @checked(old('suspended') === 'Si') style="width:16px;height:16px">
                <label for="suspended" style="margin:0;font-weight:400">Suspender reunión</label>
            </div>
        </div>

        <div class="mt-24">
            <button type="submit" class="btn btn-primary">Guardar informe</button>
        </div>
    </form>
</div>
@endsection
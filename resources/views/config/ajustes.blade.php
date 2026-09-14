@extends('layouts.app')

@section('title', 'Ajustes')

@push('styles')
<style>
    .ajustes-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .ajustes-grid .full { grid-column: 1 / -1; }
    .social-input { display: flex; align-items: center; gap: 10px; }
    .social-input .social-badge {
        width: 40px; height: 40px; flex-shrink: 0; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: var(--bg-tertiary); color: var(--cat-700); border: 1px solid var(--border-light);
    }
    .social-badge svg { width: 18px; height: 18px; }
    .ajustes-section { border-top: 1px solid var(--border-light); padding-top: 22px; margin-top: 6px; }
    .ajustes-section:first-of-type { border-top: none; padding-top: 0; margin-top: 0; }
    .ajustes-section-title {
        font-family: 'Jost', sans-serif; font-size: 13px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--text-secondary); margin: 0 0 4px;
    }
    .ajustes-section-sub { font-size: 12.5px; color: var(--text-tertiary); margin: 0 0 18px; }
    .img-upload { display: flex; align-items: center; gap: 16px; }
    .img-upload .thumb {
        width: 76px; height: 76px; border-radius: 14px; overflow: hidden; flex-shrink: 0;
        background: var(--bg-tertiary); border: 1px solid var(--border-light);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .img-upload .thumb img { width: 100%; height: 100%; object-fit: contain; }
    .img-upload .thumb.fav { width: 48px; height: 48px; border-radius: 10px; }
    .img-upload .thumb.bg { width: 168px; height: 96px; border-radius: 12px; }
    .img-upload .thumb .thumb-ph {
        display: flex; align-items: center; justify-content: center;
        color: var(--text-tertiary);
    }
    @media (max-width: 640px) { .ajustes-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Ajustes</h1>
        <p class="page-sub">Configuración general de la iglesia</p>
    </div>
</div>

<div class="card animate-fade-in-up" style="width:100%;max-width:none">
    <div class="card-header">
        <div>
            <h2 class="card-title">Datos de la iglesia</h2>
            <p class="card-sub">Nombre, logo, favicon y datos de contacto</p>
        </div>
    </div>

    <form method="POST" action="{{ route('config.ajustes.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="ajustes-section full">
                <h3 class="ajustes-section-title">Datos de la iglesia</h3>
                <p class="ajustes-section-sub">Nombre y datos de contacto que se muestran en el login y pie del sistema.</p>

                <div class="form-group full">
                    <label for="church_name">Nombre de la iglesia *</label>
                    <input id="church_name" type="text" name="church_name" value="{{ old('church_name', $config->church_name) }}" required class="input" placeholder="Ej: Catedral Cristiana">
                    @error('church_name')<div class="field-error">{{ $message }}</div>@enderror
                    <p class="td-muted" style="font-size:12px;margin-top:6px">Se muestra en el login, sidebar y pestaña del navegador.</p>
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $config->email) }}" class="input" placeholder="Ej: contacto@iglesia.com">
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', $config->phone) }}" class="input" placeholder="Ej: +54 11 5555-5555">
                    @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="ajustes-section full">
                <h3 class="ajustes-section-title">Identidad visual</h3>
                <p class="ajustes-section-sub">Logo, favicon e imagen de fondo del login.</p>

                <div class="form-group full">
                    <label>Logo / icono</label>
                    <div class="img-upload">
                        <div class="thumb" id="logo_thumb">
                            <img id="logo_preview" src="{{ $config->logo ?? '' }}" alt="logo" {{ !empty($config->logo) ? '' : 'hidden' }}>
                            <div id="logo_ph" class="thumb-ph" {{ !empty($config->logo) ? 'hidden' : '' }}>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            </div>
                        </div>
                        <div style="flex:1">
                            <label for="logo_input" class="btn btn-secondary" style="cursor:pointer;font-weight:500">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Subir imagen
                            </label>
                            <input type="file" id="logo_input" name="logo" accept="image/*" hidden value="">
                            <span id="logo_name" class="td-muted" style="font-size:12.5px;margin-left:8px"></span>
                            <p class="td-muted" style="font-size:12px;margin-top:6px">PNG, JPG o WEBP. Se muestra en el login y sidebar.</p>
                            @error('logo')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Favicon (icono de pestaña)</label>
                    <div class="img-upload">
                        <div class="thumb fav" id="favicon_thumb">
                            <img id="favicon_preview" src="{{ $config->favicon ?? '' }}" alt="favicon" {{ !empty($config->favicon) ? '' : 'hidden' }}>
                            <div id="favicon_ph" class="thumb-ph" {{ !empty($config->favicon) ? 'hidden' : '' }}>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            </div>
                        </div>
                        <div style="flex:1">
                            <label for="favicon_input" class="btn btn-secondary" style="cursor:pointer;font-weight:500">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Subir imagen
                            </label>
                            <input type="file" id="favicon_input" name="favicon" accept="image/*" hidden>
                            <span id="favicon_name" class="td-muted" style="font-size:12.5px;margin-left:8px"></span>
                            <p class="td-muted" style="font-size:12px;margin-top:6px">Se muestra en la pestaña del navegador. Recomendado 32×32.</p>
                            @error('favicon')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Imagen de fondo del login</label>
                    <div class="img-upload">
                        <div class="thumb bg" id="login_bg_thumb">
                            <img id="login_bg_preview" src="{{ $config->login_bg ?? '' }}" alt="login_bg" {{ !empty($config->login_bg) ? '' : 'hidden' }}>
                            <div id="login_bg_ph" class="thumb-ph" {{ !empty($config->login_bg) ? 'hidden' : '' }}>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5-9 9"/></svg>
                            </div>
                        </div>
                        <div style="flex:1">
                            <label for="login_bg_input" class="btn btn-secondary" style="cursor:pointer;font-weight:500">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Subir imagen
                            </label>
                            <input type="file" id="login_bg_input" name="login_bg" accept="image/*" hidden>
                            <span id="login_bg_name" class="td-muted" style="font-size:12.5px;margin-left:8px"></span>
                            <p class="td-muted" style="font-size:12px;margin-top:6px">Se muestra en el panel izquierdo del login (desktop). Recomendado amplio (paisaje).</p>
                            @error('login_bg')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="ajustes-section full">
                <h3 class="ajustes-section-title">Redes sociales</h3>
                <p class="ajustes-section-sub">Enlaces a los perfiles públicos de la iglesia.</p>

                <div class="form-group">
                    <label for="instagram">Instagram</label>
                    <div class="social-input">
                        <span class="social-badge"><svg fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg></span>
                        <input id="instagram" type="text" name="instagram" value="{{ old('instagram', $config->instagram) }}" class="input" placeholder="Ej: @iglesia">
                    </div>
                    @error('instagram')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="facebook">Facebook</label>
                    <div class="social-input">
                        <span class="social-badge"><svg fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 21v-7h2.4l.4-3h-2.8V9.1c0-.9.3-1.6 2-1.6h1V4.8c-.4-.1-1.6-.2-2.5-.2-2.6 0-4.4 1.6-4.4 4.6V11H7v3h2.6v7h3.9z"/></svg></span>
                        <input id="facebook" type="text" name="facebook" value="{{ old('facebook', $config->facebook) }}" class="input" placeholder="Ej: facebook.com/iglesia">
                    </div>
                    @error('facebook')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="tiktok">TikTok</label>
                    <div class="social-input">
                        <span class="social-badge"><svg fill="currentColor" viewBox="0 0 24 24"><path d="M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 1 1-2.59-2.59c.27 0 .53.04.77.12V9.77a5.76 5.76 0 0 0-.77-.05 5.68 5.68 0 1 0 5.68 5.68V9.36a7.35 7.35 0 0 0 4.3 1.38V7.65a4.3 4.3 0 0 1-3.24-1.83z"/></svg></span>
                        <input id="tiktok" type="text" name="tiktok" value="{{ old('tiktok', $config->tiktok) }}" class="input" placeholder="Ej: @iglesia">
                    </div>
                    @error('tiktok')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="youtube">YouTube</label>
                    <div class="social-input">
                        <span class="social-badge"><svg fill="currentColor" viewBox="0 0 24 24"><path d="M21.5 7.2a2.5 2.5 0 0 0-1.76-1.77C18.1 5 12 5 12 5s-6.1 0-7.74.43A2.5 2.5 0 0 0 2.5 7.2 26.2 26.2 0 0 0 2 12c0 1.6.17 3.2.5 4.8a2.5 2.5 0 0 0 1.76 1.77C5.9 19 12 19 12 19s6.1 0 7.74-.43a2.5 2.5 0 0 0 1.76-1.77c.33-1.6.5-3.2.5-4.8 0-1.6-.17-3.2-.5-4.8zM10 15.5v-7l6 3.5-6 3.5z"/></svg></span>
                        <input id="youtube" type="text" name="youtube" value="{{ old('youtube', $config->youtube) }}" class="input" placeholder="Ej: youtube.com/@iglesia">
                    </div>
                    @error('youtube')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mt-16">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function bindUpload(inputId, nameId, previewId, placeholderId) {
        var input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('change', function () {
            var file = input.files[0];
            document.getElementById(nameId).textContent = file ? file.name : '';
            var preview = document.getElementById(previewId);
            var placeholder = document.getElementById(placeholderId);
            if (!file) {
                return;
            }
            if (preview._objUrl) URL.revokeObjectURL(preview._objUrl);
            preview._objUrl = URL.createObjectURL(file);
            preview.src = preview._objUrl;
            preview.removeAttribute('hidden');
            if (placeholder) placeholder.setAttribute('hidden', '');
        });
    }
    bindUpload('logo_input', 'logo_name', 'logo_preview', 'logo_ph');
    bindUpload('favicon_input', 'favicon_name', 'favicon_preview', 'favicon_ph');
    bindUpload('login_bg_input', 'login_bg_name', 'login_bg_preview', 'login_bg_ph');
</script>
@endpush
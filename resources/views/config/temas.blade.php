@extends('layouts.app')

@section('title', 'Temas')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Temas</h1>
        <p class="page-sub">Temáticas para reuniones de discipulado</p>
    </div>
</div>

<div class="card animate-fade-in-up mb-24" style="max-width:700px">
    <div class="card-header">
        <div>
            <h2 class="card-title">Nuevo tema</h2>
        </div>
    </div>
    <form method="POST" action="{{ route('config.temas.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label for="classname">Tema *</label>
                <input id="classname" type="text" name="classname" value="{{ old('classname') }}" required class="input" placeholder="Ej: La fe que mueve montañas">
                @error('classname')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Imagen</label>
                <div class="upload-field">
                    <input type="file" id="theme_image" name="image" accept="image/*" hidden>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('theme_image').click()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        Subir imagen
                    </button>
                    <span id="theme_image_name" class="td-muted" style="font-size:12.5px;margin-left:4px"></span>
                    @error('image')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div id="theme_image_preview" hidden style="margin-top:10px">
                    <img id="theme_image_preview_img" alt="Vista previa" style="max-width:200px;max-height:120px;border-radius:10px;object-fit:cover;border:1px solid var(--border-light)">
                </div>
            </div>
        </div>
        <div class="mt-16">
            <div class="form-group">
                <label for="youtube">Link de YouTube <span class="td-muted" style="font-weight:400">(opcional)</span></label>
                <input id="youtube" type="text" name="youtube" value="{{ old('youtube') }}" class="input" placeholder="https://www.youtube.com/watch?v=..." style="font-size:13.5px">
                @error('youtube')<div class="field-error">{{ $message }}</div>@enderror
                <p class="td-muted" style="font-size:12px;margin-top:6px">Acepta todas las variantes: youtube.com/watch, youtu.be, /shorts/ y más.</p>
            </div>
        </div>
        <div class="mt-16">
            <button type="submit" class="btn btn-primary">Crear tema</button>
        </div>
    </form>
</div>

<div class="card animate-fade-in-up">
    <div class="card-header">
        <div>
            <h2 class="card-title">Listado de temas</h2>
            <p class="card-sub">{{ $temas->count() }} temas registrados</p>
        </div>
    </div>

    @if ($temas->isEmpty())
        <div class="empty">
            <div class="empty-title">Sin temas</div>
            <p>Crea el primer tema desde el formulario de arriba.</p>
        </div>
    @else
        <div class="table-wrap">
            <table class="rows">
                <thead>
                    <tr>
                        <th>Tema</th>
                        <th>Imagen</th>
                        <th>YouTube</th>
                        <th>Creado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($temas as $tema)
                        <tr>
                            <td style="font-weight:500">{{ $tema->classname }}</td>
                            <td>
                                @if ($tema->image)
                                    <img src="{{ $tema->image }}" alt="{{ $tema->classname }}" style="width:56px;height:40px;object-fit:cover;border-radius:8px;border:1px solid var(--border-light)">
                                @else
                                    <span class="td-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($tema->youtube)
                                    <a href="{{ $tema->youtubeUrl() }}" target="_blank" rel="noopener" class="btn btn-sm btn-secondary" title="{{ $tema->youtube }}">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="margin-right:4px"><path d="M23 12s0-3.3-.4-4.9c-.2-.9-.9-1.6-1.8-1.8C19.2 5 12 5 12 5s-7.2 0-8.8.3c-.9.2-1.6.9-1.8 1.8C1 8.7 1 12 1 12s0 3.3.4 4.9c.2.9.9 1.6 1.8 1.8C4.8 19 12 19 12 19s7.2 0 8.8-.3c.9-.2 1.6-.9 1.8-1.8.4-1.6.4-4.9.4-4.9zM10 15.5v-7l6 3.5-6 3.5z"/></svg>
                                        Ver
                                    </a>
                                @else
                                    <span class="td-muted">—</span>
                                @endif
                            </td>
                            <td class="td-muted">{{ $tema->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const themeImage = document.getElementById('theme_image');
    const themeImageName = document.getElementById('theme_image_name');
    const themeImagePreview = document.getElementById('theme_image_preview');
    const themeImagePreviewImg = document.getElementById('theme_image_preview_img');

    themeImage.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        themeImageName.textContent = file.name;

        const reader = new FileReader();
        reader.onload = function (e) {
            themeImagePreviewImg.src = e.target.result;
            themeImagePreview.hidden = false;
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush
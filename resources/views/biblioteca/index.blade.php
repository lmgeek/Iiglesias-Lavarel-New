@extends('layouts.app')

@section('title', 'Biblioteca')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Biblioteca</h1>
        <p class="page-sub">Materiales y recursos para tu discipulado</p>
    </div>
</div>

@if ($canCreate)
<div class="card animate-fade-in-up mb-24">
    <button type="button" class="card-header" onclick="this.parentElement.querySelector('.agregar-libro').style.display = this.parentElement.querySelector('.agregar-libro').style.display === 'none' ? 'block' : 'none'" style="width:100%;text-align:left;border:none;background:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:0;gap:12px">
        <div>
            <h2 class="card-title">Agregar libro</h2>
            <p class="card-sub">Sube un documento a la biblioteca (PDF, Word, PowerPoint, Excel...)</p>
        </div>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--cat-600)"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    </button>
    <div class="agregar-libro" style="display:none;margin-top:16px">
        <form method="POST" action="{{ route('biblioteca.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="title">Título *</label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required class="input" placeholder="Ej: Manual del Discípulo">
                    @error('title')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="category">Categoría</label>
                    <input id="category" type="text" name="category" value="{{ old('category') }}" class="input" placeholder="Ej: discipulado">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description" rows="2" class="input" placeholder="Breve descripción del material">{{ old('description') }}</textarea>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label for="file">Archivo *</label>
                    <input id="file" type="file" name="file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.odp,.odt,.xls,.xlsx,.txt,.csv" class="input">
                    <div style="font-size:12px;color:var(--text-tertiary);margin-top:6px">Formatos permitidos: PDF, DOC, DOCX, PPT, PPTX, ODP, ODT, XLS, XLSX, TXT, CSV · Máx 25 MB</div>
                    @error('file')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Agregar a la biblioteca</button>
        </form>
    </div>
</div>
@endif

@if ($materials->isEmpty())
    <div class="card">
        <div class="empty">
            <div class="empty-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div class="empty-title">Biblioteca vacía</div>
            <p>Aún no se han agregado materiales.</p>
        </div>
    </div>
@else
    <div class="stats-grid">
        @foreach ($materials as $material)
            <div class="card animate-fade-in-up" style="animation-delay:{{ $loop->index * 0.05 }}s">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
                    <span class="avatar" style="background:rgba(212,138,48,0.15);color:var(--cat-700)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </span>
                    <span class="badge badge-cat" style="text-transform:uppercase">{{ $material->file_type ?? 'DOC' }}</span>
                </div>
                <h3 style="font-size:16px;font-weight:500;line-height:1.4">{{ $material->title }}</h3>
                <p style="color:var(--text-secondary);font-size:13px;margin-top:6px;line-height:1.6">{{ $material->description }}</p>
                <div style="margin-top:8px">
                    @if ($material->category)
                        <span class="badge badge-gray" style="text-transform:capitalize">{{ $material->category }}</span>
                    @endif
                    <span style="font-size:12px;color:var(--text-tertiary);margin-left:8px">{{ $material->file_name }}</span>
                </div>
                <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
                    <a href="{{ Storage::disk('public')->url($material->file_path) }}" target="_blank" rel="noopener" class="btn btn-sm btn-secondary">
                        Ver
                    </a>
                    <a href="{{ route('biblioteca.download', $material->id) }}" class="btn btn-sm btn-primary">
                        Descargar
                    </a>
                    @if ($canDelete)
                        <form method="POST" action="{{ route('biblioteca.destroy', $material->id) }}" onsubmit="return confirm('¿Eliminar este libro de la biblioteca?')" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
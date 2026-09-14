<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BibliotecaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BibliotecaController extends Controller
{
    public function index()
    {
        $materials = BibliotecaItem::whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('biblioteca.index', [
            'materials' => $materials,
            'canCreate' => Auth::user()->can('Biblioteca.create'),
            'canDelete' => Auth::user()->can('Biblioteca.delete'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('Biblioteca.create'), 403, 'No tienes permisos para agregar libros.');

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,odp,odt,xls,xlsx,txt,csv',
                'max:25600',
            ],
        ]);

        $file = $request->file('file');
        $storedName = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('biblioteca', $storedName, 'public');

        BibliotecaItem::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => strtoupper($file->getClientOriginalExtension()),
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Libro agregado a la biblioteca correctamente.');
    }

    public function destroy(BibliotecaItem $material)
    {
        abort_unless(Auth::user()->can('Biblioteca.delete'), 403, 'No tienes permisos para eliminar libros.');

        $material->delete();

        return back()->with('success', 'Libro eliminado de la biblioteca.');
    }

    public function download(BibliotecaItem $material)
    {
        if (! Auth::user()->can('Biblioteca.view')) {
            abort(403, 'No tienes permisos para descargar libros.');
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($material->file_path)) {
            abort(404, 'El archivo ya no está disponible.');
        }

        return $disk->download($material->file_path, $material->file_name);
    }
}

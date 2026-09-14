<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BibliotecaItem;
use Illuminate\Support\Facades\Storage;

class BibliotecaController extends Controller
{
    public function index()
    {
        $materials = BibliotecaItem::whereNull('deleted_at')
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'type' => $item->file_type,
                    'category' => $item->category,
                    'url' => Storage::disk('public')->url($item->file_path),
                    'download_url' => route('biblioteca.download', $item->id),
                    'created_at' => $item->created_at?->toISOString(),
                ];
            });

        return response()->json($materials);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Temas\StoreTemaRequest;
use App\Http\Requests\Api\V1\Temas\UpdateTemaRequest;
use App\Http\Resources\Api\V1\MeetingsThemeCollection;
use App\Http\Resources\Api\V1\MeetingsThemeResource;
use App\Models\MeetingsTheme;
use App\Services\ImgbbService;
use Illuminate\Http\Request;

class TemasController extends Controller
{
    public function index(Request $request)
    {
        $query = MeetingsTheme::query();

        if ($request->has('search')) {
            $query->where('classname', 'like', '%'.$request->search.'%');
        }

        $perPage = $request->get('per_page', 15);
        $themes = $query->orderBy('classname')->paginate($perPage);

        return new MeetingsThemeCollection($themes);
    }

    public function store(StoreTemaRequest $request, ImgbbService $imgbb)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $upload = $imgbb->upload($request->file('image'));

            if ($upload === null) {
                return response()->json([
                    'message' => 'No se pudo subir la imagen a imgBB. Verifica la clave IMGBB_API_KEY.',
                    'errors' => ['image' => ['Subida a imgBB falló']],
                ], 422);
            }

            $data['image'] = $upload['url'];
            $data['image_delete_url'] = $upload['delete_url'];
        } elseif ($request->has('image')) {
            $data['image'] = $request->image;
        }

        $theme = MeetingsTheme::create($data);

        return new MeetingsThemeResource($theme);
    }

    public function show(MeetingsTheme $tema)
    {
        return new MeetingsThemeResource($tema);
    }

    public function update(UpdateTemaRequest $request, MeetingsTheme $tema, ImgbbService $imgbb)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior de imgBB si existe delete_url
            if ($tema->image_delete_url) {
                $imgbb->deleteByUrl($tema->image_delete_url);
            }

            $upload = $imgbb->upload($request->file('image'));

            if ($upload === null) {
                return response()->json([
                    'message' => 'No se pudo subir la imagen a imgBB. Verifica la clave IMGBB_API_KEY.',
                    'errors' => ['image' => ['Subida a imgBB falló']],
                ], 422);
            }

            $data['image'] = $upload['url'];
            $data['image_delete_url'] = $upload['delete_url'];
        } elseif ($request->has('image')) {
            $data['image'] = $request->image;
        }

        $tema->update($data);

        return new MeetingsThemeResource($tema);
    }

    public function destroy(MeetingsTheme $tema, ImgbbService $imgbb)
    {
        if ($tema->image_delete_url) {
            $imgbb->deleteByUrl($tema->image_delete_url);
        }

        $tema->delete();

        return response()->json(['message' => 'Tema eliminado']);
    }
}

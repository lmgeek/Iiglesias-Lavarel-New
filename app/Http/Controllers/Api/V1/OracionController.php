<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Intercesion;
use Illuminate\Http\Request;

class OracionController extends Controller
{
    public function index(Request $request)
    {
        $query = Intercesion::whereNull('deleted_at');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $perPage = $request->get('per_page', 15);
        $intercesion = $query->orderBy('calendar_day')->paginate($perPage);

        return response()->json($intercesion);
    }

    public function list(Request $request)
    {
        $intercesion = Intercesion::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('calendar_day')
            ->get();

        return response()->json($intercesion);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'calendar_day' => 'required|integer|between:1,31',
            'email' => 'required|email',
            'notifications' => 'boolean',
        ]);

        $intercesion = Intercesion::create($data);

        return response()->json($intercesion, 201);
    }

    public function update(Request $request, Intercesion $intercesion)
    {
        $data = $request->validate([
            'calendar_day' => 'integer|between:1,31',
            'email' => 'email',
            'notifications' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $intercesion->update($data);

        return response()->json($intercesion);
    }

    public function destroy(Intercesion $intercesion)
    {
        $intercesion->delete();

        return response()->json(['message' => 'Intercesión eliminada']);
    }
}

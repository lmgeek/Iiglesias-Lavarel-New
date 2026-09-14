<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use Illuminate\Http\Request;

class MinistriesController extends Controller
{
    public function index()
    {
        $ministries = Ministry::whereNull('deleted_at')
            ->orderBy('name')
            ->withCount('users')
            ->get();

        return response()->json($ministries);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ministries,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $ministry = Ministry::create($request->only(['name', 'description']));

        return response()->json($ministry, 201);
    }

    public function show(Ministry $ministry)
    {
        return response()->json($ministry);
    }

    public function update(Request $request, Ministry $ministry)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ministries,name,'.$ministry->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $ministry->update($request->only(['name', 'description']));

        return response()->json($ministry);
    }

    public function destroy(Ministry $ministry)
    {
        $ministry->delete();

        return response()->json(['message' => 'Ministerio eliminado']);
    }
}

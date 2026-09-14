<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\Request;

class SedesController extends Controller
{
    public function index()
    {
        $sedes = Sede::whereNull('deleted_at')
            ->orderBy('name')
            ->withCount('users')
            ->get();

        return response()->json($sedes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $sede = Sede::create($request->only(['name', 'address']));

        return response()->json($sede, 201);
    }

    public function show(Sede $sede)
    {
        return response()->json($sede);
    }

    public function update(Request $request, Sede $sede)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $sede->update($request->only(['name', 'address']));

        return response()->json($sede);
    }

    public function destroy(Sede $sede)
    {
        $sede->delete();

        return response()->json(['message' => 'Sede eliminada']);
    }
}

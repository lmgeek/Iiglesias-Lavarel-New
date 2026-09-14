<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MigracionResultResource;
use App\Services\MigracionService;
use Illuminate\Http\Request;

class MigracionController extends Controller
{
    protected MigracionService $migracionService;

    public function __construct(MigracionService $migracionService)
    {
        $this->migracionService = $migracionService;
    }

    public function index()
    {
        $databases = $this->migracionService->getAvailableDatabases();
        $current = config('database.connections.mysql.database');

        return response()->json([
            'databases' => $databases,
            'current' => $current,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_db' => 'required|string',
        ]);

        try {
            $result = $this->migracionService->migrate($request->source_db);

            return new MigracionResultResource($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

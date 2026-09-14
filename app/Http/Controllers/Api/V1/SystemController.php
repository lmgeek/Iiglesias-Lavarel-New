<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="System", description="Endpoints de sistema")
 */
class SystemController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/system/status",
     *     summary="Estado del sistema",
     *     description="Verifica versión, conexión a base de datos y estado general del sistema",
     *     tags={"System"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Sistema operativo",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="ok"),
     *             @OA\Property(property="service", type="string", example="catedral-api"),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="environment", type="string", example="local"),
     *             @OA\Property(
     *                 property="database",
     *                 type="object",
     *                 @OA\Property(property="connected", type="boolean", example=true),
     *                 @OA\Property(property="provider", type="string", example="mysql"),
     *                 @OA\Property(property="latencyMs", type="number", format="float", example=4.5),
     *                 @OA\Property(property="error", type="string", nullable=true)
     *             ),
     *             @OA\Property(property="uptimeSeconds", type="integer", example=3600),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2026-08-19T15:30:00.000Z")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=503,
     *         description="Base de datos no disponible"
     *     )
     * )
     */
    public function status()
    {
        $dbStart = microtime(true);
        $dbConnected = false;
        $dbError = null;

        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Exception $e) {
            $dbError = $e->getMessage();
        }

        $latency = round((microtime(true) - $dbStart) * 1000, 2);

        return response()->json([
            'status' => $dbConnected ? 'ok' : 'degraded',
            'service' => 'catedral-api',
            'version' => '1.0.0',
            'environment' => app()->environment(),
            'database' => [
                'connected' => $dbConnected,
                'provider' => 'mysql',
                'latencyMs' => $latency,
                'error' => $dbError,
            ],
            'uptimeSeconds' => round((microtime(true) - LARAVEL_START) ?? 0),
            'timestamp' => now()->toISOString(),
        ], $dbConnected ? 200 : 503);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Config\UpdateChurchConfigRequest;
use App\Http\Resources\Api\V1\ChurchConfigResource;
use App\Services\ChurchConfigService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Config", description="Configuración de iglesia")
 */
class ConfigController extends Controller
{
    protected ChurchConfigService $configService;

    public function __construct(ChurchConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/config/church",
     *     summary="Obtener configuración de la iglesia",
     *     tags={"Config"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Configuración actual",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="church_name", type="string", example="Catedral Cristiana"),
     *             @OA\Property(property="logo", type="string", nullable=true, example="data:image/png;base64,..."),
     *             @OA\Property(property="favicon", type="string", nullable=true, example="data:image/png;base64,...")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return new ChurchConfigResource($this->configService->getConfig());
    }

    /**
     * @OA\Put(
     *     path="/api/v1/config/church",
     *     summary="Actualizar configuración de la iglesia",
     *     tags={"Config"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="church_name", type="string", example="Catedral Cristiana"),
     *             @OA\Property(property="logo", type="string", format="binary", description="Imagen en base64"),
     *             @OA\Property(property="favicon", type="string", format="binary", description="Favicon en base64")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Configuración actualizada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="church_name", type="string"),
     *             @OA\Property(property="logo", type="string"),
     *             @OA\Property(property="favicon", type="string")
     *         )
     *     )
     * )
     */
    public function update(UpdateChurchConfigRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            // Si se sube archivo, convertir a base64
            $file = $request->file('logo');
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $mime = $file->getMimeType();
            $data['logo'] = "data:{$mime};base64,{$base64}";
        } elseif ($request->has('logo')) {
            $data['logo'] = $request->logo;
        }

        $config = $this->configService->updateConfig($data);

        return new ChurchConfigResource($config);
    }
}

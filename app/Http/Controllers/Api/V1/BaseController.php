<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Catedral Cristiana API",
 *     version="1.0.0",
 *     description="API REST para la plataforma de discipulado y gestión ministerial de Catedral Cristiana",
 *
 *     @OA\Contact(
 *         email="noreply@catedralcristiana.com",
 *         name="Catedral Cristiana"
 *     ),
 *
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     ),
 *
 *     @OA\Server(
 *         url="/api/v1",
 *         description="Development Server"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Laravel Sanctum Personal Access Token"
 * )
 *
 * @OA\Tag(name="Auth", description="Endpoints de autenticación")
 * @OA\Tag(name="System", description="Endpoints de sistema")
 * @OA\Tag(name="Config", description="Configuración de iglesia")
 * @OA\Tag(name="Migracion", description="Migración de base de datos legacy")
 * @OA\Tag(name="Temas", description="Temas de reunión")
 * @OA\Tag(name="Roles", description="Gestión de roles y permisos")
 * @OA\Tag(name="Usuarios", description="Gestión de usuarios")
 * @OA\Tag(name="Relacionamiento", description="Discipulado y relacionamiento")
 * @OA\Tag(name="Informes", description="Informes de célula")
 * @OA\Tag(name="Miembros", description="Gestión de miembros")
 * @OA\Tag(name="Oracion", description="Cadena de oración")
 * @OA\Tag(name="Calendario", description="Eventos del calendario")
 * @OA\Tag(name="Reportes", description="Reportes y dashboard")
 * @OA\Tag(name="Perfil", description="Perfil de usuario")
 * @OA\Tag(name="WebPush", description="Notificaciones push")
 */
class BaseController
{
    //
}

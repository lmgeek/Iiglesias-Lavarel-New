<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\GoogleLoginRequest;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Auth\SetupPasswordRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Auth", description="Endpoints de autenticación")
 */
class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="Iniciar sesión",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email","password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="admin@catedralcristiana.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Admin123!")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="access_token", type="string", example="1|abc123..."),
     *             @OA\Property(property="refresh_token", type="string", example="1|def456..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=86400),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login/google",
     *     summary="Iniciar sesión con Google",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"access_token"},
     *
     *             @OA\Property(property="access_token", type="string", example="ya29.a0AfH6SMC...")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login con Google exitoso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="refresh_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=86400),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Error en autenticación Google")
     * )
     */
    public function googleLogin(GoogleLoginRequest $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);

            $profile = [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ];

            $result = $this->authService->loginWithGoogle($profile['id'], $profile);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/google/callback",
     *     summary="Callback de Google OAuth",
     *     tags={"Auth"},
     *
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="Código de autorización de Google",
     *         required=true,
     *         schema={"type": "string"}
     *     ),
     *
     *     @OA\Response(
     *         response=302,
     *         description="Redirección al frontend con tokens"
     *     )
     * )
     */
    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $profile = [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ];

            $result = $this->authService->loginWithGoogle($profile['id'], $profile);

            // Redirect al frontend con tokens
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $redirectUrl = $frontendUrl.'/auth/callback?'.http_build_query([
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
            ]);

            return redirect($redirectUrl);
        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

            return redirect($frontendUrl.'/login?error='.urlencode($e->getMessage()));
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     summary="Renovar access token",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *
     *             @OA\Property(property="refresh_token", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tokens renovados",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="refresh_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=86400)
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Refresh token inválido o expirado")
     * )
     */
    public function refresh(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string']);

        try {
            $result = $this->authService->refreshTokens($request->refresh_token);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     summary="Cerrar sesión",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada correctamente",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Sesión cerrada correctamente")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $currentToken = $request->bearerToken();

        $this->authService->logout($user, $currentToken);

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout-all",
     *     summary="Cerrar todas las sesiones",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Todas las sesiones cerradas",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Todas las sesiones cerradas")
     *         )
     *     )
     * )
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();
        $this->authService->logoutAll($user);

        return response()->json(['message' => 'Todas las sesiones cerradas']);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     summary="Usuario autenticado actual",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Datos del usuario",
     *
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles', 'permissions');

        return new UserResource($user);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/setup-status",
     *     summary="Verificar si el sistema necesita configuración inicial",
     *     tags={"Auth"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Estado del setup",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="needsSetup", type="boolean", example=true),
     *             @OA\Property(property="usersCount", type="integer", example=0)
     *         )
     *     )
     * )
     */
    public function setupStatus()
    {
        $status = $this->authService->checkSetupStatus();

        return response()->json($status);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/setup-password",
     *     summary="Configurar contraseña inicial / reset de usuario migrado",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="fullname", type="string", example="Administrador"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@iglesia.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=6, example="Admin123!")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado / contraseña actualizada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="userId", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(response=403, description="El sistema ya tiene usuarios registrados")
     * )
     */
    public function setupPassword(SetupPasswordRequest $request)
    {
        try {
            $user = $this->authService->setupPassword($request->validated());
            $result = $this->authService->createTokens($user);

            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/user-status",
     *     summary="Verificar si un usuario necesita cambiar contraseña",
     *     tags={"Auth"},
     *
     *     @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Email del usuario",
     *     required=true,
     *     schema={"type": "string", "format": "email"}
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Estado del usuario",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="exists", type="boolean", example=true),
     *             @OA\Property(property="mustChangePassword", type="boolean", example=false)
     *         )
     *     )
     * )
     */
    public function userStatus(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = $this->authService->checkUserStatus($request->email);

        return response()->json($status);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/forgot-password",
     *     summary="Solicitar enlace de restablecimiento de contraseña",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="usuario@iglesia.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Si el email existe, se enviará el enlace",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Si el email existe, recibirás instrucciones para restablecer tu contraseña.")
     *         )
     *     )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->sendPasswordResetLink($request->email);

        return response()->json(['message' => 'Si el email existe, recibirás instrucciones para restablecer tu contraseña.']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/reset-password",
     *     summary="Restablecer contraseña con token",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *         required={"email", "token", "password"},
     *
     *         @OA\Property(property="email", type="string", format="email"),
     *         @OA\Property(property="token", type="string"),
     *         @OA\Property(property="password", type="string", format="password", minLength=6)
     *     )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña restablecida correctamente",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Contraseña restablecida correctamente")
     *         )
     *     ),
     *
     *     @OA\Response(response=400, description="Token inválido o expirado")
     * )
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->authService->resetPassword($request->email, $request->token, $request->password);

            return response()->json(['message' => 'Contraseña restablecida correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

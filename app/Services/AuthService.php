<?php

namespace App\Services;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthService
{
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new \Exception('Credenciales inválidas');
        }

        if (! $user->is_active) {
            throw new \Exception('Usuario inactivo');
        }

        return $this->createTokens($user);
    }

    public function loginWithGoogle(string $googleId, array $profile): array
    {
        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            $user = User::where('email', $profile['email'])->first();

            if ($user) {
                $user->update(['google_id' => $googleId]);
            } else {
                $user = User::create([
                    'uuid' => (string) Str::uuid(),
                    'fullname' => $profile['name'] ?? $profile['email'],
                    'email' => $profile['email'],
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(32)),
                    'born_date' => now()->format('Y-m-d'),
                    'sex' => '',
                    'is_active' => true,
                ]);

                // Asignar rol por defecto
                $defaultRole = Role::where('name', 'Usuario')->first();
                if ($defaultRole) {
                    $user->assignRole($defaultRole);
                }
            }
        }

        return $this->createTokens($user);
    }

    public function createTokens(User $user): array
    {
        // Revocar tokens anteriores si se quiere solo una sesión activa
        // $this->revokeAllTokens($user);

        $accessToken = $user->createToken('access-token', ['*'], now()->addHours(24))->plainTextToken;
        $refreshToken = $user->createToken('refresh-token', ['refresh'], now()->addDays(30))->plainTextToken;

        return [
            'user' => $user->load('roles', 'permissions'),
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 86400, // 24 horas
        ];
    }

    public function refreshTokens(string $refreshToken): array
    {
        $token = PersonalAccessToken::findToken($refreshToken);

        if (! $token || ! $token->can('refresh')) {
            throw new \Exception('Refresh token inválido');
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            throw new \Exception('Refresh token expirado');
        }

        $user = $token->tokenable;

        if (! $user || ! $user->is_active) {
            throw new \Exception('Usuario no encontrado o inactivo');
        }

        // Revocar el refresh token usado
        $token->delete();

        return $this->createTokens($user);
    }

    public function logout(User $user, ?string $currentToken = null): void
    {
        if ($currentToken) {
            $token = PersonalAccessToken::findToken($currentToken);
            if ($token && $token->tokenable_id === $user->id) {
                $token->delete();
            }
        } else {
            $user->tokens()->where('name', 'access-token')->delete();
        }
    }

    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }

    public function setupPassword(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $userCount = User::whereNull('deleted_at')->count();

            if ($userCount > 0) {
                // Verificar si es reset de usuario migrado
                $existing = User::where('email', $data['email'])->first();

                if ($existing && $existing->must_change_password) {
                    $existing->update([
                        'password' => Hash::make($data['password']),
                        'must_change_password' => false,
                    ]);

                    return $existing;
                }

                throw new \Exception('El sistema ya tiene usuarios registrados');
            }

            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'fullname' => $data['fullname'],
                'email' => $data['email'],
                'doc_number' => $data['email'],
                'born_date' => now()->format('Y-m-d'),
                'sex' => '',
                'password' => Hash::make($data['password']),
                'is_active' => true,
                'must_change_password' => false,
            ]);

            // Asignar rol Admin
            $adminRole = Role::where('name', 'Admin')->first();
            if ($adminRole) {
                $user->assignRole($adminRole);
            }

            // Crear permisos por defecto si no existen
            $this->ensureDefaultPermissions($adminRole);

            return $user;
        });
    }

    public function setupPasswordForMigratedUser(User $user, string $password): User
    {
        $user->update([
            'password' => Hash::make($password),
            'must_change_password' => false,
        ]);

        return $user;
    }

    public function checkSetupStatus(): array
    {
        $count = User::whereNull('deleted_at')->count();

        return [
            'needsSetup' => $count === 0,
            'usersCount' => $count,
        ];
    }

    public function checkUserStatus(string $email): array
    {
        $user = User::where('email', $email)->whereNull('deleted_at')->first();

        if (! $user) {
            return ['exists' => false];
        }

        return [
            'exists' => true,
            'mustChangePassword' => $user->must_change_password,
        ];
    }

    public function sendPasswordResetLink(string $email): void
    {
        $user = User::where('email', $email)->whereNull('deleted_at')->first();

        if (! $user || ! $user->is_active) {
            // No revelar si el email existe
            return;
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        Mail::to($user->email)->send(new PasswordResetMail($token, $user));
    }

    public function resetPassword(string $email, string $token, string $password): void
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $record || ! hash_equals($record->token, hash('sha256', $token))) {
            throw new \Exception('Token inválido o expirado');
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->update(['password' => Hash::make($password)]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Revocar todos los tokens existentes
        $this->logoutAll($user);
    }

    private function ensureDefaultPermissions(Role $role): void
    {
        $modules = [
            'Relacionamiento', 'Informes', 'Miembros',
            'Oración', 'Usuarios', 'Reportes',
            'Configuración', 'Biblioteca',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permName = "{$module}.{$action}";
                $permission = Permission::firstOrCreate([
                    'name' => $permName,
                    'guard_name' => 'web',
                ]);
                $role->givePermissionTo($permission);
            }
        }
    }
}

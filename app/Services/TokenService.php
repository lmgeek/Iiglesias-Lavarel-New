<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\PersonalAccessToken;

class TokenService
{
    public function createAccessToken(User $user): string
    {
        return $user->createToken('access-token', ['*'], now()->addHours(24))->plainTextToken;
    }

    public function createRefreshToken(User $user): string
    {
        return $user->createToken('refresh-token', ['refresh'], now()->addDays(30))->plainTextToken;
    }

    public function revokeToken(User $user, string $token): bool
    {
        $tokenModel = PersonalAccessToken::findToken($token);

        if ($tokenModel && $tokenModel->tokenable_id === $user->id) {
            $tokenModel->delete();

            return true;
        }

        return false;
    }

    public function revokeAllAccessTokens(User $user): void
    {
        $user->tokens()->where('name', 'access-token')->delete();
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function getActiveTokens(User $user): Collection
    {
        return $user->tokens()
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTokenInfo(string $token): ?array
    {
        $tokenModel = PersonalAccessToken::findToken($token);

        if (! $tokenModel) {
            return null;
        }

        return [
            'id' => $tokenModel->id,
            'name' => $tokenModel->name,
            'abilities' => $tokenModel->abilities,
            'last_used_at' => $tokenModel->last_used_at,
            'expires_at' => $tokenModel->expires_at,
            'created_at' => $tokenModel->created_at,
        ];
    }

    public function pruneExpiredTokens(int $hours = 24): int
    {
        return PersonalAccessToken::where('expires_at', '<', Carbon::now()->subHours($hours))->delete();
    }

    public function revokeExpiredTokens(User $user): int
    {
        return $user->tokens()->where('expires_at', '<', now())->delete();
    }
}

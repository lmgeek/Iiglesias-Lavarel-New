<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    public function webRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function webCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

        $profile = [
            'id' => $googleUser->getId(),
            'email' => strtolower((string) $googleUser->getEmail()),
            'name' => $googleUser->getName(),
        ];

        $user = User::where('google_id', $profile['id'])->whereNull('deleted_at')->first();

        if (! $user) {
            $user = User::where('email', $profile['email'])->whereNull('deleted_at')->first();

            if ($user) {
                $user->update(['google_id' => $profile['id']]);
            } else {
                $user = User::create([
                    'uuid' => (string) Str::uuid(),
                    'fullname' => $profile['name'] ?? $profile['email'],
                    'email' => $profile['email'],
                    'google_id' => $profile['id'],
                    'password' => Hash::make(Str::random(32)),
                    'born_date' => now()->format('Y-m-d'),
                    'sex' => '',
                    'is_active' => true,
                ]);

                $defaultRole = Role::where('name', 'Usuario')->first();
                if ($defaultRole) {
                    $user->assignRole($defaultRole);
                }
            }
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors(['email' => 'Usuario inactivo']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $destination = $user->profileComplete() ? route('dashboard') : route('perfil.completar');

        return redirect()->intended($destination);
    }
}

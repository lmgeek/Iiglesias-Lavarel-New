<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', [
            'needsSetup' => User::whereNull('deleted_at')->count() === 0,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->whereNull('deleted_at')->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Credenciales inválidas'])->withInput();
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Usuario inactivo'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function setup(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = $this->authService->setupPassword($data);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        Auth::login($user);
        session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function showForgotForm()
    {
        return view('auth.forgot');
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $this->authService->sendPasswordResetLink($data['email']);

        return back()->with('status', 'Si ese email existe, te enviamos un enlace para restablecer tu contraseña.');
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset', [
            'token' => $request->query('token'),
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $this->authService->resetPassword($data['email'], $data['token'], $data['password']);
        } catch (\Exception $e) {
            return back()->withErrors(['token' => $e->getMessage()])->withInput();
        }

        return redirect()->route('login')->with('status', 'Tu contraseña fue restablecida. Inicia sesión con tu nueva contraseña.');
    }

    public function completeForm()
    {
        $user = Auth::user();

        if ($user->profileComplete()) {
            return redirect()->route('dashboard');
        }

        return view('auth.completar', ['user' => $user]);
    }

    public function complete(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'doc_number' => [
                'required', 'string', 'max:20', 'regex:/^\d+$/',
                Rule::unique('users', 'doc_number')->whereNull('deleted_at')->ignore($user->id),
            ],
            'born_date' => 'required|date|before:today',
            'sex' => 'required|string|in:M,F',
            'phone' => 'required|string|max:255',
        ]);

        $user->update($data);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Datos actualizados correctamente. ¡Bienvenido!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

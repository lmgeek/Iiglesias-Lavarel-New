<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('roles', 'ministry', 'sede');

        return view('perfil.index', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fullname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:255',
            'church' => 'nullable|string|max:255',
            'mentor' => 'nullable|string|max:255',
            'ministerial_range' => 'nullable|string|max:255',
            'celula' => 'nullable|integer',
            'doc_number' => 'sometimes|string|max:255|unique:users,doc_number,'.$user->id,
            'lider_celula' => 'sometimes|in:Si,No',
            'current_password' => 'required_with:password|string',
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Contraseña actual incorrecta']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->update($request->except(['current_password', 'password', 'password_confirmation']));

        return back()->with('success', 'Perfil actualizado correctamente');
    }

    public function tema(Request $request)
    {
        $data = $request->validate(['theme' => 'required|in:light,dark']);

        Auth::user()->update(['theme' => $data['theme']]);

        return response()->json(['ok' => true]);
    }
}

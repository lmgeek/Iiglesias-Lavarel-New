<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('roles', 'permissions');

        return new UserResource($user);
    }

    public function update(Request $request)
    {
        $user = $request->user();

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

        if ($request->has('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'Contraseña actual incorrecta'], 400);
            }
            $user->password = $request->password;
        }

        $user->update($request->except(['current_password', 'password', 'password_confirmation']));

        return new UserResource($user->load('roles', 'permissions'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();

        return view('admin.usuario', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        // 🚨 EVITAR CAMBIARTE A VOS MISMO
        if ($user->id == auth()->id()) {
            return back()->with('error', 'No podés cambiar tu propio rol');
        }

        $user->syncRoles([$request->role]);

        return back()->with('success', 'Rol actualizado');

    }

    public function edit(User $user)
    {
        $roles = \Spatie\Permission\Models\Role::all();

        return view('admin.usuarios_edit', compact('user', 'roles'));
    }

   public function update(Request $request, User $user)
    {
        // 🚨 evitar que te edites a vos mismo (opcional)
        if ($user->id == auth()->id()) {
            return back()->with('error', 'No podés editar tu propio usuario');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required',
            'password' => 'nullable|min:6|confirmed'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // 🔐 SOLO SI SE INGRESA CONTRASEÑA
        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente');
    } 
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Lietotaji', [
            'users' => User::with('role')->orderBy('vards')->get(),
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vards'                 => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'role_id'               => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'vards'                 => $data['vards'],
            'email'                 => $data['email'],
            'password'              => Hash::make($data['password']),
            'role_id'               => $data['role_id'],
            'registracijas_datums'  => now()->toDateString(),
            'pedeja_pieteiksanas'   => now(),
        ]);

        $user->markEmailAsVerified();

        return redirect()->route('admin.users.index')
            ->with('status', 'lietotajs-pievienots');
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update(['role_id' => $data['role_id']]);

        return response()->json(['message' => 'Loma atjaunināta.']);
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            abort(403, 'Nevar dzēst savu kontu.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'lietotajs-dzests');
    }
}

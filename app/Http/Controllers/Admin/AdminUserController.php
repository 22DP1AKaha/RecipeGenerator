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
        // Ielasam visus lietotājus ar lomām, kārtotus pēc vārda
        return Inertia::render('Admin/Lietotaji', [
            'users' => User::with('role')->orderBy('vards')->get(),
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        // Validējam jauna lietotāja datus
        $data = $request->validate([
            'vards'                 => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'role_id'               => 'required|exists:roles,id',
        ]);

        // Izveidojam jaunu lietotāju ar šifrētu paroli
        $user = User::create([
            'vards'                => $data['vards'],
            'email'                => $data['email'],
            'password'             => Hash::make($data['password']),
            'role_id'              => $data['role_id'],
            'registracijas_datums' => now()->toDateString(),
            'pedeja_pieteiksanas'  => now(),
        ]);

        // Automātiski apstiprinām e-pasta adresi
        $user->markEmailAsVerified();

        return redirect()->route('admin.users.index')
            ->with('status', 'lietotajs-pievienots');
    }

    public function updateRole(Request $request, User $user)
    {
        // Validējam un atjauninām lietotāja lomu
        $data = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update(['role_id' => $data['role_id']]);

        return response()->json(['message' => 'Loma atjaunināta.']);
    }

    public function destroy(Request $request, User $user)
    {
        // Neļaujam administratoram dzēst savu kontu
        if ($user->id === $request->user()->id) {
            abort(403, 'Nevar dzēst savu kontu.');
        }

        // Dzēšam lietotāju no datubāzes
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'lietotajs-dzests');
    }
}

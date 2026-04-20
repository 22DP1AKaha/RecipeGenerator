<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrators')->firstOrFail();

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'vards'                => env('ADMIN_NAME', 'Admin'),
                'password'             => Hash::make(env('ADMIN_PASSWORD')),
                'role_id'              => $adminRole->id,
                'email_verified_at'    => now(),
                'registracijas_datums' => now(),
            ]
        );
    }
}

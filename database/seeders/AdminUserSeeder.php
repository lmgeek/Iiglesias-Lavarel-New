<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() > 0) {
            $this->command->info('Ya existen usuarios, se omite creación de admin');

            return;
        }

        $admin = User::create([
            'uuid' => (string) Str::uuid(),
            'fullname' => 'Administrador',
            'email' => 'admin@catedralcristiana.com',
            'doc_number' => 'ADMIN-0001',
            'born_date' => now()->format('Y-m-d'),
            'sex' => 'M',
            'password' => Hash::make('Admin123!'),
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('Usuario admin creado: admin@catedralcristiana.com / Admin123!');
    }
}

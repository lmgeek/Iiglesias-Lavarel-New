<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin' => '*', // Todos los permisos
            'Supervisor' => [
                'Relacionamiento.*',
                'Informes.*',
                'Miembros.*',
                'Oración.*',
                'Reportes.*',
                'Configuración.view',
                'Biblioteca.*',
            ],
            'Lider' => [
                'Relacionamiento.*',
                'Informes.*',
                'Miembros.view',
                'Oración.view',
                'Reportes.view',
                'Biblioteca.view',
            ],
            'Pastor' => [
                'Relacionamiento.*',
                'Informes.*',
                'Miembros.*',
                'Oración.*',
                'Reportes.view',
                'Biblioteca.*',
            ],
            'Facilitador' => [
                'Relacionamiento.view',
                'Relacionamiento.create',
                'Informes.create',
                'Informes.view',
                'Miembros.view',
                'Oración.view',
                'Biblioteca.view',
            ],
            'Miembro' => [
                'Relacionamiento.view',
                'Informes.view',
                'Oración.view',
                'Biblioteca.view',
            ],
            'Usuario' => [
                'Relacionamiento.view',
                'Informes.view',
                'Oración.view',
                'Biblioteca.view',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            if ($permissions === '*') {
                $role->givePermissionTo(Permission::all());
            } else {
                $perms = Permission::whereIn('name', $permissions)->get();
                $role->syncPermissions($perms);
            }
        }

        $this->command->info('Roles creados: '.Role::count());
    }
}

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
                'Configuración.*',
                'Biblioteca.*',
                'Calendario.*',
            ],
            'Lider' => [
                'Relacionamiento.*',
                'Informes.*',
                'Miembros.view',
                'Oración.view',
                'Reportes.view',
                'Biblioteca.view',
                'Calendario.view',
                'Calendario.create',
                'Calendario.edit',
                'Calendario.delete',
            ],
            'Pastor' => [
                'Relacionamiento.*',
                'Informes.*',
                'Miembros.*',
                'Oración.*',
                'Reportes.view',
                'Biblioteca.*',
                'Calendario.*',
            ],
            'Facilitador' => [
                'Relacionamiento.view',
                'Relacionamiento.create',
                'Informes.create',
                'Informes.view',
                'Miembros.view',
                'Oración.view',
                'Biblioteca.view',
                'Calendario.view',
            ],
            'Miembro' => [
                'Relacionamiento.view',
                'Informes.view',
                'Oración.view',
                'Biblioteca.view',
                'Calendario.view',
            ],
            'Usuario' => [
                'Relacionamiento.view',
                'Informes.view',
                'Oración.view',
                'Biblioteca.view',
                'Calendario.view',
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
                $expanded = collect($permissions)
                    ->flatMap(function (string $pattern) {
                        if (str_ends_with($pattern, '.*')) {
                            $prefix = substr($pattern, 0, -2);

                            return Permission::where('name', 'like', $prefix.'.%')->pluck('name');
                        }

                        return [$pattern];
                    })
                    ->values();

                $role->syncPermissions($expanded);
            }
        }

        $this->command->info('Roles creados: '.Role::count());
    }
}

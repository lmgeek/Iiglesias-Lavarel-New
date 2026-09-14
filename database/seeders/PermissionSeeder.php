<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'Relacionamiento',
            'Informes',
            'Miembros',
            'Oración',
            'Usuarios',
            'Reportes',
            'Configuración',
            'Biblioteca',
            'Calendario',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $this->command->info('Permisos creados: '.Permission::count());
    }
}

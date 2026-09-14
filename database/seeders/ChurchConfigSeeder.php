<?php

namespace Database\Seeders;

use App\Models\ChurchConfig;
use Illuminate\Database\Seeder;

class ChurchConfigSeeder extends Seeder
{
    public function run(): void
    {
        ChurchConfig::firstOrCreate([], [
            'church_name' => 'Catedral Cristiana',
            'logo' => null,
            'favicon' => null,
        ]);

        $this->command->info('Configuración de iglesia creada');
    }
}

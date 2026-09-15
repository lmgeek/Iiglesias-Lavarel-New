<?php

namespace App\Console\Commands;

use App\Services\MigracionService;
use Illuminate\Console\Command;

class MigracionEjecutar extends Command
{
    protected $signature = 'migracion:ejecutar {source_db} {church?} {--force : Forzar ejecución sin confirmación}';

    protected $description = 'Ejecuta migración desde base de datos legacy';

    public function handle(MigracionService $migracionService)
    {
        $sourceDb = $this->argument('source_db');
        $church = $this->argument('church');
        $force = $this->option('force');

        if (! $force) {
            if (! $this->confirm("¿Ejecutar migración desde '{$sourceDb}'? Esto insertará datos en la base actual.")) {
                $this->info('Migración cancelada.');

                return 0;
            }
        }

        $this->info("Iniciando migración desde {$sourceDb}...");

        try {
            $result = $migracionService->migrate($sourceDb, $church);

            $this->info('Migración completada exitosamente.');
            $this->table(['Tabla', 'Registros'], collect($result['counts'])->map(fn ($v, $k) => [$k, $v])->toArray());

            if (! empty($result['warnings'])) {
                $this->warn('Advertencias:');
                foreach ($result['warnings'] as $warning) {
                    $this->line("  - {$warning}");
                }
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Error en migración: '.$e->getMessage());

            return 1;
        }
    }
}

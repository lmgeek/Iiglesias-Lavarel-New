<?php

namespace App\Console\Commands;

use App\Models\ReportCelula;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InformesExportar extends Command
{
    protected $signature = 'informes:exportar {fecha_inicio} {fecha_fin} {--output= : Ruta de salida del archivo}';

    protected $description = 'Exporta informes a Excel';

    public function handle()
    {
        $fechaInicio = $this->argument('fecha_inicio');
        $fechaFin = $this->argument('fecha_fin');
        $output = $this->option('output') ?? storage_path('app/exports/informes_'.$fechaInicio.'_'.$fechaFin.'.xlsx');

        $this->info("Exportando informes del {$fechaInicio} al {$fechaFin}...");

        $reports = ReportCelula::with('mentor')
            ->whereBetween('f_meet', [$fechaInicio, $fechaFin])
            ->orderBy('f_meet', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'ID', 'Fecha', 'Mentor', 'Célula', 'Suspendido', 'Motivo Suspensión',
            'Líder', 'Título', 'Quién Reunió', 'Formato', 'Cantidad Personas',
            'Nuevas Personas', 'Mentoreo', 'Observaciones', 'Creado',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:'.chr(64 + count($headers)).'1')->getFont()->setBold(true);

        // Data
        $row = 2;
        foreach ($reports as $report) {
            $sheet->fromArray([
                $report->id,
                $report->f_meet?->format('Y-m-d H:i'),
                $report->mentor?->fullname,
                $report->celula,
                $report->suspended,
                $report->why_suspended,
                $report->lider,
                $report->message_title,
                $report->who_meet,
                $report->format,
                $report->people_qty,
                $report->new_people_qty,
                $report->mentoring,
                $report->observations,
                $report->created_at?->format('Y-m-d H:i'),
            ], null, 'A'.$row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', chr(64 + count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save
        $writer = new Xlsx($spreadsheet);
        $writer->save($output);

        $this->info("Exportación completada: {$output} ({$reports->count()} registros)");

        return 0;
    }
}

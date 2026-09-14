<?php

namespace Database\Seeders;

use App\Models\BibliotecaItem;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BibliotecaSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['Escuela de Vida', 'Escuela de Vida A', 'https://ipsargentina.net/wp-content/uploads/2025/01/Escuela-de-Vida-A.pdf'],
            ['Escuela de Vida', 'Escuela de Vida B', 'https://ipsargentina.net/wp-content/uploads/2025/01/Escuela-de-Vida-B.pdf'],
            ['Escuela de Vida', 'Escuela de Vida C', 'https://ipsargentina.net/wp-content/uploads/2025/01/Escuela-de-Vida-C-2022.pdf'],
            ['Pastoral / Liderazgo', 'Plantación de Iglesias', 'https://ipsargentina.net/wp-content/uploads/2025/03/1-plantacion_de_iglesias.pdf'],
            ['Pastoral / Liderazgo', 'Hola', 'https://ipsargentina.net/wp-content/uploads/2025/03/2-hola.pdf'],
            ['Pastoral / Liderazgo', 'Hay Gozo en Dar', 'https://ipsargentina.net/wp-content/uploads/2025/03/3-hay_gozo_en_dar.pdf'],
            ['Pastoral / Liderazgo', 'El Puré de Papas', 'https://ipsargentina.net/wp-content/uploads/2025/03/4-el_pure_de_papas.pdf'],
            ['Discipulado Uno a Uno', 'Discípulo Discipulador Nivel 1', 'https://ipsargentina.net/wp-content/uploads/2024/12/Discipulo-Discipulador-nivel-1.pdf'],
            ['Discipulado Uno a Uno', 'Factor Bernabé', 'https://ipsargentina.net/wp-content/uploads/2024/12/Factor-Bernabe.pdf'],
            ['Discipulado Uno a Uno', 'Discípulo Discipulador Nivel 2', 'https://ipsargentina.net/wp-content/uploads/2024/12/Discipulo-Discipulador-nivel-2.pdf'],
            ['Discipulado Uno a Uno', 'Discípulo Discipulador Nivel 3', 'https://ipsargentina.net/wp-content/uploads/2024/12/Discipulo-discipulador-nivel-3.pdf'],
            ['Discipulado Uno a Uno', 'Hombres', 'https://ipsargentina.net/wp-content/uploads/2024/12/hombres.pdf'],
            ['Discipulado Uno a Uno', 'Mujeres', 'https://ipsargentina.net/wp-content/uploads/2024/12/mujeres.pdf'],
            ['Discipulado Uno a Uno', 'Discipulado Uno a Uno Niños', 'https://ipsargentina.net/wp-content/uploads/2025/03/uno_a_uno_ninos.pdf'],
            ['Discipulado Uno a Uno Grupal', 'Id y Haced Discípulos', 'https://ipsargentina.net/wp-content/uploads/2024/12/Id-y-haced-discipulos.pdf'],
            ['Discipulado Uno a Uno Grupal', 'Principios de Poder I', 'https://ipsargentina.net/wp-content/uploads/2024/12/Principios-de-Poder-I.pdf'],
            ['Discipulado Uno a Uno Grupal', 'Principios de Poder II', 'https://ipsargentina.net/wp-content/uploads/2024/12/Principios-de-Poder-II.pdf'],
            ['Discipulado Uno a Uno Grupal', 'El Carácter de Cristo en Nosotros', 'https://catedralcristiana.com.ar/site/biblioteca/discipulado_uno_a_uno_grupal/El_caracter_de_Cristo_en_nosotros.pdf'],
            ['Discipulado Uno a Uno Grupal', 'Intercesión', 'https://ipsargentina.net/wp-content/uploads/2024/12/inter-listo.pdf'],
            ['Discipulado Uno a Uno Grupal', 'Doctrinas Básicas', 'https://ipsargentina.net/wp-content/uploads/2024/12/Doctrinas-Basicas.pdf'],
            ['Niños', 'Hablar con Dios', 'https://ipsargentina.net/wp-content/uploads/2025/03/Hablar-con-Dios_1.pdf'],
            ['Matrimonios', 'Cómo Salvar tu Matrimonio', 'https://ipsargentina.net/libros/matrinovios/como_salvar_tu_matrimonio.pdf'],
            ['Matrimonios', 'Tú y Yo por Siempre', 'https://ipsargentina.net/libros/matrinovios/tu-y-yo-por-siempre.pdf'],
            ['Devocionales', 'Devocional Tomo 1', 'https://ipsargentina.net/libros/devocionales/Tomo%201%20(1).pdf'],
            ['Devocionales', 'Devocional Tomo II', 'https://ipsargentina.net/libros/devocionales/TOMO%20II%20%20revisado%20(1).pdf'],
            ['Devocionales', 'Devocional Tomo III', 'https://ipsargentina.net/libros/devocionales/Tomo%20III.pdf'],
            ['Devocionales', 'Devocional Tomo IV', 'https://ipsargentina.net/libros/devocionales/Tomo%20IV.docx.pdf'],
            ['Devocionales', 'Devocional Enero 2021', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Enero_2021.pdf'],
            ['Devocionales', 'Devocional Mayo', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Mayo.pdf'],
            ['Devocionales', 'Devocional Junio', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Junio.pdf'],
            ['Devocionales', 'Devocional Julio', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Julio.pdf'],
            ['Devocionales', 'Devocional Agosto', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Agosto.pdf'],
            ['Devocionales', 'Devocional Septiembre', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Septiembre.pdf'],
            ['Devocionales', 'Devocional Octubre', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Octubre.pdf'],
            ['Devocionales', 'Devocional Noviembre', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Noviembre.pdf'],
            ['Devocionales', 'Devocional Diciembre', 'https://ipsargentina.net/wp-content/uploads/2025/03/Devocional_Diciembre.pdf'],
            ['Doctrinas Bíblicas', 'Clase 1 - Conversión A', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo1.DB_.Conversion-A.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 2 - Conversión B', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo2.DB_.Conversion-B.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 3 - Conversión C', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo3.DB_.Conversion-C.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 4 - Conversión D', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo4.DB_.Conversion-D.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 5 - Conversión E', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo5.DB_.Conversion-E.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 6 - Conversión F', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo6.DB_.Conversion-F.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 7 - Santificación A', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo7.DB_.Santificacion-A.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 8 - Santificación B', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo8.DB_.Santificacion-B.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 9 - Santificación C', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo9.DB_.Santificacion-C.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 10 - Espíritu Santo A', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo10.DB_.Espiritu-Santo-A.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 11 - Espíritu Santo B', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo11.DB_.Espiritu-Santo-B.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 12 - Sanidad Divina A', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo12.DB_.Sanidad-Divina-A.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 13 - Sanidad Divina B', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo13.DB_.Sanidad-Divina-B.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 14 - Segunda Venida A', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo14.DB_.Segunda-Venida-A.docx.pdf'],
            ['Doctrinas Bíblicas', 'Clase 15 - Segunda Venida B', 'https://ipsargentina.net/wp-content/uploads/2024/09/Anexo15.DB_.Segunda-Venida-B.docx.pdf'],
        ];

        foreach ($books as [$category, $title, $url]) {
            if (BibliotecaItem::where('title', $title)->exists()) {
                continue;
            }

            try {
                $response = Http::timeout(60)->get($url);
            } catch (ConnectionException) {
                $this->command->warn("No se pudo conectar: $title");

                continue;
            }

            if (! $response->successful()) {
                $this->command->warn("No se pudo descargar: $title");

                continue;
            }

            $fileName = rawurldecode(basename(parse_url($url, PHP_URL_PATH)));
            $storedName = 'biblioteca/'.Str::uuid().'.pdf';

            Storage::disk('public')->put($storedName, $response->body());

            BibliotecaItem::create([
                'title' => $title,
                'description' => null,
                'file_path' => $storedName,
                'file_name' => $fileName,
                'file_type' => 'PDF',
                'category' => $category,
                'created_by' => null,
            ]);

            $this->command->info("Insertado: $title");
        }
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChurchConfig;
use App\Models\MeetingsTheme;
use App\Models\Ministry;
use App\Models\Sede;
use App\Services\ImgbbService;
use App\Services\MigracionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ConfigController extends Controller
{
    protected MigracionService $migracionService;

    public function __construct(MigracionService $migracionService)
    {
        $this->migracionService = $migracionService;
    }

    public function roles()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('config.roles', ['roles' => $roles]);
    }

    public function temas()
    {
        $temas = MeetingsTheme::whereNull('deleted_at')->orderBy('created_at', 'desc')->get();

        return view('config.temas', ['temas' => $temas]);
    }

    public function temasStore(Request $request, ImgbbService $imgbb)
    {
        $data = $request->validate([
            'classname' => 'required|string|max:255',
            'image' => 'nullable|image|max:8192',
            'youtube' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $upload = $imgbb->upload($request->file('image'));

            if ($upload === null) {
                return back()->with('error', 'No se pudo subir la imagen a imgBB. Verifica la clave IMGBB_API_KEY.');
            }

            $data['image'] = $upload['url'];
            $data['image_delete_url'] = $upload['delete_url'];
        } else {
            $data['image'] = null;
            $data['image_delete_url'] = null;
        }

        $data['youtube'] = ! empty($data['youtube']) ? trim($data['youtube']) : null;

        MeetingsTheme::create($data);

        return back()->with('success', 'Tema creado correctamente');
    }

    public function ajustes()
    {
        return view('config.ajustes', ['config' => ChurchConfig::getConfig()]);
    }

    public function ajustesStore(Request $request)
    {
        $data = $request->validate([
            'church_name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:4096',
            'favicon' => 'nullable|image|max:4096',
            'login_bg' => 'nullable|image|max:8192',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
        ]);

        $config = ChurchConfig::getConfig();

        $fields = ['phone', 'email', 'instagram', 'facebook', 'tiktok', 'youtube'];
        foreach ($fields as $field) {
            $data[$field] = ! empty($data[$field]) ? trim($data[$field]) : null;
        }

        $data['logo'] = $config->logo;
        $data['favicon'] = $config->favicon;
        $data['login_bg'] = $config->login_bg;

        foreach (['logo', 'favicon', 'login_bg'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $file = $request->file($imageField);
                $data[$imageField] = 'data:'.$file->getMimeType().';base64,'.base64_encode(file_get_contents($file->getRealPath()));
            }
        }

        $config->update($data);

        return back()->with('success', 'Ajustes de la iglesia guardados correctamente');
    }

    public function ministries()
    {
        $ministries = Ministry::orderBy('name')->get();

        return view('config.ministries', ['ministries' => $ministries]);
    }

    public function ministriesStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Ministry::create($data);

        return back()->with('success', 'Ministry creado correctamente');
    }

    public function ministriesDestroy(Ministry $ministry)
    {
        $ministry->delete();

        return back()->with('success', 'Ministry eliminado');
    }

    public function sedes()
    {
        $sedes = Sede::orderBy('name')->get();

        return view('config.sedes', ['sedes' => $sedes]);
    }

    public function sedesStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        Sede::create($data);

        return back()->with('success', 'Sede creada correctamente');
    }

    public function sedesDestroy(Sede $sede)
    {
        $sede->delete();

        return back()->with('success', 'Sede eliminada');
    }

    public function migracion()
    {
        $tables = ['users', 'relationships', 'reports_celula', 'intercesiones', 'meetings_themes'];

        $counts = collect($tables)->mapWithKeys(function ($table) {
            try {
                $count = DB::table($table)->whereNull('deleted_at')->count();
            } catch (\Exception $e) {
                $count = 0;
            }

            return [$table => $count];
        });

        $databases = $this->migracionService->getAvailableDatabases();
        $current = DB::connection()->getDatabaseName();

        return view('config.migracion', [
            'counts' => $counts,
            'databases' => $databases,
            'current' => $current,
        ]);
    }

    public function migracionChurches(Request $request)
    {
        $request->validate([
            'source_db' => 'required|string',
        ]);

        try {
            $churches = $this->migracionService->getChurches($request->source_db);

            return response()->json(['churches' => $churches]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'churches' => []], 400);
        }
    }

    public function migracionStore(Request $request)
    {
        $data = $request->validate([
            'source_db' => 'required|string',
            'church' => 'nullable|string',
        ]);

        try {
            $result = $this->migracionService->migrate($data['source_db'], $data['church'] ?? null);

            $summary = collect($result['counts'])->map(fn ($n, $k) => "$k: $n")->implode(', ');

            return back()->with('success', "Migración completada. $summary");
        } catch (\Exception $e) {
            return back()->with('error', 'Error en la migración: '.$e->getMessage());
        }
    }
}

<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigracionService
{
    protected $sourceConnection;

    protected $targetConnection;

    // Tablas que se copian completas preservando ids (tablas de referencia)
    protected $referenceTables = ['meetings_themes'];

    // Tablas gestionadas manualmente (remapeo de ids / dependencias)
    protected $managedTables = ['users', 'relationships', 'reports_celula', 'model_has_roles'];

    // Valores de ministerial_range que no corresponden a ministerios reales
    protected array $invalidMinistries = ['Ninguno', 'Seleccione el departamento a cargo', 'Hola'];

    // Id del rol 'Lider' en la BD fuente (roles.id = 6)
    protected int $sourceLiderRoleId = 6;

    public function __construct(?string $sourceDb = null)
    {
        $this->sourceConnection = $sourceDb;
        $this->targetConnection = 'mysql';
    }

    public function getAvailableDatabases(): array
    {
        $databases = DB::connection($this->targetConnection)
            ->select('SHOW DATABASES');

        $systemDbs = ['information_schema', 'mysql', 'performance_schema', 'sys'];
        $currentDb = config('database.connections.mysql.database');

        return collect($databases)
            ->pluck('Database')
            ->filter(fn ($db) => ! in_array($db, $systemDbs))
            ->map(fn ($db) => [
                'name' => $db,
                'is_current' => $db === $currentDb,
            ])
            ->values()
            ->toArray();
    }

    public function getChurches(string $sourceDb): array
    {
        $this->ensureSourceConnection($sourceDb);

        if (! $this->tableExists($sourceDb, 'users')) {
            return [];
        }

        return DB::connection($sourceDb)
            ->table('users')
            ->whereNotNull('church')
            ->where('church', '!=', '')
            ->distinct()
            ->orderBy('church')
            ->pluck('church')
            ->values()
            ->toArray();
    }

    public function migrate(string $sourceDb, ?string $church = null): array
    {
        if ($sourceDb === config('database.connections.mysql.database')) {
            throw new Exception('La base de datos de origen no puede ser la actual');
        }

        $databases = $this->getAvailableDatabases();
        if (! collect($databases)->pluck('name')->contains($sourceDb)) {
            throw new Exception('La base de datos de origen no existe');
        }

        $this->ensureSourceConnection($sourceDb);

        $counts = [];
        $warnings = [];

        // --- Tablas de referencia (copiado completo, preservando ids) ---
        foreach ($this->referenceTables as $sourceTable) {
            if (! $this->tableExists($sourceDb, $sourceTable)) {
                $warnings[] = "La tabla `$sourceTable` no existe en `$sourceDb`, se omitió";

                continue;
            }

            if (! $this->tableExists($this->targetConnection, $sourceTable)) {
                $warnings[] = "La tabla `$sourceTable` no existe en la base de destino, se omitió";

                continue;
            }

            $counts[$sourceTable] = $this->migrateTable($sourceDb, $sourceTable);
        }

        // --- RBAC: el destino ya tiene roles y permisos configurados ---
        $targetRolesByKey = DB::connection($this->targetConnection)
            ->table('roles')
            ->get(['id', 'name'])
            ->keyBy('id');

        $sourceRoles = $this->getRoleMap($sourceDb);
        $targetRolesByName = collect($targetRolesByKey)
            ->pluck('id', 'name')
            ->toArray();

        // --- Calcular ids de usuarios a importar ---
        $importIds = $this->computeImportIds($sourceDb, $church);

        // --- Ministerios desde ministerial_range ---
        $ministryMap = $this->createMinistries($sourceDb, $importIds);
        $counts['ministries'] = count($ministryMap);

        // --- Sedes desde iglesias presentes en el set ---
        $sedeMap = $this->createSedes($sourceDb, $importIds);
        $counts['sedes'] = count($sedeMap);

        // --- Usuarios (con remapeo de ids, participantes involucrados incluidos) ---
        $usersResult = $this->migrateUsers($sourceDb, $church, $importIds, $ministryMap, $sedeMap);
        $counts['users'] = $usersResult['inserted'];
        $idMap = $usersResult['id_map'];
        $warnings = array_merge($warnings, $usersResult['warnings']);

        // --- model_has_roles (roles por NOMBRE hacia el destino) ---
        $counts['model_has_roles'] = $this->migrateUserRoles($sourceDb, $idMap, $sourceRoles, $targetRolesByName);

        // --- relationships (solo si mentor y discípulo fueron importados) ---
        $counts['relationships'] = $this->migrateRelationships($sourceDb, $idMap);

        // --- reports_celula (mentor resuelto por nombre, solo iglesia destino + aliases confiables) ---
        $reportsResult = $this->migrateReportsCelula($sourceDb, $idMap, $church);
        $counts['reports_celula'] = $reportsResult['inserted'];
        $warnings = array_merge($warnings, $reportsResult['warnings']);

        // --- Tablas vacías en origen ---
        foreach (['intercesion'] as $t) {
            if ($this->tableExists($sourceDb, $t) && $this->tableExists($this->targetConnection, $t)) {
                $total = DB::connection($sourceDb)->table($t)->count();
                if ($total === 0) {
                    $warnings[] = "La tabla `$t` está vacía en `$sourceDb`, se omitió";
                }
            }
        }

        $warnings[] = 'Roles, permisos y role_has_permissions no se importan: el destino ya tiene su propio RBAC configurado. Los usuarios importados reciben roles por nombre.';

        return [
            'success' => true,
            'source_db' => $sourceDb,
            'church' => $church,
            'counts' => $counts,
            'warnings' => $warnings,
        ];
    }

    // ------------------------------------------------------------------
    // Cálculo del conjunto de usuarios a importar
    // ------------------------------------------------------------------

    protected function computeImportIds(string $sourceDb, ?string $church): array
    {
        $query = DB::connection($sourceDb)->table('users');

        if ($church === null || $church === '') {
            return $query->pluck('id')->map(fn ($id) => (int) $id)->values()->toArray();
        }

        // Usuarios de la iglesia principal
        $primaryIds = $query->where('church', $church)->pluck('id')->map(fn ($id) => (int) $id)->values()->toArray();

        if (empty($primaryIds)) {
            return [];
        }

        // Participantes involucrados: usuarios de otras iglesias que aparecen en
        // relaciones junto a un usuario de la iglesia principal
        $primaryIn = implode(',', $primaryIds);
        $involvedRows = DB::connection($sourceDb)
            ->select("
                SELECT DISTINCT u.id
                FROM users u
                WHERE u.id IN (
                    SELECT r.mentor_id FROM relationships r WHERE r.mentor_id IN ($primaryIn) OR r.disciple_id IN ($primaryIn)
                    UNION
                    SELECT r.disciple_id FROM relationships r WHERE r.mentor_id IN ($primaryIn) OR r.disciple_id IN ($primaryIn)
                )
                AND (u.church IS NULL OR u.church <> ?)
            ", [$church]);

        $involvedIds = array_map(fn ($r) => (int) $r->id, $involvedRows);

        return array_values(array_unique(array_merge($primaryIds, $involvedIds)));
    }

    // ------------------------------------------------------------------
    // Ministerios y sedes
    // ------------------------------------------------------------------

    protected function createMinistries(string $sourceDb, array $importIds): array
    {
        if (empty($importIds)) {
            return [];
        }

        $importIn = implode(',', $importIds);
        $ranges = DB::connection($sourceDb)
            ->table('users')
            ->whereRaw("`id` IN ($importIn)")
            ->whereNotNull('ministerial_range')
            ->where('ministerial_range', '!=', '')
            ->distinct()
            ->pluck('ministerial_range')
            ->filter(fn ($v) => ! in_array($v, $this->invalidMinistries))
            ->values()
            ->toArray();

        $map = [];

        foreach ($ranges as $name) {
            $existing = DB::connection($this->targetConnection)
                ->table('ministries')
                ->where('name', $name)
                ->value('id');

            if ($existing) {
                $map[$name] = (int) $existing;

                continue;
            }

            $id = DB::connection($this->targetConnection)
                ->table('ministries')
                ->insertGetId([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            $map[$name] = (int) $id;
        }

        return $map;
    }

    protected function createSedes(string $sourceDb, array $importIds): array
    {
        if (empty($importIds)) {
            return [];
        }

        $importIn = implode(',', $importIds);
        $churches = DB::connection($sourceDb)
            ->table('users')
            ->whereRaw("`id` IN ($importIn)")
            ->whereNotNull('church')
            ->where('church', '!=', '')
            ->distinct()
            ->pluck('church')
            ->toArray();

        $map = [];

        foreach ($churches as $name) {
            $existing = DB::connection($this->targetConnection)
                ->table('sedes')
                ->where('name', $name)
                ->value('id');

            if ($existing) {
                $map[$name] = (int) $existing;

                continue;
            }

            $id = DB::connection($this->targetConnection)
                ->table('sedes')
                ->insertGetId([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            $map[$name] = (int) $id;
        }

        return $map;
    }

    // ------------------------------------------------------------------
    // Referencias
    // ------------------------------------------------------------------

    protected function migrateTable(string $sourceDb, string $sourceTable): int
    {
        if (! $this->tableExists($this->targetConnection, $sourceTable)) {
            return 0;
        }

        $sourceCols = $this->getColumns($sourceDb, $sourceTable);
        $targetCols = $this->getColumns($this->targetConnection, $sourceTable);

        $copyCols = array_intersect($sourceCols, $targetCols);
        $copyCols = array_diff($copyCols, ['created_at', 'updated_at', 'deleted_at']);

        if (empty($copyCols)) {
            return 0;
        }

        $total = DB::connection($sourceDb)->table($sourceTable)->count();

        if ($total === 0) {
            return 0;
        }

        $colList = implode(', ', array_map(fn ($c) => "`$c`", $copyCols));
        $placeholders = implode(', ', array_fill(0, count($copyCols), '?'));
        $inserted = 0;

        $batchSize = 500;
        for ($offset = 0; $offset < $total; $offset += $batchSize) {
            $rows = DB::connection($sourceDb)
                ->table($sourceTable)
                ->select($copyCols)
                ->limit($batchSize)
                ->offset($offset)
                ->get()
                ->toArray();

            if (empty($rows)) {
                break;
            }

            $values = [];
            $tuples = [];

            foreach ($rows as $row) {
                $tuple = [];
                foreach ($copyCols as $col) {
                    $tuple[] = $row->$col ?? null;
                }
                $tuples[] = "($placeholders)";
                $values = array_merge($values, $tuple);
            }

            try {
                DB::connection($this->targetConnection)
                    ->insert("INSERT IGNORE INTO `$sourceTable` ($colList) VALUES ".implode(', ', $tuples), $values);
                $inserted += count($rows);
            } catch (Exception $e) {
                // Log error but continue
            }
        }

        return $inserted;
    }

    // ------------------------------------------------------------------
    // Usuarios
    // ------------------------------------------------------------------

    protected function migrateUsers(string $sourceDb, ?string $church, array $importIds, array $ministryMap, array $sedeMap): array
    {
        if (empty($importIds)) {
            return ['inserted' => 0, 'id_map' => [], 'warnings' => []];
        }

        $importIn = implode(',', $importIds);
        $rows = DB::connection($sourceDb)
            ->table('users')
            ->whereRaw("`id` IN ($importIn)")
            ->get();

        // Consultar ids de usuarios con rol Lider en la fuente (para is_leader)
        $liderIds = $this->getLiderUserIds($sourceDb, $importIds);
        $liderSet = array_flip($liderIds);

        $inserted = 0;
        $idMap = [];
        $warnings = [];

        foreach ($rows as $row) {
            $oldId = (int) $row->id;

            $existing = null;
            $email = $row->email;

            if (! empty($email)) {
                $existing = DB::connection($this->targetConnection)
                    ->table('users')
                    ->where('email', $email)
                    ->value('id');
            }

            if ($existing) {
                $idMap[$oldId] = (int) $existing;

                continue;
            }

            $docNumber = ($row->doc_number ?? '') !== '' ? $row->doc_number : 'IMPORT-'.$oldId.'-'.$sourceDb;

            $ministryId = null;
            if (! empty($row->ministerial_range) && isset($ministryMap[$row->ministerial_range])) {
                $ministryId = $ministryMap[$row->ministerial_range];
            }

            $sedeId = null;
            if (! empty($row->church) && isset($sedeMap[$row->church])) {
                $sedeId = $sedeMap[$row->church];
            }

            $data = [
                'uuid' => ! empty($row->uuid) ? $row->uuid : (string) Str::uuid(),
                'fullname' => $row->fullname,
                'born_date' => $row->born_date ?? '',
                'sex' => $row->sex ?? null,
                'email' => $email,
                'email_verified_at' => $row->email_verified_at ?? null,
                'phone' => $row->phone ?? null,
                'church' => $row->church ?? null,
                'mentor' => $row->mentor ?? null,
                'ministerial_range' => $row->ministerial_range ?? null,
                'ministry_id' => $ministryId,
                'sede_id' => $sedeId,
                'celula' => $row->celula ?? null,
                'doc_number' => $docNumber,
                'lider_celula' => $row->lider_celula ?? 'No',
                'password' => $row->password ?? null,
                'must_change_password' => false,
                'is_active' => true,
                'is_leader' => isset($liderSet[$oldId]),
                'created_at' => $row->created_at ?? null,
                'updated_at' => $row->updated_at ?? null,
            ];

            try {
                $newId = DB::connection($this->targetConnection)
                    ->table('users')
                    ->insertGetId($data);

                $idMap[$oldId] = (int) $newId;
                $inserted++;
            } catch (Exception $e) {
                // Duplicado (p.ej. uuid/doc_number): buscar por email y remapear si existe
                $found = ! empty($email)
                    ? DB::connection($this->targetConnection)->table('users')->where('email', $email)->value('id')
                    : null;

                if ($found) {
                    $idMap[$oldId] = (int) $found;
                } else {
                    $warnings[] = "Usuario #{$oldId} ({$row->fullname}) no se pudo importar: ".$e->getMessage();
                }
            }
        }

        if ($inserted > 0) {
            $warnings[] = "Usuarios importados: $inserted. Contraseñas preservadas del origen.";
        }

        return ['inserted' => $inserted, 'id_map' => $idMap, 'warnings' => $warnings];
    }

    protected function getRoleMap(string $sourceDb): array
    {
        if (! $this->tableExists($sourceDb, 'roles')) {
            return [];
        }

        try {
            return DB::connection($sourceDb)
                ->table('roles')
                ->get(['id', 'name'])
                ->pluck('name', 'id')
                ->map(fn ($v) => (string) $v)
                ->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    protected function getLiderUserIds(string $sourceDb, array $importIds): array
    {
        if (empty($importIds)) {
            return [];
        }

        $importIn = implode(',', $importIds);

        return DB::connection($sourceDb)
            ->table('model_has_roles')
            ->where('role_id', $this->sourceLiderRoleId)
            ->where('model_type', 'App\\Models\\User')
            ->whereRaw("`model_id` IN ($importIn)")
            ->pluck('model_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->toArray();
    }

    protected function migrateUserRoles(string $sourceDb, array $idMap, array $sourceRoles, array $targetRolesByName): int
    {
        if (empty($idMap) || ! $this->tableExists($sourceDb, 'model_has_roles')) {
            return 0;
        }

        $oldIds = array_keys($idMap);
        $inserted = 0;

        $rows = DB::connection($sourceDb)
            ->table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->whereIn('model_id', $oldIds)
            ->get();

        foreach ($rows as $row) {
            $roleName = $sourceRoles[(int) $row->role_id] ?? null;
            if ($roleName === null) {
                continue;
            }

            $targetRoleId = $targetRolesByName[$roleName] ?? null;
            if ($targetRoleId === null) {
                continue;
            }

            try {
                DB::connection($this->targetConnection)
                    ->table('model_has_roles')
                    ->insertOrIgnore([
                        'role_id' => $targetRoleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $idMap[(int) $row->model_id],
                    ]);
                $inserted++;
            } catch (Exception $e) {
                // ignorar
            }
        }

        return $inserted;
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    protected function migrateRelationships(string $sourceDb, array $idMap): int
    {
        if (empty($idMap) || ! $this->tableExists($sourceDb, 'relationships') || ! $this->tableExists($this->targetConnection, 'relationships')) {
            return 0;
        }

        $oldIds = array_keys($idMap);
        $inserted = 0;

        $rows = DB::connection($sourceDb)
            ->table('relationships')
            ->whereIn('mentor_id', $oldIds)
            ->whereIn('disciple_id', $oldIds)
            ->get();

        foreach ($rows as $row) {
            $mentorId = $idMap[(int) $row->mentor_id] ?? null;
            $discipleId = $idMap[(int) $row->disciple_id] ?? null;

            if ($mentorId === null || $discipleId === null) {
                continue;
            }

            $suspended = ($row->suspended ?? 'No') === 'Si' ? 'Si' : 'No';

            $exists = DB::connection($this->targetConnection)
                ->table('relationships')
                ->where('mentor_id', $mentorId)
                ->where('disciple_id', $discipleId)
                ->where('f_meet', $row->f_meet)
                ->exists();

            if ($exists) {
                continue;
            }

            try {
                DB::connection($this->targetConnection)
                    ->table('relationships')
                    ->insertOrIgnore([
                        'mentor_id' => $mentorId,
                        'disciple_id' => $discipleId,
                        'f_meet' => $row->f_meet,
                        'suspended' => $suspended,
                        'why_suspended' => $row->why_suspended ?? null,
                        'theme_meetings_id' => $row->theme_meetings_id ?? null,
                        'other_theme' => $row->other_theme ?? null,
                        'culminate' => $row->culminate ?? null,
                        'initiative' => $row->initiative ?? null,
                        'reading' => $row->reading ?? null,
                        'testimonials' => $row->testimonials ?? null,
                        'pray_together' => $row->pray_together ?? null,
                        'description' => $row->description ?? null,
                        'uuid' => (string) Str::uuid(),
                        'created_at' => $row->created_at ?? null,
                        'updated_at' => $row->updated_at ?? null,
                    ]);
                $inserted++;
            } catch (Exception $e) {
                // ignorar
            }
        }

        return $inserted;
    }

    // ------------------------------------------------------------------
    // Reports célula
    // ------------------------------------------------------------------

    protected function migrateReportsCelula(string $sourceDb, array $idMap, ?string $church): array
    {
        $inserted = 0;
        $skipped = 0;
        $warnings = [];

        if (empty($idMap) || ! $this->tableExists($sourceDb, 'reports_celula') || ! $this->tableExists($this->targetConnection, 'reports_celula')) {
            return ['inserted' => 0, 'warnings' => $warnings];
        }

        $oldIds = array_keys($idMap);

        // Índices de usuarios importados: solo de la iglesia destino
        $byName = [];
        $byCelula = [];

        $sourceUsersQuery = DB::connection($sourceDb)->table('users')->whereIn('id', $oldIds);
        if ($church !== null && $church !== '') {
            $sourceUsersQuery->where('church', $church);
        }
        $sourceUsers = $sourceUsersQuery->get();

        foreach ($sourceUsers as $user) {
            $newId = $idMap[(int) $user->id];
            $nameKey = $this->normalizeName($user->fullname ?? '');

            if ($nameKey !== '' && ! isset($byName[$nameKey])) {
                $byName[$nameKey] = $newId;
            }

            if ($user->celula !== null && $user->celula !== '') {
                $celulaKey = (string) $user->celula;
                // Preferir líderes de célula si hay ambigüedad
                if (! isset($byCelula[$celulaKey]) || ($user->lider_celula ?? 'No') === 'Si') {
                    $byCelula[$celulaKey] = $newId;
                }
            }
        }

        // Aliases confiables: nombres con typos conocidos → usuario Once
        // figueroa tino (reordenado) → Tino Figueroa
        // marcelo esteban martínez → Esteban Martínez
        $fuzzyAliases = [];

        $tinoTarget = $byName[$this->normalizeName('Tino Figueroa')] ?? null;
        if ($tinoTarget !== null) {
            $fuzzyAliases[$this->normalizeName('Figueroa Tino')] = $tinoTarget;
        }

        $estebanTarget = $byName[$this->normalizeName('Esteban Martínez')] ?? null;
        if ($estebanTarget !== null) {
            $fuzzyAliases[$this->normalizeName('Marcelo Esteban Martínez')] = $estebanTarget;
        }

        DB::connection($sourceDb)
            ->table('reports_celula')
            ->chunkById(300, function ($rows) use ($byName, $byCelula, $fuzzyAliases, &$inserted, &$skipped) {
                foreach ($rows as $row) {
                    $mentorId = null;
                    $nameKey = $this->normalizeName($row->lider ?? '');

                    // 1. Nombre exacto
                    if ($nameKey !== '' && isset($byName[$nameKey])) {
                        $mentorId = $byName[$nameKey];
                    }
                    // 2. Alias confiable (typos conocidos)
                    elseif (isset($fuzzyAliases[$nameKey])) {
                        $mentorId = $fuzzyAliases[$nameKey];
                    }
                    // 3. Célula como fallback
                    elseif ($row->celula !== null && $row->celula !== '' && isset($byCelula[(string) $row->celula])) {
                        $mentorId = $byCelula[(string) $row->celula];
                    }

                    if ($mentorId === null) {
                        $skipped++;

                        continue;
                    }

                    if (empty($row->f_meet)) {
                        $skipped++;

                        continue;
                    }

                    $suspended = ($row->suspended ?? 'No') === 'Si' ? 'Si' : 'No';

                    $exists = DB::connection($this->targetConnection)
                        ->table('reports_celula')
                        ->where('mentor_id', $mentorId)
                        ->where('f_meet', $row->f_meet)
                        ->where('celula', (string) ($row->celula ?? ''))
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    try {
                        DB::connection($this->targetConnection)
                            ->table('reports_celula')
                            ->insertOrIgnore([
                                'mentor_id' => $mentorId,
                                'f_meet' => $row->f_meet,
                                'celula' => (string) ($row->celula ?? ''),
                                'suspended' => $suspended,
                                'why_suspended' => $row->why_suspended ?? null,
                                'lider' => $row->lider ?? '',
                                'message_title' => $row->message_title ?? null,
                                'who_meet' => $row->who_meet ?? null,
                                'format' => $row->format ?? null,
                                'people_qty' => $row->people_qty ?? null,
                                'new_people_qty' => $row->new_people_qty ?? null,
                                'mentoring' => $row->mentoring ?? null,
                                'observations' => $row->observations ?? null,
                                'created_at' => $row->created_at ?? null,
                                'updated_at' => $row->updated_at ?? null,
                            ]);
                        $inserted++;
                    } catch (Exception $e) {
                        $skipped++;
                    }
                }
            }, 'id');

        if ($skipped > 0) {
            $warnings[] = "Reportes de célula omitidos: $skipped (sin líder que coincida con un usuario importado o sin fecha de reunión).";
        }

        return ['inserted' => $inserted, 'warnings' => $warnings];
    }

    protected function normalizeName(string $name): string
    {
        $name = mb_strtolower(trim($name), 'UTF-8');

        $map = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
        ];

        $name = strtr($name, $map);

        return preg_replace('/\s+/', ' ', $name);
    }

    // ------------------------------------------------------------------
    // Utilidades de conexión
    // ------------------------------------------------------------------

    protected function ensureSourceConnection(string $sourceDb): void
    {
        $mysql = config('database.connections.mysql');

        if (! isset(config('database.connections')[$sourceDb])) {
            config([
                "database.connections.$sourceDb" => array_merge($mysql, ['database' => $sourceDb]),
            ]);
        }

        DB::purge($sourceDb);
        DB::reconnect($sourceDb);
    }

    protected function tableExists(string $connection, string $table): bool
    {
        try {
            $result = DB::connection($connection)
                ->select('SELECT COUNT(*) as n FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?', [
                    $connection === 'mysql' ? config('database.connections.mysql.database') : $connection,
                    $table,
                ]);

            return (int) $result[0]->n > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function getColumns(string $connection, string $table): array
    {
        $db = $connection === 'mysql' ? config('database.connections.mysql.database') : $connection;
        $rows = DB::connection($connection)
            ->select("SHOW COLUMNS FROM `$db`.`$table`");

        return array_map(fn ($r) => $r->Field, $rows);
    }
}

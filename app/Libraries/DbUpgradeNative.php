<?php

namespace App\Libraries;

class DbUpgradeNative
{
    protected string $migrationsPath;

    protected array $benignErrorCodes = [
        1060, // ER_DUP_FIELDNAME
        1061, // ER_DUP_KEYNAME
        1062, // ER_DUP_ENTRY
        1050, // ER_TABLE_EXISTS_ERROR
        1091, // ER_CANT_DROP_FIELD_OR_KEY
    ];

    public function __construct()
    {
        $this->migrationsPath = FCPATH . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        $this->ensureTrackingTable();
    }

    protected function ensureTrackingTable()
    {
        $db  = \Config\Database::connect();
        $sql = "CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `version` VARCHAR(20) NOT NULL,
            `migration_name` VARCHAR(255) NOT NULL,
            `applied_at` DATETIME NOT NULL,
            PRIMARY KEY (`version`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $db->query($sql);
        } catch (\Throwable $e) {
            // Silence if exists
        }
    }

    protected function homepageExpectedSections(): array
    {
        return ['hero', 'videos', 'news', 'stats', 'announcements', 'agenda', 'programs', 'testimonials', 'partners'];
    }

    protected function homepageExpectedSuffixes(): array
    {
        return ['enabled', 'title', 'subtitle', 'order'];
    }

    public function checkHomepageIntegrity(): array
    {
        $db     = \Config\Database::connect();
        $issues = [];

        $rows = $db->table('settings')
            ->select('key, value')
            ->where('group_name', 'homepage')
            ->get()
            ->getResult();

        if (empty($rows)) {
            return ['valid' => true, 'issues' => [], 'row_count' => 0];
        }

        $byKey         = [];
        $duplicateSeen = [];
        foreach ($rows as $row) {
            if (isset($byKey[$row->key])) {
                if (! isset($duplicateSeen[$row->key])) {
                    $issues[]                = "Key duplikat ditemukan: homepage.{$row->key}";
                    $duplicateSeen[$row->key] = true;
                }
                continue;
            }
            $byKey[$row->key] = $row->value;
        }

        foreach ($this->homepageExpectedSections() as $section) {
            foreach ($this->homepageExpectedSuffixes() as $suffix) {
                $key = $section . '.' . $suffix;
                if (! array_key_exists($key, $byKey)) {
                    $issues[] = "Key wajib hilang: homepage.{$key}";
                }
            }
        }

        foreach ($byKey as $key => $value) {
            if (substr($key, -8) === '.enabled') {
                if ($value !== '0' && $value !== '1') {
                    $issues[] = "homepage.{$key} harus '0' atau '1', ditemukan: '" . $value . "'";
                }
            } elseif (substr($key, -6) === '.order') {
                if ($value === '' || ! ctype_digit((string) $value)) {
                    $issues[] = "homepage.{$key} harus bilangan bulat >= 0, ditemukan: '" . $value . "'";
                }
            }
        }

        return [
            'valid'     => empty($issues),
            'issues'    => $issues,
            'row_count' => count($rows),
        ];
    }

    public function repairHomepageConfiguration(): array
    {
        $db     = \Config\Database::connect();
        $report = [
            'success'          => true,
            'snapshot_before'  => [],
            'rows_deleted'     => 0,
            'migration_result' => null,
            'integrity_after'  => null,
            'message'          => '',
        ];

        $existing = $db->table('settings')
            ->select('key, value')
            ->where('group_name', 'homepage')
            ->get()
            ->getResult();

        foreach ($existing as $row) {
            $report['snapshot_before'][$row->key] = $row->value;
        }

        $db->table('settings')->where('group_name', 'homepage')->delete();
        $report['rows_deleted'] = $db->affectedRows();

        $db->table('schema_migrations')->where('version', '012')->delete();

        $migration012 = null;
        foreach ($this->getAvailableMigrations() as $migration) {
            if ($migration['version'] === '012') {
                $migration012 = $migration;
                break;
            }
        }

        if ($migration012 === null) {
            $report['success'] = false;
            $report['message'] = 'File migration 012 tidak ditemukan — perbaikan dibatalkan setelah data lama dihapus.';
            return $report;
        }

        $result                     = $this->runMigrationFile($migration012);
        $report['migration_result'] = $result;

        if (! $result['success']) {
            $report['success'] = false;
            $report['message'] = 'Gagal menjalankan ulang migration 012: ' . $result['error'];
            return $report;
        }

        $report['integrity_after'] = $this->checkHomepageIntegrity();
        if (! $report['integrity_after']['valid']) {
            $report['success'] = false;
            $report['message'] = 'Migration 012 berhasil dijalankan ulang, tetapi hasilnya masih tidak lolos Configuration Integrity Check.';
            return $report;
        }

        $report['message'] = 'Konfigurasi Homepage berhasil dipulihkan ke nilai default dan lolos Configuration Integrity Check.';
        return $report;
    }

    public function getAvailableMigrations(): array
    {
        $files = [];
        if (! is_dir($this->migrationsPath)) {
            return $files;
        }

        foreach (glob($this->migrationsPath . '*.sql') as $path) {
            $basename = basename($path);
            if (! preg_match('/^(\d+)_(.+)\.sql$/', $basename, $m)) {
                continue;
            }
            $files[] = [
                'version'  => $m[1],
                'name'     => $m[2],
                'filename' => $basename,
                'path'     => $path,
            ];
        }

        usort($files, static fn ($a, $b) => strcmp($a['version'], $b['version']));
        return $files;
    }

    public function getAppliedVersions(): array
    {
        $db = \Config\Database::connect();
        $this->ensureTrackingTable();

        $rows = $db->table('schema_migrations')
            ->select('version, migration_name, applied_at')
            ->get()
            ->getResult();

        $applied = [];
        foreach ($rows as $row) {
            $applied[$row->version] = $row;
        }

        return $applied;
    }

    public function getPendingMigrations(): array
    {
        $applied = $this->getAppliedVersions();
        $pending = [];

        foreach ($this->getAvailableMigrations() as $migration) {
            if (! isset($applied[$migration['version']])) {
                $pending[] = $migration;
            }
        }

        return $pending;
    }

    public function getStatus(): array
    {
        $available = $this->getAvailableMigrations();
        $applied   = $this->getAppliedVersions();
        $pending   = $this->getPendingMigrations();

        return [
            'up_to_date'           => empty($pending),
            'total_migrations'     => count($available),
            'applied_count'        => count($applied),
            'pending_count'        => count($pending),
            'pending_migrations'   => $pending,
            'available_migrations' => $available,
            'applied_migrations'   => $applied,
        ];
    }

    public function runPending(): array
    {
        $report = [
            'success' => true,
            'ran'     => [],
            'message' => '',
        ];

        foreach ($this->getPendingMigrations() as $migration) {
            $result          = $this->runMigrationFile($migration);
            $report['ran'][] = $result;

            if (! $result['success']) {
                $report['success'] = false;
                $report['message'] = 'Migrasi dihentikan pada ' . $migration['filename'] . ': ' . $result['error'];
                return $report;
            }
        }

        $report['message'] = empty($report['ran'])
            ? 'Database sudah versi terbaru. Tidak ada migrasi yang perlu dijalankan.'
            : 'Semua migrasi berhasil dijalankan (' . count($report['ran']) . ' file).';

        return $report;
    }

    public function runMigrationFile(array $migration): array
    {
        $db     = \Config\Database::connect();
        $result = [
            'version'    => $migration['version'],
            'filename'   => $migration['filename'],
            'success'    => true,
            'statements' => [],
            'error'      => '',
        ];

        if (! is_file($migration['path'])) {
            $result['success'] = false;
            $result['error']   = 'File migrasi tidak ditemukan.';
            return $result;
        }

        $sql        = file_get_contents($migration['path']);
        $statements = $this->splitStatements($sql);

        $db->transBegin();

        foreach ($statements as $statement) {
            $outcome               = $this->runStatement($statement);
            $result['statements'][] = $outcome;

            if ($outcome['status'] === 'failed') {
                $db->transRollback();
                $result['success'] = false;
                $result['error']   = $outcome['message'];
                return $result;
            }
        }

        $db->transCommit();

        $db->table('schema_migrations')->insert([
            'version'        => $migration['version'],
            'migration_name' => $migration['name'],
            'applied_at'     => date('Y-m-d H:i:s'),
        ]);

        return $result;
    }

    protected function splitStatements(string $sql): array
    {
        $sql        = preg_replace('/--.*$/m', '', $sql);
        $sql        = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $parts      = explode(';', $sql);
        $statements = [];

        foreach ($parts as $part) {
            $trimmed = trim($part);
            if ($trimmed !== '') {
                $statements[] = $trimmed;
            }
        }

        return $statements;
    }

    protected function runStatement(string $statement): array
    {
        $db      = \Config\Database::connect();
        $preview = substr(preg_replace('/\s+/', ' ', $statement), 0, 150);

        if (preg_match('/ALTER\s+TABLE\s+`?(\w+)`?\s+ADD\s+COLUMN\s+`?(\w+)`?/i', $statement, $m)) {
            if ($db->fieldExists($m[2], $m[1])) {
                return ['sql' => $preview, 'status' => 'skipped', 'message' => "Kolom `{$m[2]}` pada `{$m[1]}` sudah ada — dilewati."];
            }
        }

        if (preg_match('/ALTER\s+TABLE\s+`?(\w+)`?\s+ADD\s+(?:UNIQUE\s+)?(?:INDEX|KEY)\s+`?(\w+)`?/i', $statement, $m)) {
            if ($this->indexExists($m[1], $m[2])) {
                return ['sql' => $preview, 'status' => 'skipped', 'message' => "Index `{$m[2]}` pada `{$m[1]}` sudah ada — dilewati."];
            }
        }

        try {
            $ok = $db->query($statement);
            if ($ok !== false) {
                return ['sql' => $preview, 'status' => 'success', 'message' => 'OK'];
            }
        } catch (\Throwable $e) {
            $code = $e->getCode();
            if (in_array((int) $code, $this->benignErrorCodes, true)) {
                return ['sql' => $preview, 'status' => 'skipped', 'message' => 'Sudah diterapkan sebelumnya (kode ' . $code . ') — dilewati.'];
            }

            return [
                'sql'     => $preview,
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ];
        }

        return ['sql' => $preview, 'status' => 'success', 'message' => 'OK'];
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        $db    = \Config\Database::connect();
        $query = $db->query(
            'SELECT COUNT(1) AS cnt FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $indexName]
        );
        $row = $query ? $query->getRow() : null;
        return $row && (int) $row->cnt > 0;
    }
}

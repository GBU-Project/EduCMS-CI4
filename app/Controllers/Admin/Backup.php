<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Backup extends BaseController
{
    protected array $benignErrorCodes = [1050, 1060, 1061, 1062, 1091];

    public function index()
    {
        $enableRestore = (bool) (env('ENABLE_WEB_DB_RESTORE') ?? false);

        $data = [
            'title'                 => 'Database Manager',
            'breadcrumbs'           => ['Database Manager' => ''],
            'enable_web_db_restore' => $enableRestore,
        ];

        return view('admin/backup/index', $data);
    }

    public function run()
    {
        $db = \Config\Database::connect();
        helper(['download', 'educms']);

        $filename = 'educms_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $tables   = $db->listTables();

        $output = "-- EduCMS Database Backup\n";
        $output .= '-- Generated at: ' . date('Y-m-d H:i:s') . "\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            $query = $db->query('SHOW CREATE TABLE `' . $table . '`');
            $row   = $query->getRowArray();
            if ($row) {
                $createSql = $row['Create Table'] ?? reset($row);
                $output .= 'DROP TABLE IF EXISTS `' . $table . "`;\n";
                $output .= $createSql . ";\n\n";

                $rows = $db->table($table)->get()->getResultArray();
                foreach ($rows as $r) {
                    $values = array_map(static function ($v) use ($db) {
                        if ($v === null) {
                            return 'NULL';
                        }
                        return $db->escape($v);
                    }, $r);

                    $output .= 'INSERT INTO `' . $table . '` (`' . implode('`, `', array_keys($r)) . '`) VALUES (' . implode(', ', $values) . ");\n";
                }
                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $gzOutput = gzencode($output, 9);
        $gzName   = $filename . '.gz';

        $this->logActivity('Backup', 'run', null, 'Database backup downloaded: ' . $gzName);

        return $this->response->download($gzName, $gzOutput);
    }

    public function restore()
    {
        $userId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $userId)) {
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat melakukan Restore Database.']));
        }

        $enableRestore = (bool) (env('ENABLE_WEB_DB_RESTORE') ?? false);
        if (! $enableRestore) {
            $this->_logRestoreAttempt('blocked (feature disabled)', (int) $userId);
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Restore Database via web dinonaktifkan demi keamanan. Gunakan restore via CLI atau aktifkan ENABLE_WEB_DB_RESTORE.']));
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to(base_url('admin/backup'));
        }

        if (! $this->request->getPost('confirm_restore')) {
            session()->setFlashdata('error', 'Restore dibatalkan: konfirmasi tidak diberikan.');
            return redirect()->to(base_url('admin/backup'));
        }

        $confirmPassword = (string) $this->request->getPost('confirm_password');
        if ($confirmPassword === '') {
            $this->_logRestoreAttempt('blocked (no password re-entry)', (int) $userId);
            session()->setFlashdata('error', 'Restore dibatalkan: masukkan kembali password Anda untuk konfirmasi.');
            return redirect()->to(base_url('admin/backup'));
        }

        $db          = \Config\Database::connect();
        $currentUser = $db->table('users')->where('id', $userId)->get()->getRow();

        if (! $currentUser || ! password_verify($confirmPassword, $currentUser->password)) {
            $this->_logRestoreAttempt('blocked (wrong password re-entry)', (int) $userId);
            session()->setFlashdata('error', 'Restore dibatalkan: password konfirmasi salah.');
            return redirect()->to(base_url('admin/backup'));
        }

        $file = $this->request->getFile('sql_file');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            session()->setFlashdata('error', 'Pilih file backup database (.sql atau .sql.gz) terlebih dahulu.');
            return redirect()->to(base_url('admin/backup'));
        }

        $originalName = $file->getClientName();
        $extension    = strtolower($file->getClientExtension());

        if ($extension !== 'sql' && $extension !== 'gz') {
            $this->_logRestoreAttempt('blocked (invalid extension)', (int) $userId, 'file=' . $originalName);
            session()->setFlashdata('error', 'Format file tidak valid. Silakan unggah file cadangan database berekstensi .sql atau .sql.gz.');
            return redirect()->to(base_url('admin/backup'));
        }

        $rawContent = file_get_contents($file->getTempName());
        if ($rawContent === false || trim($rawContent) === '') {
            session()->setFlashdata('error', 'File cadangan database kosong atau tidak dapat dibaca.');
            return redirect()->to(base_url('admin/backup'));
        }

        if ($extension === 'gz' || (substr($rawContent, 0, 2) === "\x1f\x8b")) {
            $sql = @gzdecode($rawContent);
            if ($sql === false) {
                session()->setFlashdata('error', 'File terkompresi .sql.gz rusak atau tidak dapat diekstrak.');
                return redirect()->to(base_url('admin/backup'));
            }
        } else {
            $sql = $rawContent;
        }

        if (trim($sql) === '') {
            session()->setFlashdata('error', 'File cadangan database kosong atau tidak dapat dibaca.');
            return redirect()->to(base_url('admin/backup'));
        }

        $statements = $this->_splitSqlStatements($sql);
        if (empty($statements)) {
            session()->setFlashdata('error', 'Format file tidak didukung: tidak ditemukan perintah SQL yang valid dalam file ini.');
            return redirect()->to(base_url('admin/backup'));
        }

        $executed = 0;
        $skipped  = 0;

        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        $db->transBegin();

        foreach ($statements as $statement) {
            try {
                $ok = $db->query($statement);
                if ($ok !== false) {
                    $executed++;
                    continue;
                }
            } catch (\Throwable $e) {
                $errCode = $e->getCode();
                if (in_array((int) $errCode, $this->benignErrorCodes, true)) {
                    $skipped++;
                    continue;
                }

                $db->transRollback();
                $db->query('SET FOREIGN_KEY_CHECKS = 1');
                $this->logActivity('Database Manager', 'Restore Database Gagal', null, 'File backup: ' . $originalName . ' | Error: ' . $e->getMessage());
                $this->_logRestoreAttempt('failed', (int) $userId, 'file=' . $originalName . ' error=' . $e->getMessage());
                session()->setFlashdata('error', 'Restore Database Gagal! Periksa kembali file backup atau lihat log sistem.');
                return redirect()->to(base_url('admin/backup'));
            }
        }

        $db->transCommit();
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
        $this->logActivity('Database Manager', 'Restore Database Berhasil', null, 'File backup: ' . $originalName . ' | Statements OK: ' . $executed . ($skipped > 0 ? ', dilewati: ' . $skipped : ''));
        $this->_logRestoreAttempt('success', (int) $userId, 'file=' . $originalName . ' statements_ok=' . $executed . ' skipped=' . $skipped);
        session()->setFlashdata('success', 'Restore Database Berhasil! Database berhasil dipulihkan.');
        return redirect()->to(base_url('admin/backup'));
    }

    private function _logRestoreAttempt(string $outcome, int $userId, string $detail = '')
    {
        $logDir = WRITEPATH . 'logs';
        if (! is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $ip   = $this->request->getIPAddress();
        $line = sprintf("[%s] user_id=%s ip=%s outcome=%s %s\n", date('Y-m-d H:i:s'), $userId, $ip, $outcome, $detail);
        @file_put_contents($logDir . '/db_restore_audit.log', $line, FILE_APPEND | LOCK_EX);
    }

    private function _splitSqlStatements(string $sql): array
    {
        $sql        = str_replace("\r\n", "\n", $sql);
        $statements = [];
        $current    = '';
        $inString   = false;
        $stringChar = '';
        $inComment  = false;
        $commentType = '';
        $len        = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            $next = ($i + 1 < $len) ? $sql[$i + 1] : '';

            if (! $inString) {
                if (! $inComment) {
                    if ($char === '#' || ($char === '-' && $next === '-')) {
                        $inComment   = true;
                        $commentType = 'line';
                        if ($char === '-') {
                            $i++;
                        }
                        continue;
                    }
                    if ($char === '/' && $next === '*') {
                        $inComment   = true;
                        $commentType = 'block';
                        $i++;
                        continue;
                    }
                } else {
                    if ($commentType === 'line' && $char === "\n") {
                        $inComment = false;
                    } elseif ($commentType === 'block' && $char === '*' && $next === '/') {
                        $inComment = false;
                        $i++;
                    }
                    continue;
                }
            }

            if (! $inComment) {
                if ($inString) {
                    if ($char === '\\') {
                        $current .= $char . $next;
                        $i++;
                        continue;
                    }

                    if ($char === $stringChar) {
                        if ($next === $stringChar) {
                            $current .= $char . $next;
                            $i++;
                            continue;
                        }

                        $inString   = false;
                        $stringChar = '';
                    }
                } else {
                    if ($char === "'" || $char === '"' || $char === '`') {
                        $inString   = true;
                        $stringChar = $char;
                    }
                }

                if ($char === ';' && ! $inString) {
                    $trimmed = trim($current);
                    if ($trimmed !== '') {
                        $statements[] = $trimmed;
                    }
                    $current = '';
                    continue;
                }

                $current .= $char;
            }
        }

        $trimmed = trim($current);
        if ($trimmed !== '') {
            $statements[] = $trimmed;
        }

        return $statements;
    }

    protected function logActivity(string $module, string $action, ?string $oldValue = null, ?string $newValue = null)
    {
        $userId = session()->get('user_id');
        if (class_exists('\Logger')) {
            $logger = new \Logger();
            $logger->log($userId, $module, $action, $oldValue, $newValue);
        }
    }
}

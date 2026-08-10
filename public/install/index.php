<?php
/**
 * EduCMS CI4 — Web Installer
 * -------------------------------------------------------------------------
 * Standalone installer (no CI4 bootstrap dependency, since .env does not
 * exist yet). Steps: welcome -> requirements -> database -> install ->
 * admin account -> finish. State between steps (DB credentials) is kept
 * in the PHP session, never written to disk until the "install" step.
 *
 * SECURITY: once installation succeeds, install.lock is written and every
 * step refuses to run again until that file is removed manually. Delete
 * (or move out of the webroot) the whole /install folder after use.
 */

declare(strict_types=1);
session_start();

define('INSTALL_DIR', __DIR__);
define('ROOT_DIR', dirname(__DIR__, 2));
define('LOCK_FILE', INSTALL_DIR . '/install.lock');
define('ENV_FILE', ROOT_DIR . '/.env');
define('SQL_DUMP', ROOT_DIR . '/educms_database.sql');

$step = $_GET['step'] ?? 'welcome';
$validSteps = ['welcome', 'requirements', 'database', 'install', 'admin', 'finish'];
if (!in_array($step, $validSteps, true)) {
    $step = 'welcome';
}

$errors = [];
$notice = null;

// ---------------------------------------------------------------------
// Guard: block everything once installed, except the finish screen.
// ---------------------------------------------------------------------
if (is_file(LOCK_FILE) && $step !== 'finish') {
    render('locked', []);
    exit;
}

// ---------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------
function h(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

function checkRequirements(): array
{
    $checks = [];

    $checks[] = [
        'label'    => 'PHP >= 8.2',
        'ok'       => version_compare(PHP_VERSION, '8.2.0', '>='),
        'detail'   => 'Terpasang: PHP ' . PHP_VERSION,
        'required' => true,
    ];

    foreach (['mysqli', 'intl', 'mbstring', 'json', 'curl', 'gd', 'fileinfo'] as $ext) {
        $checks[] = [
            'label'    => "Ekstensi PHP: {$ext}",
            'ok'       => extension_loaded($ext),
            'detail'   => extension_loaded($ext) ? 'Aktif' : 'Tidak ditemukan',
            'required' => true,
        ];
    }

    $writablePaths = [
        'writable/'          => ROOT_DIR . '/writable',
        'writable/cache/'    => ROOT_DIR . '/writable/cache',
        'writable/logs/'     => ROOT_DIR . '/writable/logs',
        'writable/session/'  => ROOT_DIR . '/writable/session',
        'writable/uploads/'  => ROOT_DIR . '/writable/uploads',
        'writable/debugbar/' => ROOT_DIR . '/writable/debugbar',
        'public/uploads/'    => ROOT_DIR . '/public/uploads',
    ];
    foreach ($writablePaths as $label => $path) {
        $ok = is_dir($path) && is_writable($path);
        $checks[] = [
            'label'    => "Folder dapat ditulis: {$label}",
            'ok'       => $ok,
            'detail'   => $ok ? 'OK' : (is_dir($path) ? 'Tidak dapat ditulis (cek permission)' : 'Folder tidak ditemukan'),
            'required' => true,
        ];
    }

    // .env: either writable if it exists, or the root dir must be writable to create it.
    $envOk = is_file(ENV_FILE) ? is_writable(ENV_FILE) : is_writable(ROOT_DIR);
    $checks[] = [
        'label'    => 'File .env dapat ditulis',
        'ok'       => $envOk,
        'detail'   => $envOk ? 'OK' : 'Tidak dapat menulis .env di root aplikasi',
        'required' => true,
    ];

    $checks[] = [
        'label'    => 'File dump skema ditemukan (educms_database.sql)',
        'ok'       => is_file(SQL_DUMP),
        'detail'   => is_file(SQL_DUMP) ? 'OK' : 'Tidak ditemukan di root aplikasi',
        'required' => true,
    ];

    return $checks;
}

function requirementsPassed(array $checks): bool
{
    foreach ($checks as $c) {
        if ($c['required'] && !$c['ok']) {
            return false;
        }
    }
    return true;
}

function testDbConnection(array $cfg, ?string &$error = null): ?PDO
{
    try {
        $dsn = "mysql:host={$cfg['hostname']};port={$cfg['port']};charset=utf8mb4";
        $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        $error = $e->getMessage();
        return null;
    }
}

/**
 * Minimal, quote-aware SQL statement splitter (handles ';' inside
 * '...' / "..." / `...` strings, and skips comment lines).
 */
function splitSqlStatements(string $sql): array
{
    $statements = [];
    $buffer = '';
    $inString = null; // ', ", or `
    $len = strlen($sql);

    for ($i = 0; $i < $len; $i++) {
        $ch = $sql[$i];

        if ($inString !== null) {
            $buffer .= $ch;
            if ($ch === '\\' && $inString !== '`') {
                // consume escaped char
                if ($i + 1 < $len) {
                    $buffer .= $sql[$i + 1];
                    $i++;
                }
                continue;
            }
            if ($ch === $inString) {
                $inString = null;
            }
            continue;
        }

        if ($ch === "'" || $ch === '"' || $ch === '`') {
            $inString = $ch;
            $buffer .= $ch;
            continue;
        }

        // Skip -- line comments and /*! ... */ optimizer comments' delimiters are fine to keep,
        // MySQL treats /*! ... */ as executable, so we leave block comments intact.
        if ($ch === ';') {
            $trimmed = trim($buffer);
            if ($trimmed !== '') {
                $statements[] = $trimmed;
            }
            $buffer = '';
            continue;
        }

        $buffer .= $ch;
    }

    $trimmed = trim($buffer);
    if ($trimmed !== '') {
        $statements[] = $trimmed;
    }

    return $statements;
}

function importSqlDump(PDO $pdo, string $file): void
{
    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException('Tidak dapat membaca file dump SQL.');
    }

    // Strip full-line -- comments to keep the splitter simple/fast.
    $lines = explode("\n", $sql);
    $lines = array_filter($lines, static fn ($l) => !str_starts_with(ltrim($l), '--'));
    $sql = implode("\n", $lines);

    $statements = splitSqlStatements($sql);

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($statements as $stmt) {
        if ($stmt === '') {
            continue;
        }
        // Skip statements that are pure comments (e.g. MariaDB-only
        // /*M!...*/ dump headers) — nothing to execute, and some of
        // these are not valid standalone statements on plain MySQL.
        if (preg_match('/^\/\*.*\*\/\s*$/s', $stmt) === 1) {
            continue;
        }
        $pdo->exec($stmt);
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
}

/**
 * Detects the public base URL of the app, including a sub-path when the
 * project isn't sitting at the web server's document root (e.g. XAMPP
 * default DocumentRoot = htdocs/, project placed in htdocs/educms/).
 * This installer lives at <public>/install/index.php, so walking two
 * directories up from SCRIPT_NAME gives the path to <public>/.
 */
function detectBasePath(): string
{
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/install/index.php';
    // .../<sub-path>/public/install/index.php -> .../<sub-path>/public/
    $publicPath = rtrim(dirname(dirname($scriptName)), '/') . '/';
    return $publicPath === '//' ? '/' : $publicPath;
}

function writeEnvFile(array $db): void
{
    $encryptionKey = bin2hex(random_bytes(32));
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Base URL of the app's public/ folder — respects sub-folder installs
    // (e.g. http://localhost/educms/public/) instead of assuming domain root.
    $baseUrl = $scheme . $host . detectBasePath();

    $env = <<<ENV
CI_ENVIRONMENT = production

app.baseURL = '{$baseUrl}'

database.default.hostname = {$db['hostname']}
database.default.database = {$db['database']}
database.default.username = {$db['username']}
database.default.password = {$db['password']}
database.default.DBDriver = MySQLi
database.default.port = {$db['port']}

encryption.key = {$encryptionKey}

ENV;

    if (file_put_contents(ENV_FILE, $env) === false) {
        throw new RuntimeException('Gagal menulis file .env');
    }
}

function render(string $view, array $data): void
{
    extract($data);
    ?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>EduCMS Installer</title>
<style>
:root{--brand:#2563eb;--brand-dark:#1e40af;--bg:#f4f6fb;--ok:#16a34a;--bad:#dc2626;--border:#e2e8f0;--text:#1e293b;--muted:#64748b;}
*{box-sizing:border-box;}
body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);}
.wrap{max-width:720px;margin:0 auto;padding:32px 20px 64px;}
.card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:32px;box-shadow:0 1px 3px rgba(0,0,0,.04);}
h1{font-size:22px;margin:0 0 4px;}
h2{font-size:18px;margin:0 0 16px;}
p.lead{color:var(--muted);margin:0 0 24px;}
.steps{display:flex;gap:6px;margin-bottom:28px;}
.steps span{flex:1;height:6px;border-radius:4px;background:var(--border);}
.steps span.done{background:var(--brand);}
label{display:block;font-size:13px;font-weight:600;margin:14px 0 6px;color:#334155;}
input[type=text],input[type=password],input[type=email],input[type=number]{
  width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px;
}
input:focus{outline:2px solid var(--brand);outline-offset:1px;}
.row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.btn{display:inline-block;background:var(--brand);color:#fff;border:none;padding:11px 22px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;}
.btn:hover{background:var(--brand-dark);}
.btn.secondary{background:#fff;color:var(--text);border:1px solid var(--border);}
.actions{margin-top:26px;display:flex;justify-content:flex-end;gap:10px;}
table.checks{width:100%;border-collapse:collapse;margin-bottom:8px;}
table.checks td{padding:8px 4px;border-bottom:1px solid var(--border);font-size:14px;}
.badge{display:inline-block;padding:2px 9px;border-radius:99px;font-size:12px;font-weight:700;}
.badge.ok{background:#dcfce7;color:var(--ok);}
.badge.bad{background:#fee2e2;color:var(--bad);}
.alert{padding:12px 14px;border-radius:8px;font-size:14px;margin-bottom:18px;}
.alert.error{background:#fee2e2;color:#991b1b;}
.alert.notice{background:#dbeafe;color:#1e3a8a;}
.alert.success{background:#dcfce7;color:#166534;}
code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:13px;}
.muted{color:var(--muted);font-size:13px;}
ul.tips{font-size:13px;color:var(--muted);padding-left:18px;}
</style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h1>EduCMS — Instalasi CodeIgniter 4</h1>
    <p class="lead">Wizard instalasi awal aplikasi.</p>
    <?php $order = ['welcome','requirements','database','install','admin','finish']; $current = array_search($view === 'locked' ? 'welcome' : $view, $order, true); ?>
    <div class="steps">
      <?php foreach ($order as $i => $s): ?>
        <span class="<?= $i <= $current ? 'done' : '' ?>"></span>
      <?php endforeach; ?>
    </div>

    <?php foreach ($data['errors'] ?? [] as $e): ?>
      <div class="alert error"><?= h($e) ?></div>
    <?php endforeach; ?>
    <?php if (!empty($data['notice'])): ?>
      <div class="alert notice"><?= h($data['notice']) ?></div>
    <?php endif; ?>

    <?php includeView($view, $data); ?>
  </div>
  <p class="muted" style="text-align:center;margin-top:16px;">EduCMS Web Installer</p>
</div>
</body>
</html>
    <?php
}

function includeView(string $view, array $data): void
{
    extract($data);
    switch ($view) {
        case 'locked': ?>
            <h2>Instalasi sudah selesai</h2>
            <p>Aplikasi sudah pernah diinstal. Untuk keamanan, installer dinonaktifkan.</p>
            <p class="muted">Jika Anda perlu menjalankan ulang instalasi (misalnya di lingkungan development), hapus file:<br><code><?= h(str_replace(ROOT_DIR, '', LOCK_FILE)) ?></code></p>
            <div class="actions"><a class="btn" href="<?= h(detectBasePath()) ?>">Buka situs</a></div>
            <?php break;

        case 'welcome': ?>
            <h2>Selamat datang</h2>
            <p>Installer ini akan memandu Anda melalui:</p>
            <ul class="tips">
                <li>Pemeriksaan kebutuhan server (PHP, ekstensi, permission folder)</li>
                <li>Konfigurasi koneksi database</li>
                <li>Import skema &amp; data awal database</li>
                <li>Pembuatan akun administrator pertama</li>
            </ul>
            <p class="muted">Pastikan Anda sudah menyiapkan database MySQL/MariaDB kosong sebelum melanjutkan.</p>
            <div class="actions"><a class="btn" href="?step=requirements">Mulai Instalasi &rarr;</a></div>
            <?php break;

        case 'requirements':
            $checks = checkRequirements();
            $passed = requirementsPassed($checks);
            ?>
            <h2>Pemeriksaan Kebutuhan Server</h2>
            <table class="checks">
                <?php foreach ($checks as $c): ?>
                <tr>
                    <td><?= h($c['label']) ?><br><span class="muted"><?= h($c['detail']) ?></span></td>
                    <td style="text-align:right;"><span class="badge <?= $c['ok'] ? 'ok' : 'bad' ?>"><?= $c['ok'] ? 'OK' : 'GAGAL' ?></span></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php if (!$passed): ?>
                <div class="alert error">Beberapa kebutuhan belum terpenuhi. Perbaiki lalu muat ulang halaman ini.</div>
            <?php endif; ?>
            <div class="actions">
                <a class="btn secondary" href="?step=requirements">Cek Ulang</a>
                <a class="btn <?= $passed ? '' : 'secondary' ?>" href="<?= $passed ? '?step=database' : '#' ?>" <?= $passed ? '' : 'onclick="return false;" style="opacity:.5;cursor:not-allowed;"' ?>>Lanjut &rarr;</a>
            </div>
            <?php break;

        case 'database':
            $db = $_SESSION['install_db'] ?? ['hostname' => 'localhost', 'port' => '3306', 'database' => 'educms', 'username' => 'educms', 'password' => ''];
            ?>
            <h2>Konfigurasi Database</h2>
            <form method="post" action="?step=database">
                <div class="row">
                    <div>
                        <label>Host</label>
                        <input type="text" name="hostname" value="<?= h($db['hostname']) ?>" required>
                    </div>
                    <div>
                        <label>Port</label>
                        <input type="number" name="port" value="<?= h((string)$db['port']) ?>" required>
                    </div>
                </div>
                <label>Nama Database</label>
                <input type="text" name="database" value="<?= h($db['database']) ?>" required>
                <div class="row">
                    <div>
                        <label>Username</label>
                        <input type="text" name="username" value="<?= h($db['username']) ?>" required>
                    </div>
                    <div>
                        <label>Password</label>
                        <input type="password" name="password" value="<?= h($db['password']) ?>">
                    </div>
                </div>
                <div class="actions">
                    <a class="btn secondary" href="?step=requirements">&larr; Kembali</a>
                    <button class="btn" type="submit" name="action" value="test_and_continue">Uji Koneksi &amp; Lanjut &rarr;</button>
                </div>
            </form>
            <?php break;

        case 'install': ?>
            <h2>Menginstal Database</h2>
            <p>Skema dan data awal sedang diimpor ke database <code><?= h($_SESSION['install_db']['database'] ?? '') ?></code>...</p>
            <form method="post" action="?step=install">
                <div class="actions">
                    <button class="btn" type="submit" name="action" value="run_install">Jalankan Instalasi</button>
                </div>
            </form>
            <?php break;

        case 'admin': ?>
            <h2>Buat Akun Administrator</h2>
            <p class="muted">Akun ini akan menggantikan akun contoh (seed) bawaan dan mendapat peran <strong>Super Admin</strong>.</p>
            <form method="post" action="?step=admin">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" required>
                <div class="row">
                    <div>
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label>Password</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                    <div>
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirm" required minlength="8">
                    </div>
                </div>
                <div class="actions">
                    <button class="btn" type="submit" name="action" value="create_admin">Buat Akun &amp; Selesaikan &rarr;</button>
                </div>
            </form>
            <?php break;

        case 'finish': ?>
            <h2>Instalasi Selesai 🎉</h2>
            <div class="alert success">EduCMS berhasil diinstal.</div>
            <p>Langkah berikutnya:</p>
            <ul class="tips">
                <li>Login ke panel admin dengan akun yang baru dibuat</li>
                <li><strong>Hapus atau pindahkan folder <code>public/install/</code></strong> dari server produksi (atau minimal pastikan tetap terkunci — file <code>install.lock</code> sudah dibuat)</li>
                <li>Aplikasi masih berisi konten contoh (sample posts, galeri, dll) dari skema awal — hapus melalui panel admin bila tidak diperlukan</li>
            </ul>
            <div class="actions">
                <a class="btn secondary" href="<?= h(detectBasePath()) ?>admin/login">Login Admin</a>
                <a class="btn" href="<?= h(detectBasePath()) ?>">Buka Situs</a>
            </div>
            <?php break;
    }
}

// ---------------------------------------------------------------------
// POST handlers
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($step === 'database' && $action === 'test_and_continue') {
        $db = [
            'hostname' => trim($_POST['hostname'] ?? ''),
            'port'     => trim($_POST['port'] ?? '3306'),
            'database' => trim($_POST['database'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => (string)($_POST['password'] ?? ''),
        ];
        $_SESSION['install_db'] = $db;

        $error = null;
        $pdo = testDbConnection($db + ['database' => null], $error); // connect to server first (db may not exist)
        if ($pdo === null) {
            $errors[] = 'Koneksi database gagal: ' . $error;
            render('database', ['errors' => $errors]);
            exit;
        }

        // Try to create the database if it doesn't exist yet.
        try {
            $dbNameEscaped = str_replace('`', '', $db['database']);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbNameEscaped}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            $errors[] = 'Koneksi berhasil, tetapi gagal membuat/mengakses database "' . $db['database'] . '": ' . $e->getMessage();
            render('database', ['errors' => $errors]);
            exit;
        }

        header('Location: ?step=install');
        exit;
    }

    if ($step === 'install' && $action === 'run_install') {
        $db = $_SESSION['install_db'] ?? null;
        if (!$db) {
            header('Location: ?step=database');
            exit;
        }

        try {
            $pdo = new PDO(
                "mysql:host={$db['hostname']};port={$db['port']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            importSqlDump($pdo, SQL_DUMP);
            writeEnvFile($db);

            $_SESSION['install_done'] = true;
            header('Location: ?step=admin');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Instalasi gagal: ' . $e->getMessage();
            render('install', ['errors' => $errors]);
            exit;
        }
    }

    if ($step === 'admin' && $action === 'create_admin') {
        $db = $_SESSION['install_db'] ?? null;
        if (!$db || empty($_SESSION['install_done'])) {
            header('Location: ?step=database');
            exit;
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirm  = (string)($_POST['password_confirm'] ?? '');

        if ($fullName === '' || $username === '' || $email === '') {
            $errors[] = 'Semua kolom wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }

        if (empty($errors)) {
            try {
                $pdo = new PDO(
                    "mysql:host={$db['hostname']};port={$db['port']};dbname={$db['database']};charset=utf8mb4",
                    $db['username'],
                    $db['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                $hash = password_hash($password, PASSWORD_BCRYPT);

                // Replace the seeded demo admin (id=1) with the real account.
                // user_roles already links id=1 -> role_id=1 (Super Admin) from the dump.
                $stmt = $pdo->prepare(
                    'UPDATE `users` SET username = ?, email = ?, full_name = ?, password = ?, status = "active" WHERE id = 1'
                );
                $stmt->execute([$username, $email, $fullName, $hash]);

                file_put_contents(LOCK_FILE, date('c') . " installed\n");

                unset($_SESSION['install_db'], $_SESSION['install_done']);

                header('Location: ?step=finish');
                exit;
            } catch (Throwable $e) {
                $errors[] = 'Gagal membuat akun admin: ' . $e->getMessage();
            }
        }

        render('admin', ['errors' => $errors]);
        exit;
    }
}

// ---------------------------------------------------------------------
// Default GET render per step
// ---------------------------------------------------------------------
switch ($step) {
    case 'install':
        if (empty($_SESSION['install_db'])) {
            header('Location: ?step=database');
            exit;
        }
        break;
    case 'admin':
        if (empty($_SESSION['install_done'])) {
            header('Location: ?step=database');
            exit;
        }
        break;
}

render($step, ['errors' => $errors, 'notice' => $notice]);

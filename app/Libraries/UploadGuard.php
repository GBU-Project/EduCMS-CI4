<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class UploadGuard
{
    protected static array $dangerousExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar', 'pl', 'py', 'cgi', 'sh', 'asp', 'aspx', 'jsp', 'exe', 'bat', 'cmd', 'html', 'htm', 'js',
    ];

    protected static array $allowlists = [
        'ppdb' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'pdf'],
            'mimes'      => ['image/jpeg', 'image/png', 'application/pdf'],
        ],
        'media' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'docx', 'xlsx', 'mp4'],
            'mimes'      => [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'video/mp4',
            ],
        ],
    ];

    /**
     * Validate uploaded file against security allowlists & real MIME checks.
     */
    public function validateFile(UploadedFile $file, string $context = 'ppdb'): array
    {
        if (! $file->isValid() || $file->hasMoved()) {
            return ['status' => false, 'error' => 'File tidak valid atau sudah dipindahkan.'];
        }

        $clientName = $file->getClientName();

        // 1. Check null byte
        if (str_contains($clientName, "\0")) {
            return ['status' => false, 'error' => 'Karakter tidak valid ditemukan pada nama file.'];
        }

        // 2. Check double extension (e.g. file.jpg.php)
        $parts = explode('.', strtolower($clientName));
        if (count($parts) > 2) {
            foreach (array_slice($parts, 1) as $part) {
                if (in_array($part, self::$dangerousExtensions, true)) {
                    return ['status' => false, 'error' => 'Nama file mengandung ekstensi berbahaya.'];
                }
            }
        }

        // 3. Extension allowlist
        $ext = strtolower($file->getClientExtension());
        $allowlist = self::$allowlists[$context] ?? self::$allowlists['ppdb'];

        if (! in_array($ext, $allowlist['extensions'], true)) {
            return ['status' => false, 'error' => 'Ekstensi file tidak diizinkan. Ekstensi yang diperbolehkan: ' . implode(', ', $allowlist['extensions'])];
        }

        // 4. Real MIME type detection via finfo
        $tempPath = $file->getTempName();
        if ($tempPath && file_exists($tempPath)) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $realMime = finfo_file($finfo, $tempPath);
            finfo_close($finfo);

            if ($realMime && ! in_array($realMime, $allowlist['mimes'], true)) {
                return ['status' => false, 'error' => 'Tipe konten (MIME) file tidak valid.'];
            }
        }

        return ['status' => true];
    }
}

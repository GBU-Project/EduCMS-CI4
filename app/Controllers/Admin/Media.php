<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MediaModel;

class Media extends BaseController
{
    protected MediaModel $mediaModel;

    public function __construct()
    {
        $this->mediaModel = new MediaModel();
    }

    public function index()
    {
        $data = [
            'list'        => $this->mediaModel->findAll(),
            'title'       => 'Media Library',
            'breadcrumbs' => ['Media Library' => ''],
        ];

        return view('admin/media/index', $data);
    }

    public function upload()
    {
        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('file');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file, 'media');
                if ($uploadResult['status']) {
                    session()->setFlashdata('success', 'File berhasil diunggah.');
                } else {
                    session()->setFlashdata('error', 'Gagal mengunggah file: ' . $uploadResult['error']);
                }
            } else {
                session()->setFlashdata('error', 'Pilih file yang akan diunggah.');
            }
        }

        return redirect()->to(base_url('admin/media'));
    }

    public function drop_upload()
    {
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'error'  => 'File tidak valid atau gagal diunggah.',
            ]);
        }

        $uploadResult = $this->handleUpload($file, 'media');

        if ($uploadResult['status']) {
            $response = [
                'status'     => true,
                'id'         => $uploadResult['media_id'],
                'filename'   => $uploadResult['file_name'],
                'url'        => base_url($uploadResult['file_path']),
                'path'       => $uploadResult['file_path'],
                'is_image'   => (strpos($uploadResult['mime_type'], 'image') !== false),
                'delete_url' => base_url('admin/media/delete/' . $uploadResult['media_id']),
            ];

            return $this->response->setJSON($response);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status' => false,
            'error'  => $uploadResult['error'],
        ]);
    }

    public function editor_upload()
    {
        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'File tidak valid.',
            ]);
        }

        $uploadResult = $this->handleUpload($file, 'editor');

        if ($uploadResult['status']) {
            return $this->response->setJSON([
                'location' => base_url($uploadResult['file_path']),
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'error' => $uploadResult['error'],
        ]);
    }

    public function picker()
    {
        $search = $this->request->getGet('q');
        $images = $this->mediaModel->getImages($search ? trim($search) : '');

        $items = [];
        foreach ($images as $item) {
            $fileUrl = (! empty($item->directory) && ! empty($item->disk_name))
                ? $item->directory . '/' . $item->disk_name
                : '';
            if (empty($fileUrl)) {
                continue;
            }

            $items[] = [
                'id'       => $item->id,
                'url'      => base_url($fileUrl),
                'path'     => $fileUrl,
                'filename' => $item->filename,
                'alt_text' => $item->alt_text,
            ];
        }

        return $this->response->setJSON([
            'status' => true,
            'items'  => $items,
        ]);
    }

    public function delete($id)
    {
        $row = $this->mediaModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File media tidak ditemukan.');
        }

        $this->mediaModel->forceDeleteMedia((int) $id);
        $this->logActivity('Media', 'delete', json_encode($row), null);

        session()->setFlashdata('success', 'Media berhasil dihapus.');
        return redirect()->to(base_url('admin/media'));
    }

    public function bulk_delete()
    {
        $ids = $this->request->getPost('media_ids');

        if (empty($ids) || ! is_array($ids)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => false,
                    'error'  => 'Tidak ada file media yang dipilih.',
                ]);
            }
            session()->setFlashdata('error', 'Tidak ada file media yang dipilih.');
            return redirect()->to(base_url('admin/media'));
        }

        $successCount = 0;
        $failCount    = 0;

        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id <= 0) {
                continue;
            }

            $row = $this->mediaModel->find($id);
            if ($row) {
                $deleted = $this->mediaModel->forceDeleteMedia($id);
                if ($deleted) {
                    $this->logActivity('Media', 'bulk_delete', json_encode($row), null);
                    $successCount++;
                } else {
                    $failCount++;
                }
            } else {
                $failCount++;
            }
        }

        $msg = $successCount . ' file media berhasil dihapus.';
        if ($failCount > 0 && $successCount > 0) {
            $msg = $successCount . ' file berhasil dihapus, ' . $failCount . ' file gagal dihapus.';
        } elseif ($successCount === 0) {
            $msg = 'Gagal menghapus file media yang dipilih.';
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => ($successCount > 0),
                'message' => $msg,
                'deleted' => $successCount,
                'failed'  => $failCount,
            ]);
        }

        session()->setFlashdata(($successCount > 0 ? 'success' : 'error'), $msg);
        return redirect()->to(base_url('admin/media'));
    }

    protected function handleUpload($file, string $subfolder = 'media'): array
    {
        $targetDir = FCPATH . 'uploads/' . $subfolder . '/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $originalName = $file->getClientName();
        $newName      = $file->getRandomName();
        $safeName     = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        $mimeType  = $file->getClientMimeType();
        $size      = $file->getSize();
        $extension = $file->getClientExtension();
        $dirPath   = 'uploads/' . $subfolder;

        if ($file->move($targetDir, $safeName)) {
            $fullPath = $targetDir . $safeName;
            $width    = null;
            $height   = null;

            if (strpos($mimeType, 'image') !== false && function_exists('getimagesize')) {
                $imgSize = @getimagesize($fullPath);
                if ($imgSize) {
                    $width  = $imgSize[0];
                    $height = $imgSize[1];
                }
            }

            $userId = session()->get('user_id');

            $mediaData = [
                'filename'    => $originalName,
                'disk_name'   => $safeName,
                'directory'   => $dirPath,
                'extension'   => $extension,
                'mime_type'   => $mimeType,
                'width'       => $width,
                'height'      => $height,
                'size'        => $size,
                'checksum'    => md5_file($fullPath),
                'uploaded_by' => $userId ? (int) $userId : null,
            ];

            $mediaId = $this->mediaModel->insert($mediaData);

            return [
                'status'    => true,
                'media_id'  => $mediaId,
                'file_name' => $originalName,
                'file_path' => $dirPath . '/' . $safeName,
                'mime_type' => $mimeType,
            ];
        }

        return [
            'status' => false,
            'error'  => $file->getErrorString(),
        ];
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

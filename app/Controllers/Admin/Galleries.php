<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GalleryItemModel;
use App\Models\GalleryModel;

class Galleries extends BaseController
{
    protected GalleryModel $galleryModel;
    protected GalleryItemModel $galleryItemModel;

    public function __construct()
    {
        $this->galleryModel     = new GalleryModel();
        $this->galleryItemModel = new GalleryItemModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->galleryModel->onlyDeleted()->findAll();
        } else {
            $list = $this->galleryModel->findAll();
        }

        $data = [
            'title'       => 'Galeri Sekolah' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Galeri Sekolah' => 'admin/galleries', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/galleries/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug);

            $postData = [
                'title'       => $title,
                'slug'        => $slug,
                'type'        => $this->request->getPost('type'),
                'description' => sanitize_html($this->request->getPost('description')),
            ];

            if (! $this->galleryModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->galleryModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Cover Image Upload
            $file = $this->request->getFile('cover_image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['cover_image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar sampul: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            } else {
                $pickerImage = $this->request->getPost('cover_image');
                if (! empty($pickerImage)) {
                    $postData['cover_image'] = trim((string) $pickerImage);
                }
            }

            $insertId = $this->galleryModel->insert($postData);

            if ($insertId) {
                // Save SEO metadata
                $this->saveSeo('gallery_album_' . $insertId);

                $this->logActivity('Galeri', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Album galeri berhasil dibuat.');
                return redirect()->to(base_url('admin/galleries'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->galleryModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Album galeri tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $postData = [
                'id'          => $id,
                'title'       => $title,
                'slug'        => $slug,
                'type'        => $this->request->getPost('type'),
                'description' => sanitize_html($this->request->getPost('description')),
            ];

            if (! $this->galleryModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->galleryModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Cover Image Upload
            $file = $this->request->getFile('cover_image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['cover_image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar sampul: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            } else {
                $pickerImage = $this->request->getPost('cover_image');
                if (! empty($pickerImage)) {
                    $postData['cover_image'] = trim((string) $pickerImage);
                }
            }

            $this->galleryModel->update($id, $postData);

            // Save SEO metadata
            $this->saveSeo('gallery_album_' . $id);

            $this->logActivity('Galeri', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Album galeri berhasil diperbarui.');
            return redirect()->to(base_url('admin/galleries'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function items($galleryId)
    {
        $gallery = $this->galleryModel->find($galleryId);
        if (! $gallery) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Album galeri tidak ditemukan.');
        }

        $items = $this->galleryItemModel->getByGallery((int) $galleryId);

        $data = [
            'gallery'     => $gallery,
            'items'       => $items,
            'title'       => 'Kelola Item — ' . $gallery->title,
            'breadcrumbs' => ['Galeri Sekolah' => 'admin/galleries', $gallery->title => ''],
        ];

        return view('admin/galleries/items', $data);
    }

    public function add_item($galleryId)
    {
        $gallery = $this->galleryModel->find($galleryId);
        if (! $gallery) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Album galeri tidak ditemukan.');
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to(base_url('admin/galleries/items/' . $galleryId));
        }

        $caption = $this->request->getPost('caption');

        if ($gallery->type === 'video') {
            $videoUrl = trim((string) $this->request->getPost('file_path'));
            if (empty($videoUrl)) {
                session()->setFlashdata('error', 'URL video tidak boleh kosong.');
                return redirect()->to(base_url('admin/galleries/items/' . $galleryId));
            }

            $this->galleryItemModel->insert([
                'gallery_id' => $galleryId,
                'file_path'  => $videoUrl,
                'file_type'  => 'video_url',
                'caption'    => $caption,
            ]);
        } else {
            $file = $this->request->getFile('file_path');
            $filePath = '';

            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $filePath = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal unggah foto: ' . $uploadResult['error']);
                    return redirect()->to(base_url('admin/galleries/items/' . $galleryId));
                }
            } else {
                $filePath = trim((string) $this->request->getPost('file_path'));
            }

            if (empty($filePath)) {
                session()->setFlashdata('error', 'Pilih foto (upload baru atau dari Media Library) terlebih dahulu.');
                return redirect()->to(base_url('admin/galleries/items/' . $galleryId));
            }

            $this->galleryItemModel->insert([
                'gallery_id' => $galleryId,
                'file_path'  => $filePath,
                'file_type'  => 'image',
                'caption'    => $caption,
            ]);
        }

        $this->logActivity('Galeri', 'add_item', null, 'Item ditambahkan ke album #' . $galleryId);
        session()->setFlashdata('success', 'Item berhasil ditambahkan.');
        return redirect()->to(base_url('admin/galleries/items/' . $galleryId));
    }

    public function delete_item($itemId)
    {
        $item = $this->galleryItemModel->find($itemId);
        if (! $item) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Item galeri tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->galleryItemModel->softDeleteWithUser((int) $itemId, $userId ? (int) $userId : null);

        $this->logActivity('Galeri', 'delete_item', json_encode($item), null);
        session()->setFlashdata('success', 'Item berhasil dihapus.');
        return redirect()->to(base_url('admin/galleries/items/' . $item->gallery_id));
    }

    public function delete($id)
    {
        $row = $this->galleryModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Album galeri tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->galleryModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Galeri', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Album galeri berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/galleries'));
    }

    public function restore($id)
    {
        $row = $this->galleryModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Album galeri tidak ditemukan di tempat sampah.');
        }

        $this->galleryModel->restoreWithUser((int) $id);
        $this->logActivity('Galeri', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Album galeri berhasil dipulihkan.');
        return redirect()->to(base_url('admin/galleries?trash=1'));
    }

    public function force_delete($id)
    {
        $userId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Hanya Super Admin yang dapat menghapus data secara permanen.']);
            }
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat menghapus data secara permanen.']));
        }

        $row = $this->galleryModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Album galeri tidak ditemukan.');
        }

        $this->galleryModel->delete($id, true); // Hard delete
        $this->logActivity('Galeri', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Album galeri berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/galleries?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->galleryModel->builder()->where('slug', $slug);
            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }

            if ($builder->countAllResults() === 0) {
                break;
            }

            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Album Galeri Baru',
            'breadcrumbs' => ['Galeri Sekolah' => 'admin/galleries', 'Buat' => ''],
        ];

        return view('admin/galleries/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_metadata')->where('page_identifier', 'gallery_album_' . $id)->get()->getRow();

        $data = [
            'row'         => $row,
            'seo'         => $seo,
            'title'       => 'Edit Album Galeri',
            'breadcrumbs' => ['Galeri Sekolah' => 'admin/galleries', 'Edit' => ''],
        ];

        return view('admin/galleries/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/galleries/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/galleries/' . $safeName,
            ];
        }

        return [
            'status' => false,
            'error'  => $file->getErrorString(),
        ];
    }

    protected function saveSeo(string $pageIdentifier)
    {
        $metaTitle = $this->request->getPost('meta_title');
        $metaDesc  = $this->request->getPost('meta_description');
        $keywords  = $this->request->getPost('meta_keywords');

        if (! empty($metaTitle) || ! empty($metaDesc) || ! empty($keywords)) {
            $db = \Config\Database::connect();
            $existing = $db->table('seo_metadata')->where('page_identifier', $pageIdentifier)->get()->getRow();

            $seoData = [
                'page_identifier'  => $pageIdentifier,
                'meta_title'       => $metaTitle,
                'meta_description' => $metaDesc,
                'keywords'         => $keywords,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $db->table('seo_metadata')->where('id', $existing->id)->update($seoData);
            } else {
                $seoData['created_at'] = date('Y-m-d H:i:s');
                $db->table('seo_metadata')->insert($seoData);
            }
        }
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

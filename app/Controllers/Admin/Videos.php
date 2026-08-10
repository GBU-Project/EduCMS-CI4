<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VideoModel;

class Videos extends BaseController
{
    protected VideoModel $videoModel;

    public function __construct()
    {
        $this->videoModel = new VideoModel();
        helper(['video']);
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->videoModel->onlyDeleted()->findAll();
        } else {
            $list = $this->videoModel->findAll();
        }

        $data = [
            'title'       => 'Video Sekolah' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Video Sekolah' => 'admin/videos', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/videos/index', $data);
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
                'platform'    => $this->request->getPost('platform'),
                'video_url'   => $this->request->getPost('video_url'),
                'description' => sanitize_html($this->request->getPost('description')),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'order_num'   => (int) $this->request->getPost('order_num'),
                'status'      => $this->request->getPost('status'),
            ];

            if (! $this->videoModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->videoModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Thumbnail Upload
            $file = $this->request->getFile('thumbnail');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['thumbnail'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload thumbnail: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            }

            $insertId = $this->videoModel->insert($postData);

            if ($insertId) {
                // Save SEO metadata
                $this->saveSeo('videos_' . $insertId);

                $this->logActivity('Video', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Video berhasil dibuat.');
                return redirect()->to(base_url('admin/videos'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->videoModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Video tidak ditemukan.');
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
                'platform'    => $this->request->getPost('platform'),
                'video_url'   => $this->request->getPost('video_url'),
                'description' => sanitize_html($this->request->getPost('description')),
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
                'order_num'   => (int) $this->request->getPost('order_num'),
                'status'      => $this->request->getPost('status'),
            ];

            if (! $this->videoModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->videoModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Thumbnail Upload
            $file = $this->request->getFile('thumbnail');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['thumbnail'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload thumbnail: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            }

            $this->videoModel->update($id, $postData);

            // Save SEO metadata
            $this->saveSeo('videos_' . $id);

            $this->logActivity('Video', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Video berhasil diperbarui.');
            return redirect()->to(base_url('admin/videos'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->videoModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Video tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->videoModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Video', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Video berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/videos'));
    }

    public function restore($id)
    {
        $row = $this->videoModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Video tidak ditemukan di tempat sampah.');
        }

        $this->videoModel->restoreWithUser((int) $id);
        $this->logActivity('Video', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Video berhasil dipulihkan.');
        return redirect()->to(base_url('admin/videos?trash=1'));
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

        $row = $this->videoModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Video tidak ditemukan.');
        }

        $this->videoModel->delete($id, true); // Hard delete
        $this->logActivity('Video', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Video berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/videos?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->videoModel->builder()->where('slug', $slug);
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
            'title'       => 'Buat Video Baru',
            'breadcrumbs' => ['Video Sekolah' => 'admin/videos', 'Buat' => ''],
        ];

        return view('admin/videos/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_metadata')->where('page_identifier', 'videos_' . $id)->get()->getRow();

        $data = [
            'row'         => $row,
            'seo'         => $seo,
            'title'       => 'Edit Video',
            'breadcrumbs' => ['Video Sekolah' => 'admin/videos', 'Edit' => ''],
        ];

        return view('admin/videos/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/videos/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/videos/' . $safeName,
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

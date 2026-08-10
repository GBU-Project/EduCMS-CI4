<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AchievementModel;

class Achievements extends BaseController
{
    protected AchievementModel $achievementModel;

    public function __construct()
    {
        $this->achievementModel = new AchievementModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->achievementModel->onlyDeleted()->findAll();
        } else {
            $list = $this->achievementModel->findAll();
        }

        $data = [
            'title'       => 'Prestasi Sekolah' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Prestasi Sekolah' => 'admin/achievements', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/achievements/index', $data);
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
                'level'       => $this->request->getPost('level'),
                'date'        => $this->request->getPost('date'),
                'winner'      => $this->request->getPost('winner'),
                'description' => sanitize_html($this->request->getPost('description')),
                'status'      => $this->request->getPost('status'),
            ];

            if (! $this->achievementModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->achievementModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Image Upload
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            }

            $insertId = $this->achievementModel->insert($postData);

            if ($insertId) {
                // Save SEO metadata
                $this->saveSeo('achievements_' . $insertId);

                $this->logActivity('Prestasi', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Prestasi berhasil dibuat.');
                return redirect()->to(base_url('admin/achievements'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->achievementModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Prestasi tidak ditemukan.');
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
                'level'       => $this->request->getPost('level'),
                'date'        => $this->request->getPost('date'),
                'winner'      => $this->request->getPost('winner'),
                'description' => sanitize_html($this->request->getPost('description')),
                'status'      => $this->request->getPost('status'),
            ];

            if (! $this->achievementModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->achievementModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Image Upload
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            }

            $this->achievementModel->update($id, $postData);

            // Save SEO metadata
            $this->saveSeo('achievements_' . $id);

            $this->logActivity('Prestasi', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Prestasi berhasil diperbarui.');
            return redirect()->to(base_url('admin/achievements'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->achievementModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Prestasi tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->achievementModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Prestasi', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Prestasi berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/achievements'));
    }

    public function restore($id)
    {
        $row = $this->achievementModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Prestasi tidak ditemukan di tempat sampah.');
        }

        $this->achievementModel->restoreWithUser((int) $id);
        $this->logActivity('Prestasi', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Prestasi berhasil dipulihkan.');
        return redirect()->to(base_url('admin/achievements?trash=1'));
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

        $row = $this->achievementModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Prestasi tidak ditemukan.');
        }

        $this->achievementModel->delete($id, true); // Hard delete
        $this->logActivity('Prestasi', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Prestasi berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/achievements?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->achievementModel->builder()->where('slug', $slug);
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
            'title'       => 'Buat Prestasi Baru',
            'breadcrumbs' => ['Prestasi Sekolah' => 'admin/achievements', 'Buat' => ''],
        ];

        return view('admin/achievements/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_metadata')->where('page_identifier', 'achievements_' . $id)->get()->getRow();

        $data = [
            'row'         => $row,
            'seo'         => $seo,
            'title'       => 'Edit Prestasi',
            'breadcrumbs' => ['Prestasi Sekolah' => 'admin/achievements', 'Edit' => ''],
        ];

        return view('admin/achievements/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/achievements/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/achievements/' . $safeName,
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

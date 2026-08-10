<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExtracurricularModel;

class Extracurriculars extends BaseController
{
    protected ExtracurricularModel $extracurricularModel;

    public function __construct()
    {
        $this->extracurricularModel = new ExtracurricularModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->extracurricularModel->onlyDeleted()->findAll();
        } else {
            $list = $this->extracurricularModel->findAll();
        }

        $data = [
            'title'       => 'Ekstrakurikuler' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Ekstrakurikuler' => 'admin/extracurriculars', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/extracurriculars/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $name = $this->request->getPost('name');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($name);
            $slug = $this->generateUniqueSlug($slug);

            $postData = [
                'name'        => $name,
                'slug'        => $slug,
                'description' => sanitize_html($this->request->getPost('description')),
                'coach'       => $this->request->getPost('coach'),
                'schedule'    => $this->request->getPost('schedule'),
            ];

            if (! $this->extracurricularModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->extracurricularModel->errors()));
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

            $insertId = $this->extracurricularModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Ekstrakurikuler', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Ekstrakurikuler berhasil dibuat.');
                return redirect()->to(base_url('admin/extracurriculars'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->extracurricularModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ekstrakurikuler tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $name = $this->request->getPost('name');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($name);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $postData = [
                'id'          => $id,
                'name'        => $name,
                'slug'        => $slug,
                'description' => sanitize_html($this->request->getPost('description')),
                'coach'       => $this->request->getPost('coach'),
                'schedule'    => $this->request->getPost('schedule'),
            ];

            if (! $this->extracurricularModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->extracurricularModel->errors()));
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

            $this->extracurricularModel->update($id, $postData);

            $this->logActivity('Ekstrakurikuler', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Ekstrakurikuler berhasil diperbarui.');
            return redirect()->to(base_url('admin/extracurriculars'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->extracurricularModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ekstrakurikuler tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->extracurricularModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Ekstrakurikuler', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Ekstrakurikuler berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/extracurriculars'));
    }

    public function restore($id)
    {
        $row = $this->extracurricularModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ekstrakurikuler tidak ditemukan di tempat sampah.');
        }

        $this->extracurricularModel->restoreWithUser((int) $id);
        $this->logActivity('Ekstrakurikuler', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Ekstrakurikuler berhasil dipulihkan.');
        return redirect()->to(base_url('admin/extracurriculars?trash=1'));
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

        $row = $this->extracurricularModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Ekstrakurikuler tidak ditemukan.');
        }

        $this->extracurricularModel->delete($id, true); // Hard delete
        $this->logActivity('Ekstrakurikuler', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Ekstrakurikuler berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/extracurriculars?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->extracurricularModel->builder()->where('slug', $slug);
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
            'title'       => 'Buat Ekstrakurikuler Baru',
            'breadcrumbs' => ['Ekstrakurikuler' => 'admin/extracurriculars', 'Buat' => ''],
        ];

        return view('admin/extracurriculars/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Ekstrakurikuler',
            'breadcrumbs' => ['Ekstrakurikuler' => 'admin/extracurriculars', 'Edit' => ''],
        ];

        return view('admin/extracurriculars/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/extracurriculars/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/extracurriculars/' . $safeName,
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

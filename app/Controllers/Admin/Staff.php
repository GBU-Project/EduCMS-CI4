<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StaffModel;

class Staff extends BaseController
{
    protected StaffModel $staffModel;

    public function __construct()
    {
        $this->staffModel = new StaffModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->staffModel->onlyDeleted()->findAll();
        } else {
            $list = $this->staffModel->findAll();
        }

        $data = [
            'title'       => 'Direktori Staf' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Direktori Staf' => 'admin/staff', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/staff/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $nik = $this->request->getPost('nik');

            $postData = [
                'nik'      => (empty($nik) || trim($nik) === '') ? null : trim($nik),
                'name'     => $this->request->getPost('name'),
                'gender'   => $this->request->getPost('gender'),
                'position' => $this->request->getPost('position'),
                'email'    => $this->request->getPost('email'),
                'phone'    => $this->request->getPost('phone'),
                'address'  => $this->request->getPost('address'),
                'status'   => $this->request->getPost('status'),
            ];

            if (! $this->staffModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->staffModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Photo Upload
            $file = $this->request->getFile('photo');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['photo'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload foto: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            }

            $insertId = $this->staffModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Staf', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Staf berhasil dibuat.');
                return redirect()->to(base_url('admin/staff'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->staffModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data staf tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $nik = $this->request->getPost('nik');

            $postData = [
                'id'       => $id,
                'nik'      => (empty($nik) || trim($nik) === '') ? null : trim($nik),
                'name'     => $this->request->getPost('name'),
                'gender'   => $this->request->getPost('gender'),
                'position' => $this->request->getPost('position'),
                'email'    => $this->request->getPost('email'),
                'phone'    => $this->request->getPost('phone'),
                'address'  => $this->request->getPost('address'),
                'status'   => $this->request->getPost('status'),
            ];

            if (! $this->staffModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->staffModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Photo Upload
            $file = $this->request->getFile('photo');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['photo'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload foto: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            }

            $this->staffModel->update($id, $postData);

            $this->logActivity('Staf', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Staf berhasil diperbarui.');
            return redirect()->to(base_url('admin/staff'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->staffModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data staf tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->staffModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Staf', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Staf berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/staff'));
    }

    public function restore($id)
    {
        $row = $this->staffModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data staf tidak ditemukan di tempat sampah.');
        }

        $this->staffModel->restoreWithUser((int) $id);
        $this->logActivity('Staf', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Staf berhasil dipulihkan.');
        return redirect()->to(base_url('admin/staff?trash=1'));
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

        $row = $this->staffModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data staf tidak ditemukan.');
        }

        $this->staffModel->delete($id, true); // Hard delete
        $this->logActivity('Staf', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Staf berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/staff?trash=1'));
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Staf Baru',
            'breadcrumbs' => ['Direktori Staf' => 'admin/staff', 'Buat' => ''],
        ];

        return view('admin/staff/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Staf',
            'breadcrumbs' => ['Direktori Staf' => 'admin/staff', 'Edit' => ''],
        ];

        return view('admin/staff/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/staff/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/staff/' . $safeName,
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

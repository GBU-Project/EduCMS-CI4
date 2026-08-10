<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TeacherModel;

class Teachers extends BaseController
{
    protected TeacherModel $teacherModel;

    public function __construct()
    {
        $this->teacherModel = new TeacherModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->teacherModel->onlyDeleted()->findAll();
        } else {
            $list = $this->teacherModel->findAll();
        }

        $data = [
            'title'       => 'Direktori Guru' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Direktori Guru' => 'admin/teachers', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/teachers/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $nip = $this->request->getPost('nip');
            $dob = $this->request->getPost('date_of_birth');

            $postData = [
                'nip'            => (empty($nip) || trim($nip) === '') ? null : trim($nip),
                'name'           => $this->request->getPost('name'),
                'gender'         => $this->request->getPost('gender'),
                'place_of_birth' => $this->request->getPost('place_of_birth'),
                'date_of_birth'  => ! empty($dob) ? $dob : null,
                'position'       => $this->request->getPost('position'),
                'email'          => $this->request->getPost('email'),
                'phone'          => $this->request->getPost('phone'),
                'address'        => $this->request->getPost('address'),
                'status'         => $this->request->getPost('status'),
            ];

            if (! $this->teacherModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->teacherModel->errors()));
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

            $insertId = $this->teacherModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Guru', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Guru berhasil dibuat.');
                return redirect()->to(base_url('admin/teachers'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->teacherModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data guru tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $nip = $this->request->getPost('nip');
            $dob = $this->request->getPost('date_of_birth');

            $postData = [
                'id'             => $id,
                'nip'            => (empty($nip) || trim($nip) === '') ? null : trim($nip),
                'name'           => $this->request->getPost('name'),
                'gender'         => $this->request->getPost('gender'),
                'place_of_birth' => $this->request->getPost('place_of_birth'),
                'date_of_birth'  => ! empty($dob) ? $dob : null,
                'position'       => $this->request->getPost('position'),
                'email'          => $this->request->getPost('email'),
                'phone'          => $this->request->getPost('phone'),
                'address'        => $this->request->getPost('address'),
                'status'         => $this->request->getPost('status'),
            ];

            if (! $this->teacherModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->teacherModel->errors()));
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

            $this->teacherModel->update($id, $postData);

            $this->logActivity('Guru', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Guru berhasil diperbarui.');
            return redirect()->to(base_url('admin/teachers'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->teacherModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data guru tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->teacherModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Guru', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Guru berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/teachers'));
    }

    public function restore($id)
    {
        $row = $this->teacherModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data guru tidak ditemukan di tempat sampah.');
        }

        $this->teacherModel->restoreWithUser((int) $id);
        $this->logActivity('Guru', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Guru berhasil dipulihkan.');
        return redirect()->to(base_url('admin/teachers?trash=1'));
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

        $row = $this->teacherModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data guru tidak ditemukan.');
        }

        $this->teacherModel->delete($id, true); // Hard delete
        $this->logActivity('Guru', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Guru berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/teachers?trash=1'));
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Guru Baru',
            'breadcrumbs' => ['Direktori Guru' => 'admin/teachers', 'Buat' => ''],
        ];

        return view('admin/teachers/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Guru',
            'breadcrumbs' => ['Direktori Guru' => 'admin/teachers', 'Edit' => ''],
        ];

        return view('admin/teachers/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/teachers/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/teachers/' . $safeName,
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

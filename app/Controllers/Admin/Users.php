<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->userModel->onlyDeleted()->findAll();
        } else {
            $list = $this->userModel->getUsersWithRoles();
        }

        $data = [
            'title'       => 'Kelola Pengguna' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Kelola Pengguna' => 'admin/users', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/users/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $fullName = $this->request->getPost('full_name');
            $username = $this->request->getPost('username') ?: strtolower(str_replace(' ', '.', (string) $fullName));
            $password = $this->request->getPost('password');

            $postData = [
                'full_name' => $fullName,
                'email'     => $this->request->getPost('email'),
                'username'  => $username,
                'status'    => $this->request->getPost('status') ?: 'active',
            ];

            if (empty($password) || strlen($password) < 6) {
                session()->setFlashdata('error', 'Password minimal 6 karakter.');
                return $this->_renderCreateView();
            }

            $postData['password'] = password_hash($password, PASSWORD_DEFAULT);

            if (! $this->userModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->userModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->userModel->insert($postData);

            if ($insertId) {
                $roleIds = $this->request->getPost('role_ids');
                if (! empty($roleIds) && is_array($roleIds)) {
                    $this->userModel->saveUserRoles((int) $insertId, $roleIds);
                }

                $this->logActivity('Users', 'create', null, json_encode(['id' => $insertId, 'email' => $postData['email']]));
                session()->setFlashdata('success', 'Pengguna berhasil dibuat.');
                return redirect()->to(base_url('admin/users'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->userModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'id'        => $id,
                'full_name' => $this->request->getPost('full_name'),
                'email'     => $this->request->getPost('email'),
                'username'  => $row->username, // Retain original username
                'status'    => $this->request->getPost('status') ?: 'active',
            ];

            $password = $this->request->getPost('password');
            if (! empty($password)) {
                if (strlen($password) < 6) {
                    session()->setFlashdata('error', 'Password minimal 6 karakter.');
                    return $this->_renderEditView($id, $row);
                }
                $postData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if (! $this->userModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->userModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->userModel->update($id, $postData);

            $roleIds = $this->request->getPost('role_ids');
            $this->userModel->saveUserRoles((int) $id, is_array($roleIds) ? $roleIds : []);

            $this->logActivity('Users', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Pengguna berhasil diperbarui.');
            return redirect()->to(base_url('admin/users'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $currentUserId = session()->get('user_id');

        if ((int) $id === (int) $currentUserId) {
            session()->setFlashdata('error', 'Anda tidak dapat menghapus akun sendiri.');
            return redirect()->to(base_url('admin/users'));
        }

        $row = $this->userModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }

        $this->userModel->softDeleteWithUser((int) $id, $currentUserId ? (int) $currentUserId : null);

        $this->logActivity('Users', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Pengguna berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/users'));
    }

    public function restore($id)
    {
        $row = $this->userModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan di tempat sampah.');
        }

        $this->userModel->restoreWithUser((int) $id);
        $this->logActivity('Users', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Pengguna berhasil dipulihkan.');
        return redirect()->to(base_url('admin/users?trash=1'));
    }

    public function force_delete($id)
    {
        $currentUserId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $currentUserId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Hanya Super Admin yang dapat menghapus data secara permanen.']);
            }
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat menghapus data secara permanen.']));
        }

        if ((int) $id === (int) $currentUserId) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Anda tidak dapat menghapus akun sendiri secara permanen.']);
            }
            session()->setFlashdata('error', 'Anda tidak dapat menghapus akun sendiri secara permanen.');
            return redirect()->to(base_url('admin/users?trash=1'));
        }

        $row = $this->userModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }

        $this->userModel->delete($id, true); // Hard delete
        $this->logActivity('Users', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Pengguna berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/users?trash=1'));
    }

    protected function _renderCreateView()
    {
        $db    = \Config\Database::connect();
        $roles = $db->tableExists('roles') ? $this->roleModel->findAll() : [];

        $data = [
            'roles'       => $roles,
            'title'       => 'Tambah Pengguna Baru',
            'breadcrumbs' => ['Kelola Pengguna' => 'admin/users', 'Tambah' => ''],
        ];

        return view('admin/users/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $userRoleIds = $this->userModel->getUserRoleIds((int) $id);
        $db          = \Config\Database::connect();
        $roles       = $db->tableExists('roles') ? $this->roleModel->findAll() : [];

        $data = [
            'row'           => $row,
            'user_role_ids' => $userRoleIds,
            'roles'         => $roles,
            'title'         => 'Edit Pengguna',
            'breadcrumbs'   => ['Kelola Pengguna' => 'admin/users', 'Edit' => ''],
        ];

        return view('admin/users/edit', $data);
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

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class Roles extends BaseController
{
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->roleModel->onlyDeleted()->findAll();
        } else {
            $list = $this->roleModel->getRolesWithCounts();
        }

        $data = [
            'title'       => 'Hak Akses (RBAC)' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Hak Akses (RBAC)' => 'admin/roles', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/roles/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $postData = [
                'name'        => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
            ];

            if (! $this->roleModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->roleModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->roleModel->insert($postData);

            if ($insertId) {
                $permIds = $this->request->getPost('permission_ids');
                if (! empty($permIds) && is_array($permIds)) {
                    $this->roleModel->saveRolePermissions((int) $insertId, $permIds);
                }

                $this->logActivity('Roles', 'create', null, json_encode(['id' => $insertId, 'name' => $postData['name']]));
                session()->setFlashdata('success', 'Role berhasil dibuat.');
                return redirect()->to(base_url('admin/roles'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->roleModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Role tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'id'          => $id,
                'name'        => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
            ];

            if (! $this->roleModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->roleModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->roleModel->update($id, $postData);

            $permIds = $this->request->getPost('permission_ids');
            $this->roleModel->saveRolePermissions((int) $id, is_array($permIds) ? $permIds : []);

            $this->logActivity('Roles', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Role berhasil diperbarui.');
            return redirect()->to(base_url('admin/roles'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->roleModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Role tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->roleModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Roles', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Role berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/roles'));
    }

    public function restore($id)
    {
        $row = $this->roleModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Role tidak ditemukan di tempat sampah.');
        }

        $this->roleModel->restoreWithUser((int) $id);
        $this->logActivity('Roles', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Role berhasil dipulihkan.');
        return redirect()->to(base_url('admin/roles?trash=1'));
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

        $row = $this->roleModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Role tidak ditemukan.');
        }

        $this->roleModel->delete($id, true); // Hard delete
        $this->logActivity('Roles', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Role berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/roles?trash=1'));
    }

    protected function _renderCreateView()
    {
        $db          = \Config\Database::connect();
        $permissions = $db->tableExists('permissions') ?
            $db->table('permissions')->where('deleted_at', null)->get()->getResult() : [];

        $data = [
            'permissions' => $permissions,
            'title'       => 'Tambah Role Baru',
            'breadcrumbs' => ['Hak Akses (RBAC)' => 'admin/roles', 'Tambah' => ''],
        ];

        return view('admin/roles/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $rolePermIds = $this->roleModel->getRolePermissionIds((int) $id);
        $db          = \Config\Database::connect();
        $permissions = $db->tableExists('permissions') ?
            $db->table('permissions')->where('deleted_at', null)->get()->getResult() : [];

        $data = [
            'row'           => $row,
            'permissions'   => $permissions,
            'role_perm_ids' => $rolePermIds,
            'title'         => 'Edit Role',
            'breadcrumbs'   => ['Hak Akses (RBAC)' => 'admin/roles', 'Edit' => ''],
        ];

        return view('admin/roles/edit', $data);
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

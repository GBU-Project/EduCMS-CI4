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
        if (strtolower($this->request->getMethod()) === 'post') {
            $postData = [
                'name'        => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
            ];

            $secNameError = $this->checkRoleNameSecurity(null, (string) $postData['name']);
            if ($secNameError) {
                return $secNameError;
            }

            $permIds = $this->request->getPost('permission_ids');
            $permIdsArray = is_array($permIds) ? $permIds : [];
            $secError = $this->checkPermissionAssignmentSecurity($permIdsArray);
            if ($secError) {
                return $secError;
            }

            if (! $this->roleModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->roleModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->roleModel->insert($postData);

            if ($insertId) {
                if (! empty($permIdsArray)) {
                    $this->roleModel->saveRolePermissions((int) $insertId, $permIdsArray);
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

        if (strtolower($this->request->getMethod()) === 'post') {
            $postData = [
                'id'          => $id,
                'name'        => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
            ];

            $secNameError = $this->checkRoleNameSecurity((string) $row->name, (string) $postData['name']);
            if ($secNameError) {
                return $secNameError;
            }

            if (! $this->roleModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->roleModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $permIds = $this->request->getPost('permission_ids');
            $permIdsArray = is_array($permIds) ? $permIds : [];
            $secError = $this->checkPermissionAssignmentSecurity($permIdsArray);
            if ($secError) {
                return $secError;
            }

            $this->roleModel->update($id, $postData);
            $this->roleModel->saveRolePermissions((int) $id, $permIdsArray);

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

    protected function checkPermissionAssignmentSecurity(array $permIds)
    {
        $actorId = (int) session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');
        $isActorSuperAdmin = $rbac->is_super_admin($actorId);

        if ($isActorSuperAdmin || empty($permIds)) {
            return null;
        }

        $actorPermissions = $rbac->get_user_permissions($actorId);
        $db = \Config\Database::connect();
        if ($db->tableExists('permissions')) {
            $requestedPerms = $db->table('permissions')
                ->whereIn('id', array_map('intval', $permIds))
                ->get()
                ->getResult();

            foreach ($requestedPerms as $perm) {
                if (! in_array($perm->name, $actorPermissions, true)) {
                    $this->logActivity('Roles', 'permission_escalation_denied', null, json_encode([
                        'actor_id' => $actorId,
                        'unauthorized_perm' => $perm->name,
                    ]));
                    if ($this->request->isAJAX()) {
                        return $this->response->setStatusCode(403)->setJSON(['error' => 'Anda hanya dapat memberikan permission yang Anda miliki.']);
                    }
                    return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Anda hanya dapat memberikan permission yang Anda miliki.']));
                }
            }
        }

        return null;
    }

    protected function checkRoleNameSecurity(?string $originalRoleName, string $newRoleName)
    {
        $actorId = (int) session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');
        $isActorSuperAdmin = $rbac->is_super_admin($actorId);

        if ($isActorSuperAdmin) {
            return null;
        }

        $protectedNames = ['Super Admin', 'admin'];

        // 1. Block non-Super-Admin from editing a role currently named Super Admin or admin
        if ($originalRoleName !== null && in_array($originalRoleName, $protectedNames, true)) {
            $this->logActivity('Roles', 'role_rename_denied', null, json_encode([
                'actor_id' => $actorId,
                'original_name' => $originalRoleName,
            ]));
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Tidak boleh mengubah role Super Admin.']);
            }
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Tidak boleh mengubah role Super Admin.']));
        }

        // 2. Block non-Super-Admin from renaming ANY role into Super Admin or admin
        if (in_array($newRoleName, $protectedNames, true)) {
            $this->logActivity('Roles', 'role_rename_denied', null, json_encode([
                'actor_id' => $actorId,
                'attempted_name' => $newRoleName,
            ]));
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Nama role tidak diperbolehkan.']);
            }
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Nama role tidak diperbolehkan.']));
        }

        return null;
    }
}

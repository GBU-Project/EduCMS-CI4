<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuGroupModel;

class Menu_groups extends BaseController
{
    protected MenuGroupModel $menuGroupModel;

    public function __construct()
    {
        $this->menuGroupModel = new MenuGroupModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        $list = $this->menuGroupModel->getGroupsWithItemCount($showTrash);

        $data = [
            'title'       => 'Menu Builder' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Menu Builder' => 'admin/menu-groups', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/menu-groups/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            helper('educms');
            $name = $this->request->getPost('name');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($name);
            $slug = $this->generateUniqueSlug($slug);

            $postData = [
                'name'        => $name,
                'slug'        => $slug,
                'description' => $this->request->getPost('description'),
            ];

            if (! $this->menuGroupModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->menuGroupModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->menuGroupModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Menu', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Menu berhasil dibuat.');
                return redirect()->to(base_url('admin/menu-groups'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->menuGroupModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Menu tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper('educms');
            $name = $this->request->getPost('name');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($name);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $postData = [
                'id'          => $id,
                'name'        => $name,
                'slug'        => $slug,
                'description' => $this->request->getPost('description'),
            ];

            if (! $this->menuGroupModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->menuGroupModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->menuGroupModel->update($id, $postData);

            $this->logActivity('Menu', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Menu berhasil diperbarui.');
            return redirect()->to(base_url('admin/menu-groups'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->menuGroupModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Menu tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->menuGroupModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Menu', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Menu berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/menu-groups'));
    }

    public function restore($id)
    {
        $row = $this->menuGroupModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Menu tidak ditemukan di tempat sampah.');
        }

        $this->menuGroupModel->restoreWithUser((int) $id);
        $this->logActivity('Menu', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Menu berhasil dipulihkan.');
        return redirect()->to(base_url('admin/menu-groups?trash=1'));
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

        $row = $this->menuGroupModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Menu tidak ditemukan.');
        }

        $this->menuGroupModel->delete($id, true); // Hard delete
        $this->logActivity('Menu', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Menu berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/menu-groups?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->menuGroupModel->builder()->where('slug', $slug);
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
            'title'       => 'Buat Menu Baru',
            'breadcrumbs' => ['Menu Builder' => 'admin/menu-groups', 'Buat' => ''],
        ];

        return view('admin/menu-groups/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Menu',
            'breadcrumbs' => ['Menu Builder' => 'admin/menu-groups', 'Edit' => ''],
        ];

        return view('admin/menu-groups/edit', $data);
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

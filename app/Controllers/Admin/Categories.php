<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->categoryModel->onlyDeleted()->findAll();
        } else {
            $list = $this->categoryModel->findAll();
        }

        $data = [
            'title'       => 'Kategori Berita' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Kategori Berita' => 'admin/categories', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/categories/index', $data);
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

            if (! $this->categoryModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->categoryModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->categoryModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Kategori', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Kategori berhasil dibuat.');
                return redirect()->to(base_url('admin/categories'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->categoryModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
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

            if (! $this->categoryModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->categoryModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->categoryModel->update($id, $postData);

            $this->logActivity('Kategori', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Kategori berhasil diperbarui.');
            return redirect()->to(base_url('admin/categories'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->categoryModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->categoryModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Kategori', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Kategori berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/categories'));
    }

    public function restore($id)
    {
        $row = $this->categoryModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan di tempat sampah.');
        }

        $this->categoryModel->restoreWithUser((int) $id);
        $this->logActivity('Kategori', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Kategori berhasil dipulihkan.');
        return redirect()->to(base_url('admin/categories?trash=1'));
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

        $row = $this->categoryModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori tidak ditemukan.');
        }

        $this->categoryModel->delete($id, true); // Hard delete
        $this->logActivity('Kategori', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Kategori berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/categories?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->categoryModel->builder()->where('slug', $slug);
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
            'title'       => 'Buat Kategori Baru',
            'breadcrumbs' => ['Kategori Berita' => 'admin/categories', 'Buat' => ''],
        ];

        return view('admin/categories/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Kategori',
            'breadcrumbs' => ['Kategori Berita' => 'admin/categories', 'Edit' => ''],
        ];

        return view('admin/categories/edit', $data);
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

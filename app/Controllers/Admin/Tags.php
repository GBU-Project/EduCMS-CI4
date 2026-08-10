<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TagModel;

class Tags extends BaseController
{
    protected TagModel $tagModel;

    public function __construct()
    {
        $this->tagModel = new TagModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->tagModel->onlyDeleted()->findAll();
        } else {
            $list = $this->tagModel->findAll();
        }

        $data = [
            'title'       => 'Tag Berita' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Tag Berita' => 'admin/tags', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/tags/index', $data);
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
                'name' => $name,
                'slug' => $slug,
            ];

            if (! $this->tagModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->tagModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->tagModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Tag', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Tag berhasil dibuat.');
                return redirect()->to(base_url('admin/tags'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->tagModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper('educms');
            $name = $this->request->getPost('name');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($name);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $postData = [
                'id'   => $id,
                'name' => $name,
                'slug' => $slug,
            ];

            if (! $this->tagModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->tagModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->tagModel->update($id, $postData);

            $this->logActivity('Tag', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Tag berhasil diperbarui.');
            return redirect()->to(base_url('admin/tags'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->tagModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->tagModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Tag', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Tag berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/tags'));
    }

    public function restore($id)
    {
        $row = $this->tagModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag tidak ditemukan di tempat sampah.');
        }

        $this->tagModel->restoreWithUser((int) $id);
        $this->logActivity('Tag', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Tag berhasil dipulihkan.');
        return redirect()->to(base_url('admin/tags?trash=1'));
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

        $row = $this->tagModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Tag tidak ditemukan.');
        }

        $this->tagModel->delete($id, true); // Hard delete
        $this->logActivity('Tag', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Tag berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/tags?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->tagModel->builder()->where('slug', $slug);
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
            'title'       => 'Buat Tag Baru',
            'breadcrumbs' => ['Tag Berita' => 'admin/tags', 'Buat' => ''],
        ];

        return view('admin/tags/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Tag',
            'breadcrumbs' => ['Tag Berita' => 'admin/tags', 'Edit' => ''],
        ];

        return view('admin/tags/edit', $data);
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

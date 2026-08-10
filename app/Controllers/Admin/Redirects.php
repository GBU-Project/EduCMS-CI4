<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RedirectModel;

class Redirects extends BaseController
{
    protected RedirectModel $redirectModel;

    public function __construct()
    {
        $this->redirectModel = new RedirectModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->redirectModel->onlyDeleted()->findAll();
        } else {
            $list = $this->redirectModel->findAll();
        }

        $data = [
            'title'       => 'URL Redirects' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['URL Redirects' => 'admin/redirects', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/redirects/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $postData = [
                'source_url'  => $this->request->getPost('source_url'),
                'target_url'  => $this->request->getPost('target_url'),
                'status_code' => $this->request->getPost('status_code'),
            ];

            if (! $this->redirectModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->redirectModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->redirectModel->insert($postData);

            if ($insertId) {
                // Log activity
                $this->logActivity('Redirect', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Redirect berhasil dibuat.');
                return redirect()->to(base_url('admin/redirects'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->redirectModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Redirect tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'id'          => $id,
                'source_url'  => $this->request->getPost('source_url'),
                'target_url'  => $this->request->getPost('target_url'),
                'status_code' => $this->request->getPost('status_code'),
            ];

            if (! $this->redirectModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->redirectModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->redirectModel->update($id, $postData);

            $this->logActivity('Redirect', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Redirect berhasil diperbarui.');
            return redirect()->to(base_url('admin/redirects'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->redirectModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Redirect tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->redirectModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Redirect', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Redirect berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/redirects'));
    }

    public function restore($id)
    {
        $row = $this->redirectModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Redirect tidak ditemukan di tempat sampah.');
        }

        $this->redirectModel->restoreWithUser((int) $id);
        $this->logActivity('Redirect', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Redirect berhasil dipulihkan.');
        return redirect()->to(base_url('admin/redirects?trash=1'));
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

        $row = $this->redirectModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Redirect tidak ditemukan.');
        }

        $this->redirectModel->delete($id, true); // Hard delete
        $this->logActivity('Redirect', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Redirect berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/redirects?trash=1'));
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Redirect Baru',
            'breadcrumbs' => ['URL Redirects' => 'admin/redirects', 'Buat' => ''],
        ];

        return view('admin/redirects/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Redirect',
            'breadcrumbs' => ['URL Redirects' => 'admin/redirects', 'Edit' => ''],
        ];

        return view('admin/redirects/edit', $data);
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

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PartnerModel;

class Partners extends BaseController
{
    protected PartnerModel $partnerModel;

    public function __construct()
    {
        $this->partnerModel = new PartnerModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->partnerModel->onlyDeleted()->findAll();
        } else {
            $list = $this->partnerModel->findAll();
        }

        $data = [
            'title'       => 'Mitra Sekolah' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Mitra Sekolah' => 'admin/partners', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/partners/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $postData = [
                'name' => $this->request->getPost('name'),
                'link' => $this->request->getPost('link'),
            ];

            if (! $this->partnerModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->partnerModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Logo Upload
            $file = $this->request->getFile('logo');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['logo'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload logo: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            } else {
                $postData['logo'] = '';
            }

            $insertId = $this->partnerModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Mitra', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Mitra berhasil dibuat.');
                return redirect()->to(base_url('admin/partners'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->partnerModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Mitra tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'id'   => $id,
                'name' => $this->request->getPost('name'),
                'link' => $this->request->getPost('link'),
            ];

            if (! $this->partnerModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->partnerModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Logo Upload
            $file = $this->request->getFile('logo');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['logo'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload logo: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            }

            $this->partnerModel->update($id, $postData);

            $this->logActivity('Mitra', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Mitra berhasil diperbarui.');
            return redirect()->to(base_url('admin/partners'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->partnerModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Mitra tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->partnerModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Mitra', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Mitra berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/partners'));
    }

    public function restore($id)
    {
        $row = $this->partnerModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Mitra tidak ditemukan di tempat sampah.');
        }

        $this->partnerModel->restoreWithUser((int) $id);
        $this->logActivity('Mitra', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Mitra berhasil dipulihkan.');
        return redirect()->to(base_url('admin/partners?trash=1'));
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

        $row = $this->partnerModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Mitra tidak ditemukan.');
        }

        $this->partnerModel->delete($id, true); // Hard delete
        $this->logActivity('Mitra', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Mitra berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/partners?trash=1'));
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Mitra Baru',
            'breadcrumbs' => ['Mitra Sekolah' => 'admin/partners', 'Buat' => ''],
        ];

        return view('admin/partners/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Mitra',
            'breadcrumbs' => ['Mitra Sekolah' => 'admin/partners', 'Edit' => ''],
        ];

        return view('admin/partners/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/partners/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/partners/' . $safeName,
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

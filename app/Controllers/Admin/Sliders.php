<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SliderModel;

class Sliders extends BaseController
{
    protected SliderModel $sliderModel;

    public function __construct()
    {
        $this->sliderModel = new SliderModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->sliderModel->onlyDeleted()->findAll();
        } else {
            $list = $this->sliderModel->findAll();
        }

        $data = [
            'title'       => 'Slider Beranda' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Slider Beranda' => 'admin/sliders', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/sliders/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $postData = [
                'title'     => $this->request->getPost('title'),
                'subtitle'  => $this->request->getPost('subtitle'),
                'link'      => $this->request->getPost('link'),
                'order_num' => (int) $this->request->getPost('order_num'),
                'status'    => $this->request->getPost('status'),
            ];

            if (! $this->sliderModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->sliderModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Image Upload
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar slider: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            } else {
                $pickerImage = $this->request->getPost('image');
                if (! empty($pickerImage)) {
                    $postData['image'] = trim((string) $pickerImage);
                }
            }

            if (empty($postData['image'])) {
                session()->setFlashdata('error', 'Gambar slider wajib diisi.');
                return $this->_renderCreateView();
            }

            $insertId = $this->sliderModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Slider', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Slider berhasil dibuat.');
                return redirect()->to(base_url('admin/sliders'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->sliderModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Slider tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'id'        => $id,
                'title'     => $this->request->getPost('title'),
                'subtitle'  => $this->request->getPost('subtitle'),
                'link'      => $this->request->getPost('link'),
                'order_num' => (int) $this->request->getPost('order_num'),
                'status'    => $this->request->getPost('status'),
            ];

            if (! $this->sliderModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->sliderModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Image Upload
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar slider: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            } else {
                $pickerImage = $this->request->getPost('image');
                if (! empty($pickerImage)) {
                    $postData['image'] = trim((string) $pickerImage);
                }
            }

            $this->sliderModel->update($id, $postData);

            $this->logActivity('Slider', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Slider berhasil diperbarui.');
            return redirect()->to(base_url('admin/sliders'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->sliderModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Slider tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->sliderModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Slider', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Slider berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/sliders'));
    }

    public function restore($id)
    {
        $row = $this->sliderModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Slider tidak ditemukan di tempat sampah.');
        }

        $this->sliderModel->restoreWithUser((int) $id);
        $this->logActivity('Slider', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Slider berhasil dipulihkan.');
        return redirect()->to(base_url('admin/sliders?trash=1'));
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

        $row = $this->sliderModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Slider tidak ditemukan.');
        }

        $this->sliderModel->delete($id, true); // Hard delete
        $this->logActivity('Slider', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Slider berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/sliders?trash=1'));
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Slider Baru',
            'breadcrumbs' => ['Slider Beranda' => 'admin/sliders', 'Buat' => ''],
        ];

        return view('admin/sliders/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Slider',
            'breadcrumbs' => ['Slider Beranda' => 'admin/sliders', 'Edit' => ''],
        ];

        return view('admin/sliders/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/sliders/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/sliders/' . $safeName,
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

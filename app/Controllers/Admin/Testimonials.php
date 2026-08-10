<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TestimonialModel;

class Testimonials extends BaseController
{
    protected TestimonialModel $testimonialModel;

    public function __construct()
    {
        $this->testimonialModel = new TestimonialModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->testimonialModel->onlyDeleted()->findAll();
        } else {
            $list = $this->testimonialModel->findAll();
        }

        $data = [
            'title'       => 'Testimoni' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Testimoni' => 'admin/testimonials', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/testimonials/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $postData = [
                'name'      => $this->request->getPost('name'),
                'role'      => $this->request->getPost('role'),
                'content'   => $this->request->getPost('testimonial_message'),
                'rating'    => $this->request->getPost('rating'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if (! $this->testimonialModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->testimonialModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Avatar Upload if present
            $file = $this->request->getFile('avatar');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['avatar'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload avatar: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            }

            $insertId = $this->testimonialModel->insert($postData);

            if ($insertId) {
                $this->logActivity('Testimoni', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Testimoni berhasil dibuat.');
                return redirect()->to(base_url('admin/testimonials'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->testimonialModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Testimoni tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'id'        => $id,
                'name'      => $this->request->getPost('name'),
                'role'      => $this->request->getPost('role'),
                'content'   => $this->request->getPost('testimonial_message'),
                'rating'    => $this->request->getPost('rating'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if (! $this->testimonialModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->testimonialModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Avatar Upload if present
            $file = $this->request->getFile('avatar');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['avatar'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload avatar: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            }

            $this->testimonialModel->update($id, $postData);

            $this->logActivity('Testimoni', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Testimoni berhasil diperbarui.');
            return redirect()->to(base_url('admin/testimonials'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->testimonialModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Testimoni tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->testimonialModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Testimoni', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Testimoni berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/testimonials'));
    }

    public function restore($id)
    {
        $row = $this->testimonialModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Testimoni tidak ditemukan di tempat sampah.');
        }

        $this->testimonialModel->restoreWithUser((int) $id);
        $this->logActivity('Testimoni', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Testimoni berhasil dipulihkan.');
        return redirect()->to(base_url('admin/testimonials?trash=1'));
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

        $row = $this->testimonialModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Testimoni tidak ditemukan.');
        }

        $this->testimonialModel->delete($id, true); // Hard delete
        $this->logActivity('Testimoni', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Testimoni berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/testimonials?trash=1'));
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Testimoni Baru',
            'breadcrumbs' => ['Testimoni' => 'admin/testimonials', 'Buat' => ''],
        ];

        return view('admin/testimonials/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $data = [
            'row'         => $row,
            'title'       => 'Edit Testimoni',
            'breadcrumbs' => ['Testimoni' => 'admin/testimonials', 'Edit' => ''],
        ];

        return view('admin/testimonials/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }
        
        $targetDir = FCPATH . 'uploads/testimonials/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        // Sanitize file name for non-UTF8 safety per §7.5
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');
        
        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/testimonials/' . $safeName,
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

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PpdbModel;

class Ppdb extends BaseController
{
    protected PpdbModel $ppdbModel;

    public function __construct()
    {
        $this->ppdbModel = new PpdbModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $list = [];
        if ($db->tableExists('ppdb_applicants')) {
            $list = $this->ppdbModel->findAll();
        }

        $data = [
            'list'        => $list,
            'title'       => 'PPDB Online',
            'breadcrumbs' => ['PPDB Online' => ''],
        ];

        return view('admin/ppdb/index', $data);
    }

    public function view($id)
    {
        $db = \Config\Database::connect();

        $row = null;
        if ($db->tableExists('ppdb_applicants')) {
            $row = $this->ppdbModel->find($id);
        }

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data pendaftar PPDB tidak ditemukan.');
        }

        $documents = $this->ppdbModel->getDocuments((int) $id);

        $data = [
            'row'         => $row,
            'documents'   => $documents,
            'title'       => 'Detail Pendaftar PPDB',
            'breadcrumbs' => ['PPDB Online' => 'admin/ppdb', 'Detail' => ''],
        ];

        return view('admin/ppdb/view', $data);
    }

    public function update_status($id)
    {
        $row = $this->ppdbModel->find($id);
        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data pendaftar PPDB tidak ditemukan.');
        }

        $status  = $this->request->getPost('status');
        $allowed = ['pending', 'verified', 'accepted', 'rejected'];

        if (! in_array($status, $allowed, true)) {
            session()->setFlashdata('error', 'Status tidak valid.');
            return redirect()->to(base_url('admin/ppdb/view/' . $id));
        }

        $this->ppdbModel->update($id, ['status' => $status]);

        $this->logActivity('PPDB', 'update_status', json_encode($row), json_encode(['status' => $status]));
        session()->setFlashdata('success', 'Status pendaftar berhasil diperbarui.');
        return redirect()->to(base_url('admin/ppdb/view/' . $id));
    }

    public function delete($id)
    {
        $row = $this->ppdbModel->find($id);
        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data pendaftar PPDB tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->ppdbModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('PPDB', 'delete', json_encode($row), null);
        session()->setFlashdata('success', 'Data pendaftar berhasil dihapus.');
        return redirect()->to(base_url('admin/ppdb'));
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

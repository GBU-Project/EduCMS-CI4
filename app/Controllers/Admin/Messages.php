<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MessageModel;

class Messages extends BaseController
{
    protected MessageModel $messageModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
    }

    public function index()
    {
        $db   = \Config\Database::connect();
        $list = [];

        if ($db->tableExists('contact_messages')) {
            $list = $this->messageModel->findAll();
        }

        $data = [
            'list'        => $list,
            'title'       => 'Pesan Kontak',
            'breadcrumbs' => ['Pesan Kontak' => ''],
        ];

        return view('admin/messages/index', $data);
    }

    public function view($id)
    {
        $db  = \Config\Database::connect();
        $row = null;

        if ($db->tableExists('contact_messages')) {
            $row = $this->messageModel->find($id);
        }

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pesan kontak tidak ditemukan.');
        }

        if (isset($row->is_read) && ! $row->is_read) {
            $this->messageModel->update($id, ['is_read' => 1]);
            $row->is_read = 1;
        }

        $data = [
            'row'         => $row,
            'title'       => 'Detail Pesan',
            'breadcrumbs' => ['Pesan Kontak' => 'admin/messages', 'Detail' => ''],
        ];

        return view('admin/messages/view', $data);
    }

    public function delete($id)
    {
        $row = $this->messageModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pesan kontak tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->messageModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Messages', 'delete', json_encode($row), null);
        session()->setFlashdata('success', 'Pesan berhasil dihapus.');
        return redirect()->to(base_url('admin/messages'));
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

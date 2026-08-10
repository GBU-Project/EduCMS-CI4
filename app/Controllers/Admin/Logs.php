<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LogModel;

class Logs extends BaseController
{
    protected LogModel $logModel;

    public function __construct()
    {
        $this->logModel = new LogModel();
    }

    public function index()
    {
        $db   = \Config\Database::connect();
        $list = [];

        if ($db->tableExists('activity_logs')) {
            $list = $this->logModel->getLogsWithUsers([], 200, 0);
        }

        $data = [
            'list'        => $list,
            'title'       => 'Log Aktivitas',
            'breadcrumbs' => ['Log Aktivitas' => ''],
        ];

        return view('admin/logs/index', $data);
    }

    public function clear()
    {
        $db = \Config\Database::connect();

        if ($db->tableExists('activity_logs')) {
            $db->table('activity_logs')->emptyTable();
        }

        session()->setFlashdata('success', 'Log aktivitas berhasil dibersihkan.');
        return redirect()->to(base_url('admin/logs'));
    }
}

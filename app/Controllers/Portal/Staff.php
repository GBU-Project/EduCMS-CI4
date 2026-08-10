<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\StaffModel;

class Staff extends BaseController
{
    protected StaffModel $staffModel;

    public function __construct()
    {
        $this->staffModel = new StaffModel();
    }

    public function index()
    {
        helper(['educms']);

        $staffList = $this->staffModel
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->findAll();

        $data = [
            'title'      => 'Direktori Staf' . (' | ' . site_name()),
            'staff_list' => $staffList,
        ];

        return view('portal/staff_index', $data);
    }

    public function detail($id)
    {
        helper(['educms']);

        $staff = $this->staffModel
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();

        if (! $staff || $staff->status !== 'active') {
            $data = ['title' => 'Data Staf Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $data = [
            'title' => $staff->name . (' | ' . site_name()),
            'staff' => $staff,
        ];

        return view('portal/staff_detail', $data);
    }
}

<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\TeacherModel;

class Guru extends BaseController
{
    protected TeacherModel $teacherModel;

    public function __construct()
    {
        $this->teacherModel = new TeacherModel();
    }

    public function index()
    {
        helper(['educms']);

        $teachers = $this->teacherModel
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->findAll();

        $data = [
            'title'    => 'Direktori Guru' . (' | ' . site_name()),
            'teachers' => $teachers,
        ];

        return view('portal/guru_index', $data);
    }

    public function detail($id)
    {
        helper(['educms']);

        $teacher = $this->teacherModel
            ->where('id', $id)
            ->where('deleted_at', null)
            ->first();

        if (! $teacher || $teacher->status !== 'active') {
            $data = ['title' => 'Data Guru Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $data = [
            'title'   => $teacher->name . (' | ' . site_name()),
            'teacher' => $teacher,
        ];

        return view('portal/guru_detail', $data);
    }
}

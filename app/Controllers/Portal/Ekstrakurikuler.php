<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\ExtracurricularModel;

class Ekstrakurikuler extends BaseController
{
    protected ExtracurricularModel $extracurricularModel;

    public function __construct()
    {
        $this->extracurricularModel = new ExtracurricularModel();
    }

    public function index()
    {
        helper(['educms']);

        $items = $this->extracurricularModel
            ->where('deleted_at', null)
            ->findAll();

        $data = [
            'title' => 'Ekstrakurikuler' . (' | ' . site_name()),
            'items' => $items,
        ];

        return view('portal/ekstrakurikuler_index', $data);
    }

    public function detail($slug)
    {
        helper(['educms']);

        $item = $this->extracurricularModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $item) {
            $data = ['title' => 'Ekstrakurikuler Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $data = [
            'title' => $item->name . (' | ' . site_name()),
            'item'  => $item,
        ];

        return view('portal/ekstrakurikuler_detail', $data);
    }
}

<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\MessageModel;

class Contact extends BaseController
{
    protected MessageModel $messageModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
    }

    public function index()
    {
        helper(['educms', 'form', 'sanitize']);

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'name'    => sanitize_html($this->request->getPost('name')),
                'email'   => $this->request->getPost('email'),
                'phone'   => $this->request->getPost('phone'),
                'subject' => sanitize_html($this->request->getPost('subject')),
                'message' => sanitize_html($this->request->getPost('message')),
                'is_read' => 0,
            ];

            if (! $this->messageModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->messageModel->errors()));
                $data = ['title' => 'Hubungi Kami' . (' | ' . site_name())];
                return view('portal/contact', $data);
            }

            $this->messageModel->insert($postData);

            session()->setFlashdata('contact_success', 'Pesan Anda berhasil terkirim. Kami akan segera menghubungi Anda kembali.');
            return redirect()->to(base_url('kontak'));
        }

        $data = ['title' => 'Hubungi Kami' . (' | ' . site_name())];
        return view('portal/contact', $data);
    }
}

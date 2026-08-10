<?php

namespace App\Controllers\Portal;

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
        helper(['educms', 'form']);

        if ($this->request->getMethod() === 'post') {
            $postData = [
                'full_name'       => $this->request->getPost('full_name'),
                'nisn'            => $this->request->getPost('nisn'),
                'nik'             => $this->request->getPost('nik'),
                'gender'          => $this->request->getPost('gender'),
                'place_of_birth'  => $this->request->getPost('place_of_birth'),
                'date_of_birth'   => $this->request->getPost('date_of_birth'),
                'address'         => $this->request->getPost('address'),
                'email'           => $this->request->getPost('email'),
                'phone'           => $this->request->getPost('phone'),
                'parent_name'     => $this->request->getPost('parent_name'),
                'parent_phone'    => $this->request->getPost('parent_phone'),
                'previous_school' => $this->request->getPost('previous_school'),
            ];

            if (! $this->ppdbModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->ppdbModel->errors()));
                $data = ['title' => 'PPDB Online' . (' | ' . site_name())];
                return view('portal/ppdb', $data);
            }

            return $this->_handleSubmit($postData);
        }

        $data = ['title' => 'PPDB Online' . (' | ' . site_name())];
        return view('portal/ppdb', $data);
    }

    protected function _handleSubmit(array $postData)
    {
        $regNumber = 'PPDB-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
        $postData['registration_number'] = $regNumber;
        $postData['status']              = 'pending';

        $applicantId = $this->ppdbModel->insert($postData);

        if ($applicantId) {
            $db = \Config\Database::connect();
            if ($db->tableExists('ppdb_documents')) {
                $docFields = [
                    'photo'  => ['label' => 'foto'],
                    'kk'     => ['label' => 'kartu_keluarga'],
                    'akta'   => ['label' => 'akta_kelahiran'],
                    'raport' => ['label' => 'raport'],
                ];

                foreach ($docFields as $field => $meta) {
                    $file = $this->request->getFile($field);
                    if ($file && $file->isValid() && ! $file->hasMoved()) {
                        $uploadResult = $this->handleUpload($file);
                        if ($uploadResult['status']) {
                            $db->table('ppdb_documents')->insert([
                                'applicant_id'  => $applicantId,
                                'document_type' => $meta['label'],
                                'file_path'     => $uploadResult['file_path'],
                                'created_at'    => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }

            $data = [
                'title'               => 'Pendaftaran Berhasil - PPDB Online',
                'registration_number' => $regNumber,
                'full_name'           => $postData['full_name'],
            ];

            return view('portal/ppdb_success', $data);
        }

        session()->setFlashdata('error', 'Gagal memproses pendaftaran. Silakan coba lagi.');
        return redirect()->to(base_url('ppdb'));
    }

    protected function handleUpload($file): array
    {
        $guard = new \App\Libraries\UploadGuard();
        $validation = $guard->validateFile($file, 'ppdb');
        if (! $validation['status']) {
            return $validation;
        }

        $targetDir = FCPATH . 'uploads/ppdb/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName  = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/ppdb/' . $safeName,
            ];
        }

        return [
            'status' => false,
            'error'  => $file->getErrorString(),
        ];
    }
}

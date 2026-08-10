<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AgendaModel;

class Agendas extends BaseController
{
    protected AgendaModel $agendaModel;

    public function __construct()
    {
        $this->agendaModel = new AgendaModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->agendaModel->onlyDeleted()->findAll();
        } else {
            $list = $this->agendaModel->findAll();
        }

        $data = [
            'title'       => 'Agenda Kegiatan' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Agenda Kegiatan' => 'admin/agendas', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/agendas/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug);

            $startDate = $this->request->getPost('start_date');
            $endDate   = $this->request->getPost('end_date');

            if (! empty($startDate) && ! empty($endDate)) {
                if (strtotime($endDate) < strtotime($startDate)) {
                    session()->setFlashdata('warning', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai kegiatan.');
                }
            }

            $postData = [
                'title'       => $title,
                'slug'        => $slug,
                'description' => sanitize_html($this->request->getPost('description')),
                'start_date'  => $startDate,
                'end_date'    => $endDate,
                'location'    => $this->request->getPost('location'),
                'coordinator' => $this->request->getPost('coordinator'),
                'status'      => $this->request->getPost('status'),
            ];

            if (! $this->agendaModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->agendaModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->agendaModel->insert($postData);

            if ($insertId) {
                // Save SEO metadata
                $this->saveSeo('agendas_' . $insertId);

                $this->logActivity('Agenda', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Agenda berhasil dibuat.');
                return redirect()->to(base_url('admin/agendas'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->agendaModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Agenda tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $startDate = $this->request->getPost('start_date');
            $endDate   = $this->request->getPost('end_date');

            if (! empty($startDate) && ! empty($endDate)) {
                if (strtotime($endDate) < strtotime($startDate)) {
                    session()->setFlashdata('warning', 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai kegiatan.');
                }
            }

            $postData = [
                'id'          => $id,
                'title'       => $title,
                'slug'        => $slug,
                'description' => sanitize_html($this->request->getPost('description')),
                'start_date'  => $startDate,
                'end_date'    => $endDate,
                'location'    => $this->request->getPost('location'),
                'coordinator' => $this->request->getPost('coordinator'),
                'status'      => $this->request->getPost('status'),
            ];

            if (! $this->agendaModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->agendaModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->agendaModel->update($id, $postData);

            // Save SEO metadata
            $this->saveSeo('agendas_' . $id);

            $this->logActivity('Agenda', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Agenda berhasil diperbarui.');
            return redirect()->to(base_url('admin/agendas'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->agendaModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Agenda tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->agendaModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Agenda', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Agenda berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/agendas'));
    }

    public function restore($id)
    {
        $row = $this->agendaModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Agenda tidak ditemukan di tempat sampah.');
        }

        $this->agendaModel->restoreWithUser((int) $id);
        $this->logActivity('Agenda', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Agenda berhasil dipulihkan.');
        return redirect()->to(base_url('admin/agendas?trash=1'));
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

        $row = $this->agendaModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Agenda tidak ditemukan.');
        }

        $this->agendaModel->delete($id, true); // Hard delete
        $this->logActivity('Agenda', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Agenda berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/agendas?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->agendaModel->builder()->where('slug', $slug);
            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }

            if ($builder->countAllResults() === 0) {
                break;
            }

            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    protected function _renderCreateView()
    {
        $data = [
            'title'       => 'Buat Agenda Baru',
            'breadcrumbs' => ['Agenda Kegiatan' => 'admin/agendas', 'Buat' => ''],
        ];

        return view('admin/agendas/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_settings')->where('page_name', 'agendas_' . $id)->get()->getRow();

        $data = [
            'row'         => $row,
            'seo'         => $seo,
            'title'       => 'Edit Agenda',
            'breadcrumbs' => ['Agenda Kegiatan' => 'admin/agendas', 'Edit' => ''],
        ];

        return view('admin/agendas/edit', $data);
    }

    protected function saveSeo(string $pageIdentifier)
    {
        $metaTitle = $this->request->getPost('meta_title');
        $metaDesc  = $this->request->getPost('meta_description');
        $keywords  = $this->request->getPost('meta_keywords');

        if (! empty($metaTitle) || ! empty($metaDesc) || ! empty($keywords)) {
            $db = \Config\Database::connect();
            $existing = $db->table('seo_settings')->where('page_name', $pageIdentifier)->get()->getRow();

            $seoData = [
                'page_name'  => $pageIdentifier,
                'meta_title'       => $metaTitle,
                'meta_description' => $metaDesc,
                'keywords'         => $keywords,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $db->table('seo_settings')->where('id', $existing->id)->update($seoData);
            } else {
                $seoData['created_at'] = date('Y-m-d H:i:s');
                $db->table('seo_settings')->insert($seoData);
            }
        }
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

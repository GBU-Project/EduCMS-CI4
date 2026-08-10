<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcements extends BaseController
{
    protected AnnouncementModel $announcementModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->announcementModel->onlyDeleted()->findAll();
        } else {
            $list = $this->announcementModel->findAll();
        }

        $data = [
            'title'       => 'Pengumuman' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Pengumuman' => 'admin/announcements', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/announcements/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug);

            $postData = [
                'title'     => $title,
                'slug'      => $slug,
                'content'   => sanitize_html($this->request->getPost('content')),
                'is_pinned' => $this->request->getPost('is_pinned') ? 1 : 0,
                'status'    => $this->request->getPost('status'),
            ];

            if (! $this->announcementModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->announcementModel->errors()));
                return $this->_renderCreateView();
            }

            $insertId = $this->announcementModel->insert($postData);

            if ($insertId) {
                // Save SEO metadata
                $this->saveSeo('announcements_' . $insertId);

                $this->logActivity('Pengumuman', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Pengumuman berhasil dibuat.');
                return redirect()->to(base_url('admin/announcements'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->announcementModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengumuman tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $postData = [
                'id'        => $id,
                'title'     => $title,
                'slug'      => $slug,
                'content'   => sanitize_html($this->request->getPost('content')),
                'is_pinned' => $this->request->getPost('is_pinned') ? 1 : 0,
                'status'    => $this->request->getPost('status'),
            ];

            if (! $this->announcementModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->announcementModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            $this->announcementModel->update($id, $postData);

            // Save SEO metadata
            $this->saveSeo('announcements_' . $id);

            $this->logActivity('Pengumuman', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Pengumuman berhasil diperbarui.');
            return redirect()->to(base_url('admin/announcements'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function delete($id)
    {
        $row = $this->announcementModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengumuman tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->announcementModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Pengumuman', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Pengumuman berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/announcements'));
    }

    public function restore($id)
    {
        $row = $this->announcementModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengumuman tidak ditemukan di tempat sampah.');
        }

        $this->announcementModel->restoreWithUser((int) $id);
        $this->logActivity('Pengumuman', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Pengumuman berhasil dipulihkan.');
        return redirect()->to(base_url('admin/announcements?trash=1'));
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

        $row = $this->announcementModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengumuman tidak ditemukan.');
        }

        $this->announcementModel->delete($id, true); // Hard delete
        $this->logActivity('Pengumuman', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Pengumuman berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/announcements?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->announcementModel->builder()->where('slug', $slug);
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
            'title'       => 'Buat Pengumuman Baru',
            'breadcrumbs' => ['Pengumuman' => 'admin/announcements', 'Buat' => ''],
        ];

        return view('admin/announcements/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_metadata')->where('page_identifier', 'announcements_' . $id)->get()->getRow();

        $data = [
            'row'         => $row,
            'seo'         => $seo,
            'title'       => 'Edit Pengumuman',
            'breadcrumbs' => ['Pengumuman' => 'admin/announcements', 'Edit' => ''],
        ];

        return view('admin/announcements/edit', $data);
    }

    protected function saveSeo(string $pageIdentifier)
    {
        $metaTitle = $this->request->getPost('meta_title');
        $metaDesc  = $this->request->getPost('meta_description');
        $keywords  = $this->request->getPost('meta_keywords');

        if (! empty($metaTitle) || ! empty($metaDesc) || ! empty($keywords)) {
            $db = \Config\Database::connect();
            $existing = $db->table('seo_metadata')->where('page_identifier', $pageIdentifier)->get()->getRow();

            $seoData = [
                'page_identifier'  => $pageIdentifier,
                'meta_title'       => $metaTitle,
                'meta_description' => $metaDesc,
                'keywords'         => $keywords,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $db->table('seo_metadata')->where('id', $existing->id)->update($seoData);
            } else {
                $seoData['created_at'] = date('Y-m-d H:i:s');
                $db->table('seo_metadata')->insert($seoData);
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

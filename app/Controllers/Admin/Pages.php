<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PageModel;

class Pages extends BaseController
{
    protected PageModel $pageModel;

    protected array $templates = [
        'default'   => 'Default Template',
        'contact'   => 'Contact Us Page',
        'fullwidth' => 'Full Width Page',
    ];

    public function __construct()
    {
        $this->pageModel = new PageModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->pageModel->onlyDeleted()->findAll();
        } else {
            $list = $this->pageModel->findAll();
        }

        $data = [
            'title'       => 'Halaman Statis' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Halaman Statis' => 'admin/pages', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/pages/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug);

            $userId = session()->get('user_id');

            $postData = [
                'title'     => $title,
                'slug'      => $slug,
                'content'   => sanitize_html($this->request->getPost('content')),
                'template'  => $this->request->getPost('template') ?? 'default',
                'status'    => $this->request->getPost('status'),
                'author_id' => $userId ? (int) $userId : null,
            ];

            if (! $this->pageModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->pageModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Banner Upload
            $file = $this->request->getFile('banner');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['banner'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload banner: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            } else {
                $pickerBanner = $this->request->getPost('banner');
                if (! empty($pickerBanner)) {
                    $postData['banner'] = trim((string) $pickerBanner);
                }
            }

            $insertId = $this->pageModel->insert($postData);

            if ($insertId) {
                // Save SEO metadata
                $this->saveSeo('pages_' . $insertId);

                $this->logActivity('Halaman', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Halaman berhasil dibuat.');
                return redirect()->to(base_url('admin/pages'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->pageModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Halaman tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $postData = [
                'id'       => $id,
                'title'    => $title,
                'slug'     => $slug,
                'content'  => sanitize_html($this->request->getPost('content')),
                'template' => $this->request->getPost('template') ?? 'default',
                'status'   => $this->request->getPost('status'),
            ];

            if (! $this->pageModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->pageModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Banner Upload
            $file = $this->request->getFile('banner');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['banner'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload banner: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            } else {
                $pickerBanner = $this->request->getPost('banner');
                if (! empty($pickerBanner)) {
                    $postData['banner'] = trim((string) $pickerBanner);
                }
            }

            $this->pageModel->update($id, $postData);

            // Save SEO metadata
            $this->saveSeo('pages_' . $id);

            $this->logActivity('Halaman', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Halaman berhasil diperbarui.');
            return redirect()->to(base_url('admin/pages'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function preview($id)
    {
        $page = $this->pageModel->find($id);
        if (! $page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Halaman tidak ditemukan.');
        }

        $data = [
            'title'       => 'Pratinjau: ' . $page->title,
            'page'        => $page,
            'breadcrumbs' => ['Halaman Statis' => 'admin/pages', 'Pratinjau' => ''],
        ];

        return view('admin/pages/preview', $data);
    }

    public function delete($id)
    {
        $row = $this->pageModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Halaman tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->pageModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Halaman', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Halaman berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/pages'));
    }

    public function restore($id)
    {
        $row = $this->pageModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Halaman tidak ditemukan di tempat sampah.');
        }

        $this->pageModel->restoreWithUser((int) $id);
        $this->logActivity('Halaman', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Halaman berhasil dipulihkan.');
        return redirect()->to(base_url('admin/pages?trash=1'));
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

        $row = $this->pageModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Halaman tidak ditemukan.');
        }

        $this->pageModel->delete($id, true); // Hard delete
        $this->logActivity('Halaman', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Halaman berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/pages?trash=1'));
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->pageModel->builder()->where('slug', $slug);
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
            'templates'   => $this->templates,
            'title'       => 'Buat Halaman Baru',
            'breadcrumbs' => ['Halaman Statis' => 'admin/pages', 'Buat' => ''],
        ];

        return view('admin/pages/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_settings')->where('page_name', 'pages_' . $id)->get()->getRow();

        $data = [
            'row'         => $row,
            'page'        => $row,
            'templates'   => $this->templates,
            'seo'         => $seo,
            'title'       => 'Edit Halaman',
            'breadcrumbs' => ['Halaman Statis' => 'admin/pages', 'Edit' => ''],
        ];

        return view('admin/pages/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/pages/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/pages/' . $safeName,
            ];
        }

        return [
            'status' => false,
            'error'  => $file->getErrorString(),
        ];
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

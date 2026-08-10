<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TagModel;

class Posts extends BaseController
{
    protected PostModel $postModel;
    protected CategoryModel $categoryModel;
    protected TagModel $tagModel;

    public function __construct()
    {
        $this->postModel     = new PostModel();
        $this->categoryModel = new CategoryModel();
        $this->tagModel      = new TagModel();
    }

    public function index()
    {
        $showTrash = (bool) $this->request->getGet('trash');

        if ($showTrash) {
            $list = $this->postModel->onlyDeleted()->findAll();
        } else {
            $list = $this->postModel->findAll();
        }

        $data = [
            'title'       => 'Berita & Artikel' . ($showTrash ? ' (Tempat Sampah)' : ''),
            'breadcrumbs' => ['Berita & Artikel' => 'admin/posts', ($showTrash ? 'Sampah' : 'Daftar') => ''],
            'show_trash'  => $showTrash,
            'list'        => $list,
        ];

        return view('admin/posts/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug);

            $status = $this->request->getPost('status');
            $userId = session()->get('user_id');

            $postData = [
                'title'       => $title,
                'slug'        => $slug,
                'content'     => sanitize_html($this->request->getPost('content')),
                'status'      => $status,
                'author_id'   => $userId ? (int) $userId : null,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            ];

            if ($status === 'published') {
                $postData['published_by'] = $userId ? (int) $userId : null;
                $postData['published_at'] = date('Y-m-d H:i:s');
            }

            if (! $this->postModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->postModel->errors()));
                return $this->_renderCreateView();
            }

            // Handle Image Upload
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar: ' . $uploadResult['error']);
                    return $this->_renderCreateView();
                }
            } else {
                $pickerImage = $this->request->getPost('image');
                if (! empty($pickerImage)) {
                    $postData['image'] = trim((string) $pickerImage);
                }
            }

            $insertId = $this->postModel->insert($postData);

            if ($insertId) {
                // Save taxonomy relations
                $categoryIds = $this->_resolveTaxonomyIds('category_text', $this->categoryModel);
                $tagIds      = $this->_resolveTaxonomyIds('tag_text', $this->tagModel);
                $this->postModel->saveRelations((int) $insertId, $categoryIds, $tagIds);

                // Save SEO metadata
                $this->saveSeo('posts_' . $insertId);

                $this->logActivity('Berita', 'create', null, json_encode(['id' => $insertId] + $postData));
                session()->setFlashdata('success', 'Berita berhasil dibuat.');
                return redirect()->to(base_url('admin/posts'));
            }
        }

        return $this->_renderCreateView();
    }

    public function edit($id)
    {
        $row = $this->postModel->withDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        if ($this->request->getMethod() === 'post') {
            helper(['educms', 'sanitize']);
            $title = $this->request->getPost('title');
            $slugInput = $this->request->getPost('slug');
            $slug = ! empty($slugInput) ? slugify($slugInput) : slugify($title);
            $slug = $this->generateUniqueSlug($slug, (int) $id);

            $status = $this->request->getPost('status');
            $userId = session()->get('user_id');

            $postData = [
                'id'          => $id,
                'title'       => $title,
                'slug'        => $slug,
                'content'     => sanitize_html($this->request->getPost('content')),
                'status'      => $status,
                'updated_by'  => $userId ? (int) $userId : null,
                'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            ];

            if ($status === 'published' && $row->status !== 'published') {
                $postData['published_by'] = $userId ? (int) $userId : null;
                $postData['published_at'] = date('Y-m-d H:i:s');
            }

            if (! $this->postModel->validate($postData)) {
                session()->setFlashdata('error', implode('<br>', $this->postModel->errors()));
                return $this->_renderEditView($id, $row);
            }

            // Handle Image Upload
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $uploadResult = $this->handleUpload($file);
                if ($uploadResult['status']) {
                    $postData['image'] = $uploadResult['file_path'];
                } else {
                    session()->setFlashdata('error', 'Gagal upload gambar: ' . $uploadResult['error']);
                    return $this->_renderEditView($id, $row);
                }
            } else {
                $pickerImage = $this->request->getPost('image');
                if (! empty($pickerImage)) {
                    $postData['image'] = trim((string) $pickerImage);
                }
            }

            $this->postModel->update($id, $postData);

            // Save taxonomy relations
            $categoryIds = $this->_resolveTaxonomyIds('category_text', $this->categoryModel);
            $tagIds      = $this->_resolveTaxonomyIds('tag_text', $this->tagModel);
            $this->postModel->saveRelations((int) $id, $categoryIds, $tagIds);

            // Save SEO metadata
            $this->saveSeo('posts_' . $id);

            $this->logActivity('Berita', 'update', json_encode($row), json_encode($postData));
            session()->setFlashdata('success', 'Berita berhasil diperbarui.');
            return redirect()->to(base_url('admin/posts'));
        }

        return $this->_renderEditView($id, $row);
    }

    public function preview($id)
    {
        $post = $this->postModel->find($id);
        if (! $post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        $post->categories = $this->postModel->getPostCategories((int) $id);
        $post->tags       = $this->postModel->getPostTags((int) $id);

        $data = [
            'title'       => 'Pratinjau Berita: ' . $post->title,
            'post'        => $post,
            'breadcrumbs' => ['Berita & Artikel' => 'admin/posts', 'Pratinjau' => ''],
        ];

        return view('admin/posts/preview', $data);
    }

    public function delete($id)
    {
        $row = $this->postModel->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        $userId = session()->get('user_id');
        $this->postModel->softDeleteWithUser((int) $id, $userId ? (int) $userId : null);

        $this->logActivity('Berita', 'delete', json_encode($row), null);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }

        session()->setFlashdata('success', 'Berita berhasil dipindahkan ke tempat sampah.');
        return redirect()->to(base_url('admin/posts'));
    }

    public function restore($id)
    {
        $row = $this->postModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan di tempat sampah.');
        }

        $this->postModel->restoreWithUser((int) $id);
        $this->logActivity('Berita', 'restore', null, json_encode($row));

        session()->setFlashdata('success', 'Berita berhasil dipulihkan.');
        return redirect()->to(base_url('admin/posts?trash=1'));
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

        $row = $this->postModel->onlyDeleted()->find($id);

        if (! $row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Berita tidak ditemukan.');
        }

        $this->postModel->delete($id, true); // Hard delete
        $this->logActivity('Berita', 'force_delete', json_encode($row), null);

        session()->setFlashdata('success', 'Berita berhasil dihapus secara permanen.');
        return redirect()->to(base_url('admin/posts?trash=1'));
    }

    protected function _resolveTaxonomyIds(string $postField, $model): array
    {
        $raw = $this->request->getPost($postField);
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $names     = [];
        $seenLower = [];
        foreach (explode(',', $raw) as $piece) {
            $name = trim($piece);
            if ($name === '') {
                continue;
            }
            $lower = strtolower($name);
            if (isset($seenLower[$lower])) {
                continue;
            }
            $seenLower[$lower] = true;
            $names[]           = $name;
        }

        $ids = [];
        foreach ($names as $name) {
            $ids[] = $model->findOrCreate($name);
        }
        return $ids;
    }

    protected function generateUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $i = 1;

        while (true) {
            $builder = $this->postModel->builder()->where('slug', $slug);
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
            'title'       => 'Tulis Berita Baru',
            'breadcrumbs' => ['Berita & Artikel' => 'admin/posts', 'Tulis' => ''],
        ];

        return view('admin/posts/create', $data);
    }

    protected function _renderEditView($id, $row)
    {
        $currentCats = $this->postModel->getPostCategories((int) $id);
        $currentTags = $this->postModel->getPostTags((int) $id);

        $catText = implode(', ', array_map(static fn ($c) => $c->name, $currentCats));
        $tagText = implode(', ', array_map(static fn ($t) => $t->name, $currentTags));

        $db  = \Config\Database::connect();
        $seo = $db->table('seo_settings')->where('page_name', 'posts_' . $id)->get()->getRow();

        $data = [
            'row'           => $row,
            'post'          => $row,
            'category_text' => $catText,
            'tag_text'      => $tagText,
            'seo'           => $seo,
            'title'         => 'Edit Berita',
            'breadcrumbs'   => ['Berita & Artikel' => 'admin/posts', 'Edit' => ''],
        ];

        return view('admin/posts/edit', $data);
    }

    protected function handleUpload($file): array
    {
        if (function_exists('upload_media')) {
            helper('upload');
        }

        $targetDir = FCPATH . 'uploads/posts/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $safeName = mb_convert_encoding($newName, 'UTF-8', 'UTF-8');

        if ($file->move($targetDir, $safeName)) {
            return [
                'status'    => true,
                'file_path' => 'uploads/posts/' . $safeName,
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

<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcement extends BaseController
{
    protected AnnouncementModel $announcementModel;
    protected int $perPage = 10;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
    }

    public function index()
    {
        helper(['educms']);

        $page   = (int) $this->request->getGet('page');
        $page   = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $this->perPage;

        $builder = $this->announcementModel
            ->where('status', 'published')
            ->where('deleted_at', null);

        $total = $builder->countAllResults(false);

        $announcements = $builder
            ->orderBy('is_pinned', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($this->perPage, $offset);

        $seoMeta = $this->loadSeo('announcements_index', 'Pengumuman resmi sekolah.');

        $data = [
            'title'         => 'Pengumuman' . (' | ' . site_name()),
            'announcements' => $announcements,
            'current_page'  => $page,
            'total_pages'   => max(1, (int) ceil($total / $this->perPage)),
            'seo_meta'      => $seoMeta,
        ];

        return view('portal/announcement_index', $data);
    }

    public function detail($slug)
    {
        helper(['educms']);

        $item = $this->announcementModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        // A draft must be exactly as unreachable as a nonexistent slug
        if (! $item || $item->status !== 'published') {
            $data = ['title' => 'Pengumuman Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $seoMeta = $this->loadSeo('announcements_' . $item->id, strip_tags($item->content));

        $data = [
            'title'    => $item->title . (' | ' . site_name()),
            'item'     => $item,
            'seo_meta' => $seoMeta,
        ];

        return view('portal/announcement_detail', $data);
    }

    protected function loadSeo(string $pageName, string $fallbackDescription = ''): array
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_settings')->where('page_name', $pageName)->get()->getRow();

        return [
            'meta_title'       => $seo->meta_title ?? '',
            'meta_description' => $seo->meta_description ?? $fallbackDescription,
            'keywords'         => $seo->keywords ?? '',
            'og_title'         => $seo->og_title ?? '',
            'og_description'   => $seo->og_description ?? '',
            'og_image'         => $seo->og_image ?? '',
            'canonical_url'    => $seo->canonical_url ?? '',
        ];
    }
}

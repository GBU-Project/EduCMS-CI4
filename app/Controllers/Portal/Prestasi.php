<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\AchievementModel;

class Prestasi extends BaseController
{
    protected AchievementModel $achievementModel;

    public function __construct()
    {
        $this->achievementModel = new AchievementModel();
    }

    public function index()
    {
        helper(['educms']);

        $achievements = $this->achievementModel
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->orderBy('date', 'DESC')
            ->findAll();

        $seoMeta = $this->loadSeo('achievements_index', 'Galeri prestasi siswa dan sekolah.');

        $data = [
            'title'        => 'Prestasi Sekolah' . (' | ' . site_name()),
            'achievements' => $achievements,
            'seo_meta'     => $seoMeta,
        ];

        return view('portal/prestasi_index', $data);
    }

    public function detail($slug)
    {
        helper(['educms']);

        $item = $this->achievementModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        // A draft must be exactly as unreachable as a nonexistent slug
        if (! $item || $item->status !== 'published') {
            $data = ['title' => 'Prestasi Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $seoMeta = $this->loadSeo('achievements_' . $item->id, strip_tags($item->description));

        $data = [
            'title'    => $item->title . (' | ' . site_name()),
            'item'     => $item,
            'seo_meta' => $seoMeta,
        ];

        return view('portal/prestasi_detail', $data);
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

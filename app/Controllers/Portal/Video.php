<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\VideoModel;

class Video extends BaseController
{
    protected VideoModel $videoModel;
    protected int $perPage = 9;

    public function __construct()
    {
        $this->videoModel = new VideoModel();
        helper(['video']);
    }

    public function index()
    {
        helper(['educms']);

        $page   = (int) $this->request->getGet('page');
        $page   = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $this->perPage;

        $builder = $this->videoModel
            ->where('status', 'published')
            ->where('deleted_at', null);

        $total = $builder->countAllResults(false);

        $videos = $builder
            ->orderBy('is_featured', 'DESC')
            ->orderBy('order_num', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll($this->perPage, $offset);

        $seoMeta = $this->loadSeo('videos_index', 'Kumpulan video profil dan kegiatan sekolah.');

        $data = [
            'title'        => 'Video' . (' | ' . site_name()),
            'videos'       => $videos,
            'current_page' => $page,
            'total_pages'  => max(1, (int) ceil($total / $this->perPage)),
            'seo_meta'     => $seoMeta,
        ];

        return view('portal/video_index', $data);
    }

    public function detail($slug)
    {
        helper(['educms']);

        $item = $this->videoModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $item || $item->status !== 'published') {
            $data = ['title' => 'Video Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $related = $this->videoModel
            ->where('status', 'published')
            ->where('id !=', $item->id)
            ->where('deleted_at', null)
            ->orderBy('is_featured', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll(3);

        $seoMeta = $this->loadSeo('videos_' . $item->id, strip_tags($item->description ?? ''));

        $data = [
            'title'    => $item->title . (' | ' . site_name()),
            'item'     => $item,
            'related'  => $related,
            'seo_meta' => $seoMeta,
        ];

        return view('portal/video_detail', $data);
    }

    protected function loadSeo(string $pageName, string $fallbackDescription = ''): array
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_metadata')->where('page_identifier', $pageName)->get()->getRow();

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

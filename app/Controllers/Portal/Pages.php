<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\PageModel;

class Pages extends BaseController
{
    protected PageModel $pageModel;

    public function __construct()
    {
        $this->pageModel = new PageModel();
    }

    public function detail($slug)
    {
        helper(['educms']);

        $page = $this->pageModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $page || $page->status !== 'published') {
            $data = [
                'title'          => 'Halaman Tidak Ditemukan',
                'requested_slug' => $slug,
            ];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        // Increment view counter
        $db = \Config\Database::connect();
        $db->table('pages')->where('id', $page->id)->set('view_count', 'view_count+1', false)->update();

        $seoMeta = $this->loadSeo('pages_' . $page->id, strip_tags($page->content));

        $data = [
            'title'    => $page->title . (' | ' . site_name()),
            'page'     => $page,
            'seo_meta' => $seoMeta,
        ];

        return view('portal/page_detail', $data);
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

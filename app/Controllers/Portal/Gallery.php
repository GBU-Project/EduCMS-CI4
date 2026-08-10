<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\GalleryItemModel;
use App\Models\GalleryModel;

class Gallery extends BaseController
{
    protected GalleryModel $galleryModel;
    protected GalleryItemModel $galleryItemModel;

    public function __construct()
    {
        $this->galleryModel     = new GalleryModel();
        $this->galleryItemModel = new GalleryItemModel();
    }

    public function photo()
    {
        return $this->_index('photo', 'Galeri Foto');
    }

    public function video()
    {
        return $this->_index('video', 'Galeri Video');
    }

    private function _index(string $type, string $pageTitle)
    {
        helper(['educms']);

        $albums = $this->galleryModel
            ->where('type', $type)
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $seoMeta = $this->loadSeo('gallery_' . $type, 'Dokumentasi ' . strtolower($pageTitle) . ' kegiatan sekolah.');

        $data = [
            'title'      => $pageTitle . (' | ' . site_name()),
            'type'       => $type,
            'page_title' => $pageTitle,
            'albums'     => $albums,
            'seo_meta'   => $seoMeta,
        ];

        return view('portal/gallery_index', $data);
    }

    public function photo_detail($slug)
    {
        return $this->_detail($slug, 'photo', 'galeri-foto');
    }

    public function video_detail($slug)
    {
        return $this->_detail($slug, 'video', 'galeri-video');
    }

    private function _detail(string $slug, string $type, string $baseRoute)
    {
        helper(['educms']);

        $album = $this->galleryModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $album || $album->type !== $type) {
            $data = ['title' => 'Album Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $items = $this->galleryItemModel->getByGallery((int) $album->id);
        $seoMeta = $this->loadSeo('gallery_album_' . $album->id, strip_tags($album->description ?? ''));

        $data = [
            'album'      => $album,
            'items'      => $items,
            'base_route' => $baseRoute,
            'title'      => $album->title . (' | ' . site_name()),
            'seo_meta'   => $seoMeta,
        ];

        return view('portal/gallery_detail', $data);
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

<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\AgendaModel;

class Agenda extends BaseController
{
    protected AgendaModel $agendaModel;

    public function __construct()
    {
        $this->agendaModel = new AgendaModel();
    }

    public function index()
    {
        helper(['educms']);

        $agendas = $this->agendaModel
            ->where('status', 'published')
            ->where('deleted_at', null)
            ->orderBy('start_date', 'ASC')
            ->findAll();

        $seoMeta = $this->loadSeo('agendas_index', 'Kalender dan agenda kegiatan sekolah.');

        $data = [
            'title'    => 'Agenda Kegiatan' . (' | ' . site_name()),
            'agendas'  => $agendas,
            'seo_meta' => $seoMeta,
        ];

        return view('portal/agenda_index', $data);
    }

    public function detail($slug)
    {
        helper(['educms']);

        $item = $this->agendaModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        // A draft must be exactly as unreachable as a nonexistent slug
        if (! $item || $item->status !== 'published') {
            $data = ['title' => 'Agenda Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        $seoMeta = $this->loadSeo('agendas_' . $item->id, strip_tags($item->description));

        $data = [
            'title'    => $item->title . (' | ' . site_name()),
            'item'     => $item,
            'seo_meta' => $seoMeta,
        ];

        return view('portal/agenda_detail', $data);
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

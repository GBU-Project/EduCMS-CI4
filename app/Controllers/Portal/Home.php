<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\AgendaModel;
use App\Models\AnnouncementModel;
use App\Models\GalleryModel;
use App\Models\PartnerModel;
use App\Models\PostModel;
use App\Models\SliderModel;
use App\Models\StaffModel;
use App\Models\TeacherModel;
use App\Models\TestimonialModel;
use App\Models\VideoModel;

class Home extends BaseController
{
    public function index()
    {
        helper(['educms']);
        $db = \Config\Database::connect();

        $hp = [];
        if ($db->tableExists('settings')) {
            $rows = $db->table('settings')->where('group_name', 'homepage')->get()->getResult();
            foreach ($rows as $r) {
                $hp[$r->key] = $r->value;
            }
        }

        $showHero          = ! isset($hp['hero.enabled']) || $hp['hero.enabled'] == '1';
        $showVideos        = ! isset($hp['videos.enabled']) || $hp['videos.enabled'] == '1';
        $showNews          = ! isset($hp['news.enabled']) || $hp['news.enabled'] == '1';
        $showStats         = ! isset($hp['stats.enabled']) || $hp['stats.enabled'] == '1';
        $showAnnouncements = ! isset($hp['announcements.enabled']) || $hp['announcements.enabled'] == '1';
        $showAgenda        = ! isset($hp['agenda.enabled']) || $hp['agenda.enabled'] == '1';
        $showPrograms      = ! isset($hp['programs.enabled']) || $hp['programs.enabled'] == '1';
        $showPartners      = ! isset($hp['partners.enabled']) || $hp['partners.enabled'] == '1';
        $showTestimonials  = ! isset($hp['testimonials.enabled']) || $hp['testimonials.enabled'] == '1';
        $showGallery       = ! isset($hp['gallery.enabled']) || $hp['gallery.enabled'] == '1';
        $showPpdb          = ! isset($hp['ppdb.enabled']) || $hp['ppdb.enabled'] == '1';

        $data = [
            'show_hero_section'          => $showHero,
            'show_video_section'         => $showVideos,
            'show_news_section'          => $showNews,
            'show_stats_section'         => $showStats,
            'show_announcements_section' => $showAnnouncements,
            'show_agenda_section'        => $showAgenda,
            'show_programs_section'      => $showPrograms,
            'show_partners_section'      => $showPartners,
            'show_testimonials_section'  => $showTestimonials,
            'show_gallery_section'       => $showGallery,
            'show_ppdb_section'          => $showPpdb,
        ];

        // Sliders
        $sliders = [];
        if ($db->tableExists('sliders')) {
            $sliderModel = new SliderModel();
            $sliders     = $sliderModel->where('status', 'active')->orderBy('order_num', 'ASC')->findAll();
        }
        $data['sliders'] = $sliders;

        // Latest Posts / News
        $latestPosts = [];
        if ($db->tableExists('posts')) {
            $postModel   = new PostModel();
            $latestPosts = $postModel->getPostsWithRelations(['posts.status' => 'published'], 6, 0);
        }
        $data['latest_posts'] = $latestPosts;

        // Featured Posts
        $featuredPosts = [];
        if ($db->tableExists('posts')) {
            $postModel     = new PostModel();
            $featuredPosts = $postModel->getPostsWithRelations(['posts.status' => 'published', 'posts.is_featured' => 1], 3, 0);
        }
        $data['featured_posts'] = $featuredPosts;

        // Announcements
        $announcements = [];
        if ($db->tableExists('announcements')) {
            $announcementModel = new AnnouncementModel();
            $announcements     = $announcementModel->where('status', 'published')->orderBy('created_at', 'DESC')->findAll(5);
        }
        $data['announcements'] = $announcements;

        // Agendas
        $agendas = [];
        if ($db->tableExists('agendas')) {
            $agendaModel = new AgendaModel();
            $agendas     = $agendaModel->where('status', 'published')->orderBy('start_date', 'ASC')->findAll(5);
        }
        $data['agendas'] = $agendas;

        // Galleries
        $galleries = [];
        if ($db->tableExists('galleries')) {
            $galleryModel = new GalleryModel();
            $galleries    = $galleryModel->orderBy('id', 'DESC')->findAll(6);
        }
        $data['galleries'] = $galleries;

        // Videos
        $videos = [];
        if ($db->tableExists('videos')) {
            $videoModel = new VideoModel();
            $videos     = $videoModel->where('status', 'published')->orderBy('id', 'DESC')->findAll(4);
        }
        $data['videos'] = $videos;

        // Testimonials
        $testimonials = [];
        if ($db->tableExists('testimonials')) {
            $testimonialModel = new TestimonialModel();
            $testimonials     = $testimonialModel->where('is_active', 1)->findAll(6);
        }
        $data['testimonials'] = $testimonials;

        // Partners
        $partners = [];
        if ($db->tableExists('school_partners')) {
            $partnerModel = new PartnerModel();
            $partners     = $partnerModel->orderBy('id', 'ASC')->findAll(12);
        }
        $data['partners'] = $partners;

        // Stats counts
        $teacherCount = 0;
        if ($db->tableExists('teachers')) {
            $teacherModel = new TeacherModel();
            $teacherCount = $teacherModel->where('status', 'active')->countAllResults();
        }
        $data['teacher_count'] = $teacherCount;

        $staffCount = 0;
        if ($db->tableExists('staff')) {
            $staffModel = new StaffModel();
            $staffCount = $staffModel->where('status', 'active')->countAllResults();
        }
        $data['staff_count'] = $staffCount;

        $data['title']    = site_name() . ' | Home';
        $data['seo_meta'] = $this->loadSeo('home', site_name() . ' - Official Website');

        return view('portal/home', $data);
    }

    protected function loadSeo(string $pageName, string $fallbackDescription = ''): array
    {
        $db  = \Config\Database::connect();
        $seo = $db->tableExists('seo_settings') ? $db->table('seo_settings')->where('page_name', $pageName)->get()->getRow() : null;

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

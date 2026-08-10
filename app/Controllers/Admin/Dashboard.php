<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        helper(['educms']);
        $db = \Config\Database::connect();

        $data = [
            'title'                 => 'Dashboard Utama',
            'breadcrumbs'           => ['Dashboard' => ''],
            'install_folder_exists' => is_dir(FCPATH . 'install'),
        ];

        // 1. Content Summary Counts
        $counts = [
            'posts'         => 0,
            'announcements' => 0,
            'agendas'       => 0,
            'teachers'      => 0,
            'staff'         => 0,
            'galleries'     => 0,
            'videos'        => 0,
            'testimonials'  => 0,
            'partners'      => 0,
            'ppdb'          => 0,
        ];

        if ($db->tableExists('posts')) {
            $counts['posts'] = (int) $db->table('posts')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('announcements')) {
            $counts['announcements'] = (int) $db->table('announcements')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('agendas')) {
            $counts['agendas'] = (int) $db->table('agendas')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('teachers')) {
            $counts['teachers'] = (int) $db->table('teachers')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('staff')) {
            $counts['staff'] = (int) $db->table('staff')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('galleries')) {
            $counts['galleries'] = (int) $db->table('galleries')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('videos')) {
            $counts['videos'] = (int) $db->table('videos')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('testimonials')) {
            $counts['testimonials'] = (int) $db->table('testimonials')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('school_partners')) {
            $counts['partners'] = (int) $db->table('school_partners')->where('deleted_at', null)->countAllResults();
        }
        if ($db->tableExists('ppdb_applicants')) {
            $counts['ppdb'] = (int) $db->table('ppdb_applicants')->where('deleted_at', null)->countAllResults();
        }

        $data['counts'] = $counts;

        // 2. Recent Activities (Activity Log)
        $recentActivities = [];
        if ($db->tableExists('activity_logs')) {
            $recentActivities = $db->table('activity_logs')
                ->select('activity_logs.*, users.full_name as user_name')
                ->join('users', 'activity_logs.user_id = users.id', 'left')
                ->orderBy('activity_logs.id', 'DESC')
                ->limit(8)
                ->get()
                ->getResult();
        }
        $data['recent_activities'] = $recentActivities;

        // 3. Website Health Status
        $healthItems = [];

        $logo          = site_logo();
        $healthItems[] = [
            'label'       => 'Logo Website',
            'status'      => ! empty($logo),
            'desc'        => ! empty($logo) ? 'Logo sekolah telah dikonfigurasi.' : 'Logo sekolah belum diunggah.',
            'action_url'  => 'admin/settings',
            'action_text' => ! empty($logo) ? 'Atur Logo' : 'Upload Logo',
            'btn_class'   => ! empty($logo) ? 'btn-outline-secondary' : 'btn-primary',
        ];

        $favicon       = site_favicon();
        $healthItems[] = [
            'label'       => 'Favicon Browser',
            'status'      => ! empty($favicon),
            'desc'        => ! empty($favicon) ? 'Favicon tab browser telah dikonfigurasi.' : 'Favicon belum diunggah.',
            'action_url'  => 'admin/settings',
            'action_text' => ! empty($favicon) ? 'Atur Favicon' : 'Upload Favicon',
            'btn_class'   => ! empty($favicon) ? 'btn-outline-secondary' : 'btn-primary',
        ];

        $activeTheme   = get_setting('theme', 'active_theme', 'default');
        $healthItems[] = [
            'label'       => 'Theme Website',
            'status'      => true,
            'desc'        => 'Template aktif: ' . ($activeTheme === 'islamic' ? 'EduCMS Islamic' : 'EduCMS Default'),
            'action_url'  => 'admin/theme-website',
            'action_text' => 'Kelola Tema',
            'btn_class'   => 'btn-outline-indigo',
        ];

        $smtpHost      = get_setting('smtp', 'smtp_host', '');
        $healthItems[] = [
            'label'       => 'Email (SMTP)',
            'status'      => ! empty($smtpHost),
            'desc'        => ! empty($smtpHost) ? 'Server email SMTP telah aktif.' : 'Pengiriman email SMTP belum dikonfigurasi.',
            'action_url'  => 'admin/settings',
            'action_text' => ! empty($smtpHost) ? 'Kelola SMTP' : 'Perbaiki SMTP',
            'btn_class'   => ! empty($smtpHost) ? 'btn-outline-secondary' : 'btn-warning',
        ];

        $waMsg         = get_setting('school', 'wa_default_message', '');
        $healthItems[] = [
            'label'       => 'Integrasi WhatsApp',
            'status'      => ! empty($waMsg),
            'desc'        => ! empty($waMsg) ? 'Pesan otomatis WA telah aktif.' : 'Pesan salam WA belum dikonfigurasi.',
            'action_url'  => 'admin/settings',
            'action_text' => ! empty($waMsg) ? 'Kelola WA' : 'Perbaiki WA',
            'btn_class'   => ! empty($waMsg) ? 'btn-outline-secondary' : 'btn-warning',
        ];

        $maps          = get_setting('school', 'maps_embed', '');
        $healthItems[] = [
            'label'       => 'Google Maps Embed',
            'status'      => ! empty($maps),
            'desc'        => ! empty($maps) ? 'Peta Google Maps lokasi sekolah telah terpasang.' : 'Tag Google Maps belum diisi.',
            'action_url'  => 'admin/settings',
            'action_text' => ! empty($maps) ? 'Edit Peta' : 'Edit Data Sekolah',
            'btn_class'   => ! empty($maps) ? 'btn-outline-secondary' : 'btn-warning',
        ];

        $healthItems[] = [
            'label'       => 'Galeri Video',
            'status'      => ($counts['videos'] > 0),
            'desc'        => ($counts['videos'] > 0) ? 'Terdapat ' . $counts['videos'] . ' video terpublikasi.' : 'Belum ada video dipublikasikan.',
            'action_url'  => ($counts['videos'] > 0) ? 'admin/videos' : 'admin/videos/create',
            'action_text' => ($counts['videos'] > 0) ? 'Kelola Video' : 'Tambah Video',
            'btn_class'   => ($counts['videos'] > 0) ? 'btn-outline-secondary' : 'btn-danger',
        ];

        $healthItems[] = [
            'label'       => 'Testimoni Orang Tua/Alumni',
            'status'      => ($counts['testimonials'] > 0),
            'desc'        => ($counts['testimonials'] > 0) ? 'Terdapat ' . $counts['testimonials'] . ' testimoni aktif.' : 'Belum ada testimoni dipublikasikan.',
            'action_url'  => ($counts['testimonials'] > 0) ? 'admin/testimonials' : 'admin/testimonials/create',
            'action_text' => ($counts['testimonials'] > 0) ? 'Kelola Testimoni' : 'Tambah Testimoni',
            'btn_class'   => ($counts['testimonials'] > 0) ? 'btn-outline-secondary' : 'btn-primary',
        ];

        $data['health_items'] = $healthItems;

        return view('admin/dashboard/index', $data);
    }
}

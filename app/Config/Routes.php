<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Portal\Home');
$routes->setDefaultMethod('index');

// Mirrors CI3 config/routes.php translate_uri_dashes = TRUE
$routes->setTranslateURIDashes(true);

// CI3 used automatic URI->controller/method routing for every admin CRUD
// module (admin/posts/create, admin/menu-groups, admin/videos/edit/3, ...).
// Enable legacy auto-routing so those URLs keep working without an explicit
// route per controller action.
$routes->setAutoRoute(false);

$routes->get('/', 'Portal\Home::index');

$routes->set404Override('App\Controllers\Main::error_404');

// -------------------------------------------------------------------------
// Auth Routes
// -------------------------------------------------------------------------
$routes->add('admin/login', 'Auth::login');
$routes->add('admin/logout', 'Auth::logout');
$routes->add('admin/forgot', 'Auth::forgot');

// -------------------------------------------------------------------------
// Admin Routes
// -------------------------------------------------------------------------

// Redirects module (Native CI4)
$routes->group('admin/redirects', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Redirects::index', ['filter' => 'perm:redirects.view']);
    $routes->match(['GET', 'POST'], 'create', 'Redirects::create', ['filter' => 'perm:redirects.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Redirects::edit/$1', ['filter' => 'perm:redirects.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Redirects::delete/$1', ['filter' => 'perm:redirects.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Redirects::restore/$1', ['filter' => 'perm:redirects.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Redirects::force_delete/$1', ['filter' => 'perm:redirects.manage']);
});

// Testimonials module (Native CI4)
$routes->group('admin/testimonials', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Testimonials::index', ['filter' => 'perm:testimonials.view']);
    $routes->match(['GET', 'POST'], 'create', 'Testimonials::create', ['filter' => 'perm:testimonials.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Testimonials::edit/$1', ['filter' => 'perm:testimonials.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Testimonials::delete/$1', ['filter' => 'perm:testimonials.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Testimonials::restore/$1', ['filter' => 'perm:testimonials.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Testimonials::force_delete/$1', ['filter' => 'perm:testimonials.manage']);
});

// Partners module (Native CI4)
$routes->group('admin/partners', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Partners::index', ['filter' => 'perm:partners.view']);
    $routes->match(['GET', 'POST'], 'create', 'Partners::create', ['filter' => 'perm:partners.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Partners::edit/$1', ['filter' => 'perm:partners.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Partners::delete/$1', ['filter' => 'perm:partners.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Partners::restore/$1', ['filter' => 'perm:partners.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Partners::force_delete/$1', ['filter' => 'perm:partners.manage']);
});

// Tags module (Native CI4) - shares posts permission prefix
$routes->group('admin/tags', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Tags::index', ['filter' => 'perm:posts.view']);
    $routes->match(['GET', 'POST'], 'create', 'Tags::create', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Tags::edit/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Tags::delete/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Tags::restore/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Tags::force_delete/$1', ['filter' => 'perm:posts.manage']);
});

// Categories module (Native CI4) - shares posts permission prefix
$routes->group('admin/categories', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Categories::index', ['filter' => 'perm:posts.view']);
    $routes->match(['GET', 'POST'], 'create', 'Categories::create', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Categories::edit/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Categories::delete/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Categories::restore/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Categories::force_delete/$1', ['filter' => 'perm:posts.manage']);
});

// Menu_groups module (Native CI4)
$routes->group('admin/menu-groups', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Menu_groups::index', ['filter' => 'perm:menus.view']);
    $routes->match(['GET', 'POST'], 'create', 'Menu_groups::create', ['filter' => 'perm:menus.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Menu_groups::edit/$1', ['filter' => 'perm:menus.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Menu_groups::delete/$1', ['filter' => 'perm:menus.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Menu_groups::restore/$1', ['filter' => 'perm:menus.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Menu_groups::force_delete/$1', ['filter' => 'perm:menus.manage']);
});

// Menus module (Native CI4) - granular menu.* permissions
$routes->group('admin/menus', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Menus::index', ['filter' => 'perm:menu.view']);
    $routes->match(['GET', 'POST'], 'create', 'Menus::create', ['filter' => 'perm:menu.create']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Menus::edit/$1', ['filter' => 'perm:menu.edit']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Menus::delete/$1', ['filter' => 'perm:menu.delete']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Menus::restore/$1', ['filter' => 'perm:menu.restore']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Menus::force_delete/$1', ['filter' => 'perm:menu.delete']);
    $routes->get('toggle_status/(:num)', 'Menus::toggle_status/$1', ['filter' => 'perm:menu.edit']);
    $routes->post('save_order', 'Menus::save_order', ['filter' => 'perm:menu.edit']);
});

// Achievements module (Native CI4)
$routes->group('admin/achievements', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Achievements::index', ['filter' => 'perm:achievements.view']);
    $routes->match(['GET', 'POST'], 'create', 'Achievements::create', ['filter' => 'perm:achievements.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Achievements::edit/$1', ['filter' => 'perm:achievements.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Achievements::delete/$1', ['filter' => 'perm:achievements.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Achievements::restore/$1', ['filter' => 'perm:achievements.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Achievements::force_delete/$1', ['filter' => 'perm:achievements.manage']);
});

// Agendas module (Native CI4)
$routes->group('admin/agendas', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Agendas::index', ['filter' => 'perm:agendas.view']);
    $routes->match(['GET', 'POST'], 'create', 'Agendas::create', ['filter' => 'perm:agendas.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Agendas::edit/$1', ['filter' => 'perm:agendas.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Agendas::delete/$1', ['filter' => 'perm:agendas.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Agendas::restore/$1', ['filter' => 'perm:agendas.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Agendas::force_delete/$1', ['filter' => 'perm:agendas.manage']);
});

// Announcements module (Native CI4)
$routes->group('admin/announcements', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Announcements::index', ['filter' => 'perm:announcements.view']);
    $routes->match(['GET', 'POST'], 'create', 'Announcements::create', ['filter' => 'perm:announcements.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Announcements::edit/$1', ['filter' => 'perm:announcements.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Announcements::delete/$1', ['filter' => 'perm:announcements.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Announcements::restore/$1', ['filter' => 'perm:announcements.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Announcements::force_delete/$1', ['filter' => 'perm:announcements.manage']);
});

// Extracurriculars module (Native CI4)
$routes->group('admin/extracurriculars', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Extracurriculars::index', ['filter' => 'perm:extracurriculars.view']);
    $routes->match(['GET', 'POST'], 'create', 'Extracurriculars::create', ['filter' => 'perm:extracurriculars.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Extracurriculars::edit/$1', ['filter' => 'perm:extracurriculars.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Extracurriculars::delete/$1', ['filter' => 'perm:extracurriculars.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Extracurriculars::restore/$1', ['filter' => 'perm:extracurriculars.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Extracurriculars::force_delete/$1', ['filter' => 'perm:extracurriculars.manage']);
});

// Staff module (Native CI4)
$routes->group('admin/staff', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Staff::index', ['filter' => 'perm:staff.view']);
    $routes->match(['GET', 'POST'], 'create', 'Staff::create', ['filter' => 'perm:staff.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Staff::edit/$1', ['filter' => 'perm:staff.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Staff::delete/$1', ['filter' => 'perm:staff.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Staff::restore/$1', ['filter' => 'perm:staff.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Staff::force_delete/$1', ['filter' => 'perm:staff.manage']);
});

// Teachers module (Native CI4)
$routes->group('admin/teachers', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Teachers::index', ['filter' => 'perm:teachers.view']);
    $routes->match(['GET', 'POST'], 'create', 'Teachers::create', ['filter' => 'perm:teachers.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Teachers::edit/$1', ['filter' => 'perm:teachers.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Teachers::delete/$1', ['filter' => 'perm:teachers.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Teachers::restore/$1', ['filter' => 'perm:teachers.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Teachers::force_delete/$1', ['filter' => 'perm:teachers.manage']);
});

// Videos module (Native CI4)
$routes->group('admin/videos', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Videos::index', ['filter' => 'perm:videos.view']);
    $routes->match(['GET', 'POST'], 'create', 'Videos::create', ['filter' => 'perm:videos.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Videos::edit/$1', ['filter' => 'perm:videos.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Videos::delete/$1', ['filter' => 'perm:videos.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Videos::restore/$1', ['filter' => 'perm:videos.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Videos::force_delete/$1', ['filter' => 'perm:videos.manage']);
});

// Galleries module (Native CI4)
$routes->group('admin/galleries', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Galleries::index', ['filter' => 'perm:gallery.view']);
    $routes->match(['GET', 'POST'], 'create', 'Galleries::create', ['filter' => 'perm:gallery.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Galleries::edit/$1', ['filter' => 'perm:gallery.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Galleries::delete/$1', ['filter' => 'perm:gallery.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Galleries::restore/$1', ['filter' => 'perm:gallery.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Galleries::force_delete/$1', ['filter' => 'perm:gallery.manage']);
    $routes->get('items/(:num)', 'Galleries::items/$1', ['filter' => 'perm:gallery.view']);
    $routes->post('add_item/(:num)', 'Galleries::add_item/$1', ['filter' => 'perm:gallery.manage']);
    $routes->match(['GET', 'POST'], 'delete_item/(:num)', 'Galleries::delete_item/$1', ['filter' => 'perm:gallery.manage']);
});

// Posts module (Native CI4)
$routes->group('admin/posts', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Posts::index', ['filter' => 'perm:posts.view']);
    $routes->match(['GET', 'POST'], 'create', 'Posts::create', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Posts::edit/$1', ['filter' => 'perm:posts.manage']);
    $routes->get('preview/(:num)', 'Posts::preview/$1', ['filter' => 'perm:posts.view']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Posts::delete/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Posts::restore/$1', ['filter' => 'perm:posts.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Posts::force_delete/$1', ['filter' => 'perm:posts.manage']);
});

// Pages module (Native CI4)
$routes->group('admin/pages', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Pages::index', ['filter' => 'perm:pages.view']);
    $routes->match(['GET', 'POST'], 'create', 'Pages::create', ['filter' => 'perm:pages.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Pages::edit/$1', ['filter' => 'perm:pages.manage']);
    $routes->get('preview/(:num)', 'Pages::preview/$1', ['filter' => 'perm:pages.view']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Pages::delete/$1', ['filter' => 'perm:pages.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Pages::restore/$1', ['filter' => 'perm:pages.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Pages::force_delete/$1', ['filter' => 'perm:pages.manage']);
});

// PPDB module (Native CI4)
$routes->group('admin/ppdb', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Ppdb::index', ['filter' => 'perm:ppdb.view']);
    $routes->get('view/(:num)', 'Ppdb::view/$1', ['filter' => 'perm:ppdb.view']);
    $routes->post('update_status/(:num)', 'Ppdb::update_status/$1', ['filter' => 'perm:ppdb.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Ppdb::delete/$1', ['filter' => 'perm:ppdb.manage']);
});

// Sliders module (Native CI4)
$routes->group('admin/sliders', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Sliders::index', ['filter' => 'perm:sliders.view']);
    $routes->match(['GET', 'POST'], 'create', 'Sliders::create', ['filter' => 'perm:sliders.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Sliders::edit/$1', ['filter' => 'perm:sliders.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Sliders::delete/$1', ['filter' => 'perm:sliders.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Sliders::restore/$1', ['filter' => 'perm:sliders.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Sliders::force_delete/$1', ['filter' => 'perm:sliders.manage']);
});

// Media Library module (Native CI4)
$routes->group('admin/media', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Media::index', ['filter' => 'perm:media.view']);
    $routes->post('upload', 'Media::upload', ['filter' => 'perm:media.manage']);
    $routes->post('drop_upload', 'Media::drop_upload', ['filter' => 'perm:media.manage']);
    $routes->post('editor_upload', 'Media::editor_upload', ['filter' => 'perm:media.manage']);
    $routes->get('picker', 'Media::picker', ['filter' => 'perm:media.view']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Media::delete/$1', ['filter' => 'perm:media.manage']);
    $routes->post('bulk_delete', 'Media::bulk_delete', ['filter' => 'perm:media.manage']);
});

// Users module (Native CI4)
$routes->group('admin/users', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Users::index', ['filter' => 'perm:users.view']);
    $routes->match(['GET', 'POST'], 'create', 'Users::create', ['filter' => 'perm:users.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Users::edit/$1', ['filter' => 'perm:users.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Users::delete/$1', ['filter' => 'perm:users.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Users::restore/$1', ['filter' => 'perm:users.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Users::force_delete/$1', ['filter' => 'perm:users.manage']);
});

// Roles module (Native CI4)
$routes->group('admin/roles', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Roles::index', ['filter' => 'perm:roles.view']);
    $routes->match(['GET', 'POST'], 'create', 'Roles::create', ['filter' => 'perm:roles.manage']);
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'Roles::edit/$1', ['filter' => 'perm:roles.manage']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Roles::delete/$1', ['filter' => 'perm:roles.manage']);
    $routes->match(['GET', 'POST'], 'restore/(:num)', 'Roles::restore/$1', ['filter' => 'perm:roles.manage']);
    $routes->match(['GET', 'POST'], 'force_delete/(:num)', 'Roles::force_delete/$1', ['filter' => 'perm:roles.manage']);
});

// Settings module (Native CI4)
$routes->group('admin/settings', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Settings::index', ['filter' => 'perm:settings.view']);
    $routes->post('save', 'Settings::save', ['filter' => 'perm:settings.manage']);
});

// Messages module (Native CI4)
$routes->group('admin/messages', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Messages::index', ['filter' => 'perm:messages.view']);
    $routes->get('view/(:num)', 'Messages::view/$1', ['filter' => 'perm:messages.view']);
    $routes->match(['GET', 'POST'], 'delete/(:num)', 'Messages::delete/$1', ['filter' => 'perm:messages.manage']);
});

// Dashboard module (Native CI4)
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index', ['filter' => 'perm:dashboard.view']);
    $routes->get('dashboard', 'Dashboard::index', ['filter' => 'perm:dashboard.view']);
});

// Backup / Database Manager module (Native CI4)
$routes->group('admin/backup', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Backup::index', ['filter' => 'perm:settings.manage']);
    $routes->get('run', 'Backup::run', ['filter' => 'perm:settings.manage']);
    $routes->post('restore', 'Backup::restore', ['filter' => 'perm:settings.manage']);
});

// Logs module (Native CI4)
$routes->group('admin/logs', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Logs::index', ['filter' => 'perm:settings.manage']);
    $routes->match(['GET', 'POST'], 'clear', 'Logs::clear', ['filter' => 'perm:settings.manage']);
});

// System Upgrade module (Native CI4)
$routes->group('admin/system-upgrade', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'System_upgrade::index', ['filter' => 'perm:settings.manage']);
    $routes->post('run', 'System_upgrade::run', ['filter' => 'perm:settings.manage']);
    $routes->post('repair_homepage', 'System_upgrade::repair_homepage', ['filter' => 'perm:settings.manage']);
});

// Theme Website module (Native CI4)
$routes->group('admin/theme-website', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Theme_website::index', ['filter' => 'perm:settings.view']);
    $routes->post('save', 'Theme_website::save', ['filter' => 'perm:settings.manage']);
});

// Styleguide module (Native CI4)
$routes->get('admin/styleguide', 'Admin\Styleguide::index', ['filter' => 'perm:settings.manage']);

// -------------------------------------------------------------------------
// Public Portal Routes (mirrors CI3 config/routes.php)
// -------------------------------------------------------------------------
$routes->add('pages/(:any)', 'Portal\Pages::detail/$1');
$routes->add('page/(:any)', 'Portal\Pages::detail/$1');
$routes->add('guru-staff', 'Portal\Guru::index');
$routes->add('guru-staff/(:any)', 'Portal\Guru::detail/$1');
$routes->add('guru', 'Portal\Guru::index');
$routes->add('guru/(:num)', 'Portal\Guru::detail/$1');
$routes->add('staff', 'Portal\Staff::index');
$routes->add('staff/(:num)', 'Portal\Staff::detail/$1');
$routes->add('prestasi', 'Portal\Prestasi::index');
$routes->add('prestasi/(:any)', 'Portal\Prestasi::detail/$1');
$routes->add('ekstrakurikuler', 'Portal\Ekstrakurikuler::index');
$routes->add('ekstrakurikuler/(:any)', 'Portal\Ekstrakurikuler::detail/$1');
$routes->add('berita', 'Portal\News::index');
$routes->add('berita/(:any)', 'Portal\News::detail/$1');
$routes->add('posts', 'Portal\News::index');
$routes->add('posts/(:any)', 'Portal\News::detail/$1');
$routes->add('pengumuman', 'Portal\Announcement::index');
$routes->add('pengumuman/(:any)', 'Portal\Announcement::detail/$1');
$routes->add('agenda', 'Portal\Agenda::index');
$routes->add('agenda/(:any)', 'Portal\Agenda::detail/$1');
$routes->add('galeri-foto', 'Portal\Gallery::photo');
$routes->add('galeri-foto/(:any)', 'Portal\Gallery::photo_detail/$1');
$routes->add('galeri-video', 'Portal\Gallery::video');
$routes->add('galeri-video/(:any)', 'Portal\Gallery::video_detail/$1');
// RC5-005: Modul Video (external URL/embed content, distinct from Galeri Video albums above)
$routes->add('video', 'Portal\Video::index');
$routes->add('video/(:any)', 'Portal\Video::detail/$1');
$routes->add('ppdb', 'Portal\Ppdb::index');
$routes->add('ppdb/status', 'Portal\Ppdb::status');
$routes->add('kontak', 'Portal\Contact::index');
$routes->add('contact', 'Portal\Contact::index');

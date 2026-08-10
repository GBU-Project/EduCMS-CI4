<?php
$active_segment = $this->uri->segment(2);
if (empty($active_segment)) {
    $active_segment = 'dashboard';
}

// 1. Define Dynamic Sidebar Structure
$admin_sidebar_menu = array(
    'dashboard' => array(
        'title' => 'Dashboard',
        'url' => 'admin',
        'icon' => 'gauge-high',
        'segment' => 'dashboard'
    ),
    'cms_header' => array('header' => 'CMS UTAMA'),
    'pages' => array(
        'title' => 'Halaman Statis',
        'url' => 'admin/pages',
        'icon' => 'file-lines',
        'segment' => 'pages'
    ),
    'posts' => array(
        'title' => 'Berita / Artikel',
        'url' => 'admin/posts',
        'icon' => 'newspaper',
        'segment' => array('posts', 'categories', 'tags')
    ),
    'sliders' => array(
        'title' => 'Slider Beranda',
        'url' => 'admin/sliders',
        'icon' => 'images',
        'segment' => 'sliders'
    ),
    'menu-groups' => array(
        'title' => 'Menu Builder',
        'url' => 'admin/menu-groups',
        'icon' => 'bars-staggered',
        'segment' => 'menu-groups'
    ),
    'media' => array(
        'title' => 'Media Library',
        'url' => 'admin/media',
        'icon' => 'photo-film',
        'segment' => 'media'
    ),
    'videos' => array(
        'title' => 'Video',
        'url' => 'admin/videos',
        'icon' => 'video',
        'segment' => 'videos'
    ),
    'galleries' => array(
        'title' => 'Galeri',
        'url' => 'admin/galleries',
        'icon' => 'images',
        'segment' => 'galleries'
    ),
    'partners' => array(
        'title' => 'Mitra',
        'url' => 'admin/partners',
        'icon' => 'handshake',
        'segment' => 'partners'
    ),
    'testimonials' => array(
        'title' => 'Testimoni',
        'url' => 'admin/testimonials',
        'icon' => 'quote-left',
        'segment' => 'testimonials'
    ),
    'academic_header' => array('header' => 'AKADEMIK & KEGIATAN'),
    'teachers' => array(
        'title' => 'Direktori Guru',
        'url' => 'admin/teachers',
        'icon' => 'chalkboard-user',
        'segment' => 'teachers'
    ),
    'staff' => array(
        'title' => 'Direktori Staf',
        'url' => 'admin/staff',
        'icon' => 'id-card',
        'segment' => 'staff'
    ),
    'achievements' => array(
        'title' => 'Prestasi Sekolah',
        'url' => 'admin/achievements',
        'icon' => 'trophy',
        'segment' => 'achievements'
    ),
    'extracurriculars' => array(
        'title' => 'Ekstrakurikuler',
        'url' => 'admin/extracurriculars',
        'icon' => 'volleyball',
        'segment' => 'extracurriculars'
    ),
    'communication_header' => array('header' => 'KOMUNIKASI'),
    'announcements' => array(
        'title' => 'Pengumuman',
        'url' => 'admin/announcements',
        'icon' => 'bullhorn',
        'segment' => 'announcements'
    ),
    'agendas' => array(
        'title' => 'Agenda Kegiatan',
        'url' => 'admin/agendas',
        'icon' => 'calendar-days',
        'segment' => 'agendas'
    ),
    'messages' => array(
        'title' => 'Pesan Kontak',
        'url' => 'admin/messages',
        'icon' => 'envelope-open-text',
        'segment' => 'messages'
    ),
    'ppdb_header' => array('header' => 'PENDAFTARAN'),
    'ppdb' => array(
        'title' => 'PPDB Online',
        'url' => 'admin/ppdb',
        'icon' => 'user-plus',
        'segment' => 'ppdb'
    ),
    'system_header' => array('header' => 'PENGATURAN SISTEM'),
    'users' => array(
        'title' => 'Kelola Pengguna',
        'url' => 'admin/users',
        'icon' => 'users-gear',
        'segment' => 'users'
    ),
    'roles' => array(
        'title' => 'Hak Akses (RBAC)',
        'url' => 'admin/roles',
        'icon' => 'shield-halved',
        'segment' => 'roles'
    ),
    'redirects' => array(
        'title' => 'URL Redirects',
        'url' => 'admin/redirects',
        'icon' => 'route',
        'segment' => 'redirects'
    ),
    'settings' => array(
        'title' => 'Setelan Website',
        'url' => 'admin/settings',
        'icon' => 'sliders',
        'segment' => 'settings'
    ),
    'backup' => array(
        'title' => 'Database Manager',
        'url' => 'admin/backup',
        'icon' => 'database',
        'segment' => 'backup'
    ),
    'system_upgrade' => array(
        'title' => 'Upgrade Database',
        'url' => 'admin/system-upgrade',
        'icon' => 'arrows-rotate',
        'segment' => 'system-upgrade'
    ),
    'logs' => array(
        'title' => 'Log Aktivitas',
        'url' => 'admin/logs',
        'icon' => 'clock-rotate-left',
        'segment' => 'logs'
    )
);

// 2. RC5-010: Role-gated menu items, inserted into the base structure above
// rather than duplicated per-role. Uses the existing Rbac helper only —
// no role name is hardcoded here, consistent with System_upgrade/Backup.
//   - UI Styleguide  = Developer Playground -> Super Admin only.
//   - Theme Website  = Administrator feature -> gated by the same
//     'settings.view' permission that already separates Administrator
//     from Editor for Setelan Website (see install/sql/seeders.sql).
$insert_menu_after = function (&$menu, $after_key, $new_key, $new_item) {
    $keys = array_keys($menu);
    $pos = array_search($after_key, $keys, TRUE);
    if ($pos === FALSE) {
        $menu[$new_key] = $new_item;
        return;
    }
    $menu = array_slice($menu, 0, $pos + 1, TRUE)
          + array($new_key => $new_item)
          + array_slice($menu, $pos + 1, NULL, TRUE);
};

if ($this->rbac->is_super_admin($current_user->id)) {
    $insert_menu_after($admin_sidebar_menu, 'dashboard', 'styleguide', array(
        'title' => 'UI Styleguide',
        'url' => 'admin/styleguide',
        'icon' => 'palette',
        'segment' => 'styleguide',
        'icon_color' => 'text-indigo'
    ));
}

if ($this->rbac->has_permission($current_user->id, 'settings.view')) {
    $insert_menu_after($admin_sidebar_menu, 'settings', 'theme_website', array(
        'title' => 'Theme Website',
        'url' => 'admin/theme-website',
        'icon' => 'swatchbook',
        'segment' => 'theme-website'
    ));
}
?>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo base_url('admin'); ?>" class="brand-link">
      <?php if (site_logo()): ?>
        <img src="<?php echo esc_attr(site_logo()); ?>" alt="<?php echo esc_attr(site_name()); ?>" class="brand-image ml-3 mr-2" style="max-height:33px;width:auto;object-fit:contain;">
      <?php else: ?>
        <i class="fa-solid fa-graduation-cap brand-image text-indigo fa-lg ml-3 mr-2" style="color: #818cf8 !important;"></i>
      <?php endif; ?>
      <span class="brand-text"><?php echo esc_html(site_name()); ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-3">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <?php foreach ($admin_sidebar_menu as $key => $item): ?>
            <?php if (isset($item['header'])): ?>
              <li class="nav-header text-xs text-secondary-50 font-weight-bold tracking-wider px-3 mt-3 mb-1"><?php echo esc_html($item['header']); ?></li>
            <?php else: ?>
              <?php 
                $is_active = FALSE;
                if (is_array($item['segment'])) {
                    $is_active = in_array($active_segment, $item['segment']);
                } else {
                    $is_active = ($active_segment === $item['segment']);
                }
                
                $icon_color = isset($item['icon_color']) ? $item['icon_color'] : '';
              ?>
              <li class="nav-item">
                <a href="<?php echo base_url($item['url']); ?>" class="nav-link <?php echo $is_active ? 'active' : ''; ?>">
                  <?php echo render_icon($item['icon'], 'nav-icon ' . $icon_color); ?>
                  <p><?php echo esc_html($item['title']); ?></p>
                </a>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <?php if (!empty($pending_migrations_count) && $pending_migrations_count > 0): ?>
    <div class="alert alert-warning m-3 d-flex align-items-center justify-content-between flex-wrap" style="border-radius:12px;border-left:4px solid #f59e0b;">
        <div class="d-flex align-items-center">
            <i class="fas fa-database mr-2"></i>
            <span>Ada <strong><?php echo (int) $pending_migrations_count; ?> pembaruan database</strong> yang belum diterapkan. Sistem akan menerapkannya secara otomatis.</span>
        </div>
        <a href="<?php echo base_url('admin/system-upgrade'); ?>" class="btn btn-sm btn-warning mt-2 mt-sm-0">Upgrade Database Sekarang</a>
    </div>
    <?php endif; ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <?php echo generate_breadcrumb($breadcrumbs); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($install_folder_exists) && isset($current_user) && $this->rbac->is_super_admin($current_user->id)): ?>
<div class="container-fluid">
    <div class="alert alert-danger d-flex align-items-start" role="alert">
        <?php echo render_icon('triangle-exclamation', 'mr-2 mt-1'); ?>
        <div>
            <strong>Folder <code>install/</code> masih ada di server.</strong>
            Folder ini berisi skrip installer dan kredensial default awal. Sebuah <code>.htaccess</code> penolakan akses
            otomatis sudah ditambahkan, tetapi opsi paling aman adalah <strong>menghapus seluruh folder <code>install/</code>
            dari server</strong> sekarang bahwa instalasi sudah selesai.
        </div>
    </div>
</div>
<?php endif; ?>

<section class="content">
    <div class="container-fluid">

        <!-- 1. Welcome Banner -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;background:linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);color:#ffffff;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge badge-light text-indigo font-weight-bold mb-2 px-3 py-1" style="border-radius:20px;">Selamat Datang</span>
                        <h2 class="font-weight-bold text-white mb-2" style="letter-spacing:-0.5px;">
                            <?php echo esc_html($current_user->full_name); ?>
                        </h2>
                        <p class="text-white-50 mb-0" style="font-size:0.95rem;line-height:1.6;">
                            Anda berada di panel kontrol administrasi resmi <strong><?php echo esc_html(site_name()); ?></strong>. Kelola publikasi artikel, pengumuman, data sekolah, dan konfigurasi tampilan dengan mudah.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <a href="<?php echo base_url(); ?>" target="_blank" class="btn btn-light btn-lg px-4 shadow-sm font-weight-bold" style="border-radius:12px;color:#3730a3;">
                            <?php echo render_icon('up-right-from-square', 'mr-2'); ?> Lihat Website
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Quick Actions (Akses Cepat Operator) -->
        <div class="card card-outline card-indigo shadow-sm mb-4" style="border-radius:12px;">
            <div class="card-header border-0 py-3">
                <h3 class="card-title font-weight-bold text-dark mb-0">
                    <?php echo render_icon('bolt', 'mr-2 text-warning'); ?> Akses Cepat (Quick Actions)
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="row text-center">
                    <?php
                    $quick_actions = array(
                        array('label' => 'Tambah Berita',     'icon' => 'newspaper',     'url' => 'admin/posts/create',        'color' => 'btn-outline-primary'),
                        array('label' => 'Tambah Pengumuman', 'icon' => 'bullhorn',      'url' => 'admin/announcements/create','color' => 'btn-outline-info'),
                        array('label' => 'Tambah Agenda',     'icon' => 'calendar-plus', 'url' => 'admin/agendas/create',      'color' => 'btn-outline-warning'),
                        array('label' => 'Upload Galeri',     'icon' => 'images',        'url' => 'admin/galleries/create',    'color' => 'btn-outline-success'),
                        array('label' => 'Upload Video',      'icon' => 'video',         'url' => 'admin/videos/create',       'color' => 'btn-outline-danger'),
                        array('label' => 'Media Library',     'icon' => 'folder-open',   'url' => 'admin/media',               'color' => 'btn-outline-secondary'),
                        array('label' => 'Homepage Manager',  'icon' => 'house-laptop',  'url' => 'admin/settings',            'color' => 'btn-outline-indigo'),
                        array('label' => 'Setelan Website',   'icon' => 'gear',          'url' => 'admin/settings',            'color' => 'btn-outline-dark'),
                        array('label' => 'Backup Database',   'icon' => 'database',      'url' => 'admin/backup',              'color' => 'btn-outline-secondary'),
                    );
                    foreach ($quick_actions as $qa):
                    ?>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-2 px-1">
                            <a href="<?php echo base_url($qa['url']); ?>" class="btn <?php echo $qa['color']; ?> btn-block py-2 text-truncate" style="border-radius:10px;font-size:0.82rem;font-weight:600;" title="<?php echo esc_attr($qa['label']); ?>">
                                <?php echo render_icon($qa['icon'], 'd-block mb-1 fa-lg'); ?>
                                <span><?php echo esc_html($qa['label']); ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 3. Content Summary Grid -->
        <h5 class="font-weight-bold text-dark mb-3">
            <?php echo render_icon('chart-pie', 'mr-2 text-indigo'); ?> Ringkasan Konten & Data Sekolah
        </h5>
        <div class="row">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-indigo elevation-0" style="border-radius:8px;background-color:#4f46e5!important;"><i class="fa-solid fa-newspaper text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Berita</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['posts']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-info elevation-0" style="border-radius:8px;background-color:#0ea5e9!important;"><i class="fa-solid fa-bullhorn text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Pengumuman</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['announcements']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-warning elevation-0" style="border-radius:8px;background-color:#f59e0b!important;"><i class="fa-solid fa-calendar-days text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Agenda</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['agendas']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-success elevation-0" style="border-radius:8px;background-color:#10b981!important;"><i class="fa-solid fa-chalkboard-user text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Guru</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['teachers']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-secondary elevation-0" style="border-radius:8px;"><i class="fa-solid fa-user-tie text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Staff</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['staff']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-teal elevation-0" style="border-radius:8px;background-color:#14b8a6!important;"><i class="fa-solid fa-images text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Galeri</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['galleries']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-danger elevation-0" style="border-radius:8px;background-color:#ef4444!important;"><i class="fa-solid fa-video text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Video</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['videos']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-purple elevation-0" style="border-radius:8px;background-color:#8b5cf6!important;"><i class="fa-solid fa-quote-left text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Testimoni</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['testimonials']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-pink elevation-0" style="border-radius:8px;background-color:#ec4899!important;"><i class="fa-solid fa-handshake text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">Mitra</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['partners']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="info-box shadow-sm border-0 h-100" style="border-radius:12px;">
                    <span class="info-box-icon bg-primary elevation-0" style="border-radius:8px;"><i class="fa-solid fa-user-plus text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-secondary text-xs font-weight-medium">PPDB</span>
                        <span class="info-box-number text-dark font-weight-bold" style="font-size:1.2rem;"><?php echo (int) $counts['ppdb']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Website Health & Recent Activities -->
        <div class="row mt-2">
            <!-- Left Column: Website Health Check -->
            <div class="col-lg-7 mb-4">
                <div class="card card-outline card-indigo shadow-sm h-100" style="border-radius:12px;">
                    <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between">
                        <h3 class="card-title font-weight-bold text-dark mb-0">
                            <?php echo render_icon('heart-pulse', 'mr-2 text-danger'); ?> Status Kesehatan Website (Website Health)
                        </h3>
                        <span class="badge badge-light text-secondary font-weight-medium">Internal Audit</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <tbody>
                                    <?php foreach ($health_items as $h): ?>
                                    <tr>
                                        <td style="width:40px;" class="text-center align-middle">
                                            <?php if ($h['status']): ?>
                                                <i class="fa-solid fa-circle-check text-success fa-lg" title="Valid / Siap"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-triangle-exclamation text-warning fa-lg" title="Perlu Perhatian"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <strong class="text-dark d-block" style="font-size:0.9rem;"><?php echo esc_html($h['label']); ?></strong>
                                            <span class="text-muted small"><?php echo esc_html($h['desc']); ?></span>
                                        </td>
                                        <td style="width:140px;" class="text-right align-middle">
                                            <a href="<?php echo base_url($h['action_url']); ?>" class="btn btn-xs <?php echo $h['btn_class']; ?> font-weight-bold shadow-xs px-2.5 py-1" style="border-radius:6px;font-size:0.75rem;">
                                                <?php echo esc_html($h['action_text']); ?> &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Recent Activities & System Info -->
            <div class="col-lg-5 mb-4 space-y-4">
                <!-- System Info Card -->
                <div class="card card-outline card-secondary shadow-sm mb-4" style="border-radius:12px;">
                    <div class="card-header border-0 py-3">
                        <h3 class="card-title font-weight-bold text-dark mb-0">
                            <?php echo render_icon('server', 'mr-2 text-info'); ?> Informasi Sistem
                        </h3>
                    </div>
                    <div class="card-body py-2 px-3 small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary">Versi EduCMS</span>
                            <span class="font-weight-bold text-dark">v1.2 (LOCKED)</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary">Framework</span>
                            <span class="font-weight-bold text-dark">CodeIgniter <?php echo CI_VERSION; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-secondary">Versi PHP</span>
                            <span class="font-weight-bold text-dark"><?php echo phpversion(); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-secondary">Template Aktif</span>
                            <span class="badge badge-indigo" style="background:#6366f1;color:#fff;"><?php echo get_setting('theme', 'active_theme', 'default') === 'islamic' ? 'EduCMS Islamic' : 'EduCMS Default'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Activity Log Card -->
                <div class="card card-outline card-primary shadow-sm" style="border-radius:12px;">
                    <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between">
                        <h3 class="card-title font-weight-bold text-dark mb-0">
                            <?php echo render_icon('clock-rotate-left', 'mr-2 text-primary'); ?> Aktivitas Terbaru
                        </h3>
                        <a href="<?php echo base_url('admin/logs'); ?>" class="small font-weight-bold text-indigo">Semua Log &rarr;</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($recent_activities)): ?>
                            <ul class="list-group list-group-flush small mb-0">
                                <?php foreach ($recent_activities as $act): ?>
                                <li class="list-group-item py-2 px-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-dark"><?php echo esc_html($act->user_name ?: 'Sistem'); ?></strong>
                                        <span class="text-muted text-xs"><?php echo date('d M H:i', strtotime($act->created_at)); ?></span>
                                    </div>
                                    <div class="text-secondary mt-1">
                                        <span class="badge badge-light border text-dark"><?php echo esc_html($act->module); ?></span>
                                        <span class="ml-1"><?php echo esc_html($act->action); ?></span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="p-3 text-center text-muted small">Belum ada catatan aktivitas terbaru.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

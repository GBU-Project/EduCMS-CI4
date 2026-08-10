<?php echo view('portal/partials/header', $data ?? []); ?>

<?php
// ================================================================
// RC07-001: Dynamic Section Rendering (Default Theme)
// ================================================================
// If $homepage_sections is available (RC07+), iterate sections in
// the order determined by admin settings. Otherwise, fall back to
// the original hardcoded order for backward compatibility.

/**
 * Helper: render a specific default-theme section by key.
 * Each case outputs the exact same HTML that was previously
 * hardcoded in the original home.php, preserving pixel-perfect
 * backward compatibility.
 */
if (!empty($homepage_sections)):
    $main_opened = FALSE;
    foreach ($homepage_sections as $sec):
        if (empty($sec['enabled'])) continue;
        switch ($sec['key']):

            // ============================================================
            // HERO / SLIDER
            // ============================================================
            case 'hero':
                if (!empty($show_hero_section)):
                    if (!empty($sliders)):
?>
<div class="relative bg-slate-900 text-white overflow-hidden" x-data="{ active: 0 }" id="hero-slider" data-total="<?php echo count($sliders); ?>">
    <div class="relative h-[420px] lg:h-[560px]">
        <?php foreach ($sliders as $i => $slide): ?>
        <div class="slider-slide absolute inset-0 transition-opacity duration-700 <?php echo $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>" data-slide-index="<?php echo $i; ?>">
            <div class="absolute inset-0">
                <img src="<?php echo base_url($slide->image); ?>" alt="<?php echo esc_attr($slide->title); ?>" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/60 to-transparent"></div>
            </div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
                <div class="max-w-2xl space-y-5">
                    <?php if (!empty($slide->title)): ?>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight"><?php echo esc_html($slide->title); ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($slide->subtitle)): ?>
                        <p class="text-slate-200 text-lg"><?php echo esc_html($slide->subtitle); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($slide->link)): ?>
                        <a href="<?php echo resolve_menu_url($slide->link); ?>" class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium shadow-xl shadow-indigo-900/40 transition">
                            <span>Selengkapnya</span>
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($sliders) > 1): ?>
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex space-x-2">
        <?php foreach ($sliders as $i => $slide): ?>
            <button type="button" class="slider-dot w-2.5 h-2.5 rounded-full transition <?php echo $i === 0 ? 'bg-white' : 'bg-white/40'; ?>" data-goto="<?php echo $i; ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<script>
(function () {
    var slides = document.querySelectorAll('#hero-slider .slider-slide');
    var dots = document.querySelectorAll('#hero-slider .slider-dot');
    if (slides.length < 2) return;
    var current = 0;
    function show(index) {
        slides.forEach(function (s, i) {
            s.classList.toggle('opacity-100', i === index);
            s.classList.toggle('z-10', i === index);
            s.classList.toggle('opacity-0', i !== index);
            s.classList.toggle('z-0', i !== index);
        });
        dots.forEach(function (d, i) {
            d.classList.toggle('bg-white', i === index);
            d.classList.toggle('bg-white/40', i !== index);
        });
        current = index;
    }
    dots.forEach(function (dot) {
        dot.addEventListener('click', function () { show(parseInt(dot.getAttribute('data-goto'), 10)); });
    });
    setInterval(function () { show((current + 1) % slides.length); }, 6000);
})();
</script>
<?php else: ?>
<!-- Elegant placeholder hero -->
<div class="relative bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white overflow-hidden py-20 lg:py-32">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                    <?php echo !empty($hp_hero_title) ? esc_html($hp_hero_title) : 'Selamat Datang'; ?>
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-none">
                    Membangun Generasi <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Unggul &amp; Berkarakter</span>
                </h1>
                <p class="text-slate-300 text-lg max-w-2xl mx-auto lg:mx-0">
                    <?php echo isset($site_settings['general']['site_tagline']) ? esc_html($site_settings['general']['site_tagline']) : 'Belum ada slider aktif. Tambahkan slide pertama Anda di Admin > Sliders.'; ?>
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    <a href="<?php echo base_url('ppdb'); ?>" class="inline-flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium shadow-xl shadow-indigo-900/40 transition">
                        <span>Daftar PPDB Online</span>
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 p-6 space-y-6 shadow-2xl">
                    <h3 class="text-lg font-bold tracking-tight text-white border-b border-white/10 pb-3">Informasi Satuan Pendidikan</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <i data-lucide="user" class="w-5 h-5 text-indigo-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-xs text-slate-400 uppercase">Kepala Sekolah</div>
                                <div class="text-sm font-semibold text-white"><?php echo isset($site_settings['school']['principal_name']) ? esc_html($site_settings['school']['principal_name']) : 'Belum diatur'; ?></div>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i data-lucide="map-pin" class="w-5 h-5 text-indigo-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-xs text-slate-400 uppercase">Alamat Lengkap</div>
                                <div class="text-sm font-semibold text-white"><?php echo esc_html(site_address('Belum diatur')); ?></div>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i data-lucide="phone" class="w-5 h-5 text-indigo-400 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-xs text-slate-400 uppercase">Hubungi Kontak</div>
                                <div class="text-sm font-semibold text-white"><?php echo esc_html(site_phone('Belum diatur')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
                    endif; // sliders
                endif; // show_hero_section

                // Open <main> after hero
                if (!$main_opened) {
                    echo '<main class="flex-grow w-full">';
                    $main_opened = TRUE;
                }
                break;

            // ============================================================
            // VIDEO
            // ============================================================
            case 'videos':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_video_section) && !empty($videos)):
                    $this->load->helper('video');
?>
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex justify-between items-end">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_videos_subtitle) ? esc_html($hp_videos_subtitle) : 'Kenali Kami Lebih Dekat'; ?></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-1"><?php echo !empty($hp_videos_title) ? esc_html($hp_videos_title) : 'Video Terbaru'; ?></h2>
            </div>
            <a href="<?php echo base_url('video'); ?>" class="hidden sm:inline-flex items-center space-x-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                <span>Lihat Semua Video</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($videos as $vid): $vid_thumb = resolve_video_thumbnail($vid->thumbnail, $vid->platform, $vid->video_url); ?>
                <a href="<?php echo base_url('video/' . $vid->slug); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                    <div class="h-32 bg-slate-800 relative overflow-hidden">
                        <?php if (!empty($vid_thumb)): ?>
                            <img src="<?php echo esc_attr($vid_thumb); ?>" alt="<?php echo esc_attr($vid->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-white/60">
                                <i data-lucide="video" class="w-8 h-8"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                            <div class="w-9 h-9 rounded-full bg-white/90 flex items-center justify-center">
                                <i data-lucide="play" class="w-4 h-4 text-indigo-600 ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-sm text-slate-900 leading-snug line-clamp-2 group-hover:text-indigo-600 transition"><?php echo esc_html($vid->title); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // NEWS
            // ============================================================
            case 'news':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_news_section)):
?>
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="flex justify-between items-end">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_news_subtitle) ? esc_html($hp_news_subtitle) : 'Portal Berita'; ?></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900"><?php echo !empty($hp_news_title) ? esc_html($hp_news_title) : 'Berita Terbaru'; ?></h2>
            </div>
            <a href="<?php echo base_url('berita'); ?>" class="hidden sm:inline-flex items-center space-x-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                <span>Lihat Semua Berita</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <?php if ($featured_post): ?>
        <div class="grid lg:grid-cols-3 gap-8">
            <a href="<?php echo base_url('berita/' . $featured_post->slug); ?>" class="lg:col-span-2 bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-lg transition duration-300 flex flex-col group">
                <div class="h-72 sm:h-96 bg-slate-200 relative overflow-hidden">
                    <?php if (!empty($featured_post->image)): ?>
                        <img src="<?php echo base_url($featured_post->image); ?>" alt="<?php echo esc_attr($featured_post->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400">
                            <i data-lucide="image" class="w-14 h-14"></i>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($featured_post->categories)): ?>
                        <span class="absolute top-4 left-4 bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full shadow">
                            <?php echo esc_html($featured_post->categories[0]->name); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="p-6 sm:p-8 flex-grow flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="text-[10px] uppercase font-bold text-indigo-600 tracking-wider"><?php echo date('d M Y', strtotime($featured_post->created_at)); ?></div>
                        <h3 class="font-extrabold text-2xl text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($featured_post->title); ?></h3>
                        <p class="text-slate-600 text-sm line-clamp-3"><?php echo esc_html(strip_tags($featured_post->content)); ?></p>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-slate-50 text-xs text-slate-500">
                        <span class="inline-flex items-center space-x-1"><i data-lucide="user" class="w-3.5 h-3.5"></i><span><?php echo esc_html($featured_post->author_name ?: 'Admin'); ?></span></span>
                        <span class="inline-flex items-center space-x-1 text-sm font-medium text-indigo-600">
                            <span>Baca Selengkapnya</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </span>
                    </div>
                </div>
            </a>

            <div class="flex flex-col gap-4">
                <?php if (!empty($latest_posts)): ?>
                    <?php foreach (array_slice($latest_posts, 0, 3) as $post): ?>
                        <a href="<?php echo base_url('berita/' . $post->slug); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition duration-300 flex items-center gap-4 p-3 group">
                            <div class="w-20 h-20 shrink-0 bg-slate-200 rounded-xl overflow-hidden">
                                <?php if (!empty($post->image)): ?>
                                    <img src="<?php echo base_url($post->image); ?>" alt="<?php echo esc_attr($post->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400">
                                        <i data-lucide="image" class="w-6 h-6"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 space-y-1">
                                <div class="text-[10px] uppercase font-bold text-indigo-600 tracking-wider"><?php echo date('d M Y', strtotime($post->created_at)); ?></div>
                                <h4 class="font-semibold text-sm text-slate-900 leading-snug line-clamp-2 group-hover:text-indigo-600 transition"><?php echo esc_html($post->title); ?></h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 text-xs text-slate-400 flex-grow flex items-center justify-center text-center">
                        Belum ada berita lain
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm p-10 text-center text-sm text-slate-400">
            Belum ada berita dipublikasikan
        </div>
        <?php endif; ?>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // STATS
            // ============================================================
            case 'stats':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_stats_section)):
?>
<section class="bg-indigo-600 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center text-white divide-y sm:divide-y-0 sm:divide-x divide-white/20">
            <div class="flex flex-col items-center space-y-2 pt-6 sm:pt-0">
                <i data-lucide="users" class="w-8 h-8 text-indigo-200"></i>
                <div class="text-4xl font-extrabold"><?php echo (int) $stats['staff_count']; ?></div>
                <div class="text-sm text-indigo-100 font-medium">Guru &amp; Staf</div>
            </div>
            <div class="flex flex-col items-center space-y-2 pt-6 sm:pt-0">
                <i data-lucide="volleyball" class="w-8 h-8 text-indigo-200"></i>
                <div class="text-4xl font-extrabold"><?php echo (int) $stats['extracurricular_count']; ?></div>
                <div class="text-sm text-indigo-100 font-medium">Ekstrakurikuler</div>
            </div>
            <div class="flex flex-col items-center space-y-2 pt-6 sm:pt-0">
                <i data-lucide="trophy" class="w-8 h-8 text-indigo-200"></i>
                <div class="text-4xl font-extrabold"><?php echo (int) $stats['achievement_count']; ?></div>
                <div class="text-sm text-indigo-100 font-medium">Prestasi</div>
            </div>
        </div>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // ANNOUNCEMENTS
            // ============================================================
            case 'announcements':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_announcements_section)):
?>
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex justify-between items-end">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_announcements_subtitle) ? esc_html($hp_announcements_subtitle) : 'Informasi Resmi'; ?></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900"><?php echo !empty($hp_announcements_title) ? esc_html($hp_announcements_title) : 'Pengumuman Terbaru'; ?></h2>
            </div>
            <a href="<?php echo base_url('pengumuman'); ?>" class="hidden sm:inline-flex items-center space-x-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                <span>Lihat Semua Pengumuman</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <?php if (!empty($announcements)): ?>
        <div class="grid md:grid-cols-3 gap-6">
            <?php foreach ($announcements as $item): ?>
                <a href="<?php echo base_url('pengumuman/' . $item->slug); ?>" class="bg-slate-50 hover:bg-white rounded-2xl border border-slate-100 hover:shadow-md transition duration-300 p-6 flex flex-col space-y-3 group">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-indigo-600 tracking-wider"><?php echo date('d M Y', strtotime($item->created_at)); ?></span>
                        <?php if (!empty($item->is_pinned)): ?>
                            <span class="inline-flex items-center space-x-1 text-[10px] font-bold uppercase text-amber-600">
                                <i data-lucide="pin" class="w-3 h-3"></i><span>Disematkan</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-base text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($item->title); ?></h3>
                    <p class="text-slate-600 text-sm line-clamp-2"><?php echo esc_html(strip_tags($item->content)); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="bg-slate-50 rounded-2xl border border-slate-100 p-10 text-center text-sm text-slate-400">
            Belum ada pengumuman dipublikasikan
        </div>
        <?php endif; ?>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // AGENDA
            // ============================================================
            case 'agenda':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_agenda_section)):
?>
<section class="bg-slate-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex justify-between items-end">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_agenda_subtitle) ? esc_html($hp_agenda_subtitle) : 'Kalender Kegiatan'; ?></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900"><?php echo !empty($hp_agenda_title) ? esc_html($hp_agenda_title) : 'Agenda Mendatang'; ?></h2>
            </div>
            <a href="<?php echo base_url('agenda'); ?>" class="hidden sm:inline-flex items-center space-x-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                <span>Lihat Semua Agenda</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <?php if (!empty($agendas)): ?>
        <div class="grid md:grid-cols-3 gap-6">
            <?php foreach ($agendas as $item): ?>
                <a href="<?php echo base_url('agenda/' . $item->slug); ?>" class="bg-white rounded-2xl border border-slate-100 hover:shadow-md transition duration-300 p-6 flex items-start space-x-4 group">
                    <div class="shrink-0 w-16 h-16 rounded-xl bg-indigo-50 flex flex-col items-center justify-center text-indigo-600">
                        <span class="text-xl font-extrabold leading-none"><?php echo date('d', strtotime($item->start_date)); ?></span>
                        <span class="text-[10px] uppercase font-bold tracking-wider"><?php echo date('M', strtotime($item->start_date)); ?></span>
                    </div>
                    <div class="min-w-0 space-y-1.5">
                        <h3 class="font-bold text-base text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($item->title); ?></h3>
                        <?php if (!empty($item->location)): ?>
                            <div class="text-xs text-slate-500 inline-flex items-center space-x-1">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i><span><?php echo esc_html($item->location); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-2xl border border-slate-100 p-10 text-center text-sm text-slate-400">
            Belum ada agenda mendatang
        </div>
        <?php endif; ?>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // PROGRAMS (Ekstrakurikuler)
            // ============================================================
            case 'programs':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_programs_section) && !empty($extracurriculars)):
?>
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex justify-between items-end">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_programs_subtitle) ? esc_html($hp_programs_subtitle) : 'Kembangkan Bakat & Minat'; ?></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900"><?php echo !empty($hp_programs_title) ? esc_html($hp_programs_title) : 'Ekstrakurikuler'; ?></h2>
            </div>
            <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="hidden sm:inline-flex items-center space-x-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                <span>Lihat Semua Ekstrakurikuler</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach ($extracurriculars as $ex): ?>
                <a href="<?php echo base_url('ekstrakurikuler/' . $ex->slug); ?>" class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 hover:shadow-md hover:bg-white transition duration-300 flex flex-col group">
                    <div class="h-36 bg-slate-200 relative overflow-hidden">
                        <?php if (!empty($ex->image)): ?>
                            <img src="<?php echo base_url($ex->image); ?>" alt="<?php echo esc_attr($ex->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400">
                                <i data-lucide="medal" class="w-9 h-9"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-5 space-y-2">
                        <h3 class="font-bold text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($ex->name); ?></h3>
                        <p class="text-slate-500 text-sm line-clamp-2"><?php echo esc_html(strip_tags($ex->description)); ?></p>
                        <?php if (!empty($ex->schedule)): ?>
                            <div class="inline-flex items-center space-x-1.5 text-xs text-slate-400 pt-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                <span><?php echo esc_html($ex->schedule); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // TESTIMONIALS
            // ============================================================
            case 'testimonials':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_testimonials_section) && !empty($testimonials)):
?>
<section class="bg-slate-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_testimonials_subtitle) ? esc_html($hp_testimonials_subtitle) : 'Apa Kata Mereka'; ?></span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-1"><?php echo !empty($hp_testimonials_title) ? esc_html($hp_testimonials_title) : 'Testimoni'; ?></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <?php foreach (array_slice($testimonials, 0, 6) as $t): ?>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <div class="text-amber-400 text-sm"><?php echo str_repeat('★', (int) $t->rating); echo str_repeat('☆', 5 - (int) $t->rating); ?></div>
                    <p class="text-slate-600 text-sm leading-relaxed">&ldquo;<?php echo esc_html($t->content); ?>&rdquo;</p>
                    <div class="flex items-center space-x-3 pt-2 border-t border-slate-50">
                        <?php if (!empty($t->avatar)): ?>
                            <img src="<?php echo base_url($t->avatar); ?>" alt="<?php echo esc_attr($t->name); ?>" class="w-10 h-10 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-400 flex items-center justify-center"><i data-lucide="user" class="w-5 h-5"></i></div>
                        <?php endif; ?>
                        <div>
                            <div class="text-sm font-bold text-slate-900"><?php echo esc_html($t->name); ?></div>
                            <div class="text-xs text-slate-500"><?php echo esc_html($t->role); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // GALLERY
            // ============================================================
            case 'gallery':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_gallery_section) && !empty($gallery_albums)):
?>
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="flex justify-between items-end">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_gallery_subtitle) ? esc_html($hp_gallery_subtitle) : 'Dokumentasi Kegiatan'; ?></span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900"><?php echo !empty($hp_gallery_title) ? esc_html($hp_gallery_title) : 'Galeri Sekolah'; ?></h2>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($gallery_albums as $album): ?>
                <a href="<?php echo base_url(($album->type === 'video' ? 'galeri-video/' : 'galeri-foto/') . $album->slug); ?>" class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 hover:shadow-md transition duration-300 flex flex-col group">
                    <div class="h-32 bg-slate-200 relative overflow-hidden">
                        <?php if (!empty($album->cover_image)): ?>
                            <img src="<?php echo base_url($album->cover_image); ?>" alt="<?php echo esc_attr($album->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-300">
                                <i data-lucide="<?php echo $album->type === 'video' ? 'video' : 'image'; ?>" class="w-8 h-8"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-sm text-slate-900 leading-snug line-clamp-2 group-hover:text-indigo-600 transition"><?php echo esc_html($album->title); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // PARTNERS
            // ============================================================
            case 'partners':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_partners_section) && !empty($partners)):
?>
<section class="bg-slate-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-indigo-600"><?php echo !empty($hp_partners_subtitle) ? esc_html($hp_partners_subtitle) : 'Didukung Oleh'; ?></span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-1"><?php echo !empty($hp_partners_title) ? esc_html($hp_partners_title) : 'Mitra Sekolah'; ?></h2>
        </div>
        <div class="flex flex-wrap justify-center items-center gap-8">
            <?php foreach ($partners as $p): ?>
                <?php if (!empty($p->link)): ?><a href="<?php echo esc_attr($p->link); ?>" target="_blank" rel="noopener" class="opacity-80 hover:opacity-100 transition"><?php endif; ?>
                    <?php if (!empty($p->logo)): ?>
                        <img src="<?php echo base_url($p->logo); ?>" alt="<?php echo esc_attr($p->name); ?>" class="h-12 object-contain grayscale hover:grayscale-0 transition">
                    <?php else: ?>
                        <span class="text-sm font-semibold text-slate-500"><?php echo esc_html($p->name); ?></span>
                    <?php endif; ?>
                <?php if (!empty($p->link)): ?></a><?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // PPDB
            // ============================================================
            case 'ppdb':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                if (!empty($show_ppdb_section)):
?>
<section class="bg-gradient-to-r from-indigo-600 to-indigo-700 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-5">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-200"><?php echo !empty($hp_ppdb_subtitle) ? esc_html($hp_ppdb_subtitle) : 'Tahun Ajaran Baru'; ?></span>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white"><?php echo !empty($hp_ppdb_title) ? esc_html($hp_ppdb_title) : 'Pendaftaran Siswa Baru'; ?></h2>
        <p class="text-indigo-100 max-w-xl mx-auto">Daftarkan putra-putri Anda sekarang melalui PPDB Online. Proses cepat, mudah, dan dapat dipantau secara berkala.</p>
        <a href="<?php echo base_url('ppdb'); ?>" class="inline-flex items-center justify-center space-x-2 bg-white hover:bg-indigo-50 text-indigo-700 px-6 py-3 rounded-xl font-bold shadow-xl transition">
            <span>Daftar Sekarang</span>
            <i data-lucide="arrow-right" class="w-5 h-5"></i>
        </a>
    </div>
</section>
<?php
                endif;
                break;

            // ============================================================
            // CTA
            // ============================================================
            case 'cta':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
?>
<section class="bg-slate-900 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Butuh Informasi Lebih Lanjut?</h2>
        <p class="text-slate-300">Tim kami siap membantu menjawab pertanyaan seputar sekolah, pendaftaran, hingga kegiatan akademik.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4 pt-2">
            <?php if (!empty($site_settings['school']['phone'])): ?>
                <a href="tel:<?php echo esc_attr($site_settings['school']['phone']); ?>" class="inline-flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium shadow-xl shadow-indigo-900/40 transition">
                    <i data-lucide="phone" class="w-5 h-5"></i>
                    <span><?php echo esc_html($site_settings['school']['phone']); ?></span>
                </a>
            <?php endif; ?>
            <?php if (!empty($site_settings['school']['email'])): ?>
                <a href="mailto:<?php echo esc_attr($site_settings['school']['email']); ?>" class="inline-flex items-center justify-center space-x-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl font-medium border border-white/20 transition">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                    <span><?php echo esc_html($site_settings['school']['email']); ?></span>
                </a>
            <?php endif; ?>
            <?php if (empty($site_settings['school']['phone']) && empty($site_settings['school']['email'])): ?>
                <a href="<?php echo base_url('kontak'); ?>" class="inline-flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium shadow-xl shadow-indigo-900/40 transition">
                    <span>Hubungi Kami</span>
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
                break;

            // Welcome and Vision are not implemented in the default theme
            // as separate components — skip silently
            case 'welcome':
            case 'vision':
                if (!$main_opened) { echo '<main class="flex-grow w-full">'; $main_opened = TRUE; }
                break;

        endswitch;
    endforeach;

    if (!$main_opened) { echo '<main class="flex-grow w-full">'; }
?>
</main>
<?php else: ?>
<!-- Fallback: original hardcoded order (pre-RC07 compat) -->
<main class="flex-grow w-full">
</main>
<?php endif; ?>

<?php echo view('portal/partials/footer', $data ?? []); ?>

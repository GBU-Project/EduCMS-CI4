<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<?php
// ================================================================
// RC07-001: Dynamic Section Rendering
// ================================================================
// If $homepage_sections is available (RC07+), iterate sections in
// the order determined by admin settings. Otherwise, fall back to
// the original hardcoded order for backward compatibility.
if (!empty($homepage_sections)):
    foreach ($homepage_sections as $sec):
        if (empty($sec['enabled'])) continue;
        switch ($sec['key']):
            case 'hero':
?>
<!-- Hero / Banner Component -->
<?php $this->load->view('../../themes/islamic/views/components/hero'); ?>

<main class="flex-grow w-full">
<?php
                break;

            case 'welcome':
                $this->load->view('../../themes/islamic/views/components/welcome');
                break;

            case 'vision':
                $this->load->view('../../themes/islamic/views/components/vision');
                break;

            case 'stats':
                $this->load->view('../../themes/islamic/views/components/stats');
                break;

            case 'programs':
                $this->load->view('../../themes/islamic/views/components/programs');
                break;

            case 'news':
                if (!empty($show_news_section)):
?>
    <section class="py-16 sm:py-20 bg-slate-50 border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200 inline-block">
                        <?php echo !empty($hp_news_subtitle) ? esc_html($hp_news_subtitle) : 'Kabar & Artikel Terbaru'; ?>
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                        <?php echo !empty($hp_news_title) ? esc_html($hp_news_title) : 'Berita Sekolah'; ?>
                    </h2>
                </div>
                <a href="<?php echo base_url('berita'); ?>" class="inline-flex items-center space-x-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition">
                    <span>Lihat Semua Berita</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <?php if (!empty($latest_posts) || !empty($featured_post)): ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if ($featured_post): ?>
                    <?php $post = $featured_post; $this->load->view('../../themes/islamic/views/components/news-card', array('post' => $post)); ?>
                <?php endif; ?>

                <?php foreach (array_slice($latest_posts, 0, 5) as $post): ?>
                    <?php $this->load->view('../../themes/islamic/views/components/news-card', array('post' => $post)); ?>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-3xl p-12 text-center text-sm text-slate-500 border border-emerald-100">
                Belum ada berita dipublikasikan.
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php
                endif;
                break;

            case 'announcements':
                if (!empty($show_announcements_section)):
?>
    <section class="py-16 sm:py-20 bg-white border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-200 inline-block">
                        <?php echo !empty($hp_announcements_subtitle) ? esc_html($hp_announcements_subtitle) : 'Informasi Resmi'; ?>
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                        <?php echo !empty($hp_announcements_title) ? esc_html($hp_announcements_title) : 'Pengumuman Terbaru'; ?>
                    </h2>
                </div>
                <a href="<?php echo base_url('pengumuman'); ?>" class="inline-flex items-center space-x-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition">
                    <span>Lihat Semua Pengumuman</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <?php if (!empty($announcements)): ?>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($announcements as $item): ?>
                    <a href="<?php echo base_url('pengumuman/' . $item->slug); ?>" class="islamic-card bg-slate-50 hover:bg-white rounded-3xl border border-emerald-100/80 p-6 flex flex-col space-y-3 group transition">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-3 py-1 rounded-full">
                                <?php echo date('d M Y', strtotime($item->created_at)); ?>
                            </span>
                            <?php if (!empty($item->is_pinned)): ?>
                                <span class="inline-flex items-center space-x-1 text-[10px] font-bold uppercase text-amber-600">
                                    <i data-lucide="pin" class="w-3 h-3"></i><span>Disematkan</span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-extrabold text-base text-emerald-950 leading-snug group-hover:text-emerald-700 transition"><?php echo esc_html($item->title); ?></h3>
                        <p class="text-slate-600 text-xs sm:text-sm line-clamp-2 leading-relaxed"><?php echo esc_html(strip_tags($item->content)); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="bg-slate-50 rounded-3xl p-12 text-center text-sm text-slate-500 border border-emerald-100">
                Belum ada pengumuman dipublikasikan.
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php
                endif;
                break;

            case 'agenda':
                if (!empty($show_agenda_section)):
?>
    <section class="py-16 sm:py-20 bg-slate-50 border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200 inline-block">
                        <?php echo !empty($hp_agenda_subtitle) ? esc_html($hp_agenda_subtitle) : 'Kalender Kegiatan'; ?>
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                        <?php echo !empty($hp_agenda_title) ? esc_html($hp_agenda_title) : 'Agenda Mendatang'; ?>
                    </h2>
                </div>
                <a href="<?php echo base_url('agenda'); ?>" class="inline-flex items-center space-x-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition">
                    <span>Lihat Kalender Agenda</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <?php if (!empty($agendas)): ?>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($agendas as $item): ?>
                    <?php $this->load->view('../../themes/islamic/views/components/agenda-card', array('item' => $item)); ?>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-3xl p-12 text-center text-sm text-slate-500 border border-emerald-100">
                Belum ada agenda mendatang.
            </div>
            <?php endif; ?>
        </div>
    </section>
<?php
                endif;
                break;

            case 'videos':
                if (!empty($show_video_section) && !empty($videos)):
?>
    <section class="py-16 sm:py-20 bg-white border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-200 inline-block">
                        <?php echo !empty($hp_videos_subtitle) ? esc_html($hp_videos_subtitle) : 'Dokumentasi Video'; ?>
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                        <?php echo !empty($hp_videos_title) ? esc_html($hp_videos_title) : 'Video Terbaru'; ?>
                    </h2>
                </div>
                <a href="<?php echo base_url('video'); ?>" class="inline-flex items-center space-x-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition">
                    <span>Lihat Galeri Video</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($videos as $vid): ?>
                    <?php $this->load->view('../../themes/islamic/views/components/video-card', array('vid' => $vid)); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php
                endif;
                break;

            case 'gallery':
                if (!empty($show_gallery_section) && !empty($gallery_albums)):
?>
    <section class="py-16 sm:py-20 bg-slate-50 border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200 inline-block">
                        <?php echo !empty($hp_gallery_subtitle) ? esc_html($hp_gallery_subtitle) : 'Album Dokumentasi'; ?>
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                        <?php echo !empty($hp_gallery_title) ? esc_html($hp_gallery_title) : 'Galeri Kegiatan'; ?>
                    </h2>
                </div>
                <a href="<?php echo base_url('galeri-foto'); ?>" class="inline-flex items-center space-x-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition">
                    <span>Lihat Semua Album</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($gallery_albums as $album): ?>
                    <?php $this->load->view('../../themes/islamic/views/components/gallery-card', array('album' => $album)); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php
                endif;
                break;

            case 'partners':
                if (!empty($show_partners_section) && !empty($partners)):
?>
    <section class="py-14 bg-white border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 inline-block">
                    <?php echo !empty($hp_partners_subtitle) ? esc_html($hp_partners_subtitle) : 'Kerjasama & Kemitraan'; ?>
                </span>
                <h2 class="text-2xl font-extrabold tracking-tight text-emerald-950">
                    <?php echo !empty($hp_partners_title) ? esc_html($hp_partners_title) : 'Mitra &amp; Network Sekolah'; ?>
                </h2>
            </div>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12">
                <?php foreach ($partners as $p): ?>
                    <?php if (!empty($p->link)): ?><a href="<?php echo esc_attr($p->link); ?>" target="_blank" rel="noopener" class="opacity-80 hover:opacity-100 transition"><?php endif; ?>
                        <?php if (!empty($p->logo)): ?>
                            <img src="<?php echo base_url($p->logo); ?>" alt="<?php echo esc_attr($p->name); ?>" class="h-10 sm:h-12 object-contain grayscale hover:grayscale-0 transition duration-300">
                        <?php else: ?>
                            <span class="text-sm font-semibold text-slate-600 bg-slate-100 px-4 py-2 rounded-xl"><?php echo esc_html($p->name); ?></span>
                        <?php endif; ?>
                    <?php if (!empty($p->link)): ?></a><?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php
                endif;
                break;

            case 'testimonials':
                if (!empty($show_testimonials_section) && !empty($testimonials)):
?>
    <section class="py-16 sm:py-20 bg-white border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-200 inline-block">
                    <?php echo !empty($hp_testimonials_subtitle) ? esc_html($hp_testimonials_subtitle) : 'Apa Kata Mereka'; ?>
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                    <?php echo !empty($hp_testimonials_title) ? esc_html($hp_testimonials_title) : 'Testimoni'; ?>
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach (array_slice($testimonials, 0, 6) as $t): ?>
                    <div class="islamic-card bg-slate-50 rounded-3xl border border-emerald-100/80 p-6 flex flex-col justify-between space-y-4 shadow-sm hover:shadow-md transition">
                        <div class="space-y-3">
                            <div class="text-amber-400 text-sm tracking-wider">
                                <?php echo str_repeat('★', (int) $t->rating); echo str_repeat('☆', 5 - (int) $t->rating); ?>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed italic">&ldquo;<?php echo esc_html($t->content); ?>&rdquo;</p>
                        </div>
                        <div class="flex items-center space-x-3 pt-4 border-t border-emerald-100/60">
                            <?php if (!empty($t->avatar)): ?>
                                <img src="<?php echo base_url($t->avatar); ?>" alt="<?php echo esc_attr($t->name); ?>" class="w-10 h-10 rounded-full object-cover border border-emerald-200">
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm border border-emerald-200">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="text-sm font-bold text-emerald-950"><?php echo esc_html($t->name); ?></div>
                                <div class="text-xs text-emerald-700/80 font-medium"><?php echo esc_html($t->role); ?></div>
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

            case 'ppdb':
                // The CTA component includes PPDB section internally,
                // so we render it here and track it to avoid double-load.
                if (empty($__cta_rendered)) {
                    $this->load->view('../../themes/islamic/views/components/cta');
                    $__cta_rendered = TRUE;
                }
                break;

            case 'cta':
                // CTA is already rendered via the ppdb case above.
                // If ppdb was disabled/skipped, render CTA here.
                if (empty($__cta_rendered)) {
                    $this->load->view('../../themes/islamic/views/components/cta');
                    $__cta_rendered = TRUE;
                }
                break;

        endswitch;
    endforeach;
?>
</main>

<?php else: ?>
<!-- Fallback: original hardcoded order for pre-RC07 compatibility -->
<?php $this->load->view('../../themes/islamic/views/components/hero'); ?>

<main class="flex-grow w-full">
    <?php $this->load->view('../../themes/islamic/views/components/welcome'); ?>
    <?php $this->load->view('../../themes/islamic/views/components/vision'); ?>
    <?php $this->load->view('../../themes/islamic/views/components/stats'); ?>
    <?php $this->load->view('../../themes/islamic/views/components/programs'); ?>
    <?php $this->load->view('../../themes/islamic/views/components/cta'); ?>
</main>
<?php endif; ?>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

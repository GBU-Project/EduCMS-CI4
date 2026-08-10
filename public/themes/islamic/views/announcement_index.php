<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold">Pengumuman</span>
        </nav>

        <!-- Header -->
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Papan Informasi
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                Pengumuman Resmi Sekolah
            </h1>
        </div>

        <?php if (empty($announcements)): ?>
            <div class="bg-white rounded-3xl border border-emerald-100 p-16 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                    <i data-lucide="megaphone" class="w-8 h-8"></i>
                </div>
                <h3 class="font-extrabold text-emerald-950 text-lg">Belum Ada Pengumuman</h3>
                <p class="text-slate-500 text-sm mt-1">Pengumuman yang dipublikasikan admin akan tampil di sini.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($announcements as $item): ?>
                    <a href="<?php echo base_url('pengumuman/' . $item->slug); ?>" class="islamic-card bg-white rounded-3xl border border-emerald-100 shadow-sm hover:shadow-md transition p-6 flex items-start gap-5 group">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100 shadow-sm">
                            <i data-lucide="megaphone" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-grow space-y-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <?php if (!empty($item->is_pinned)): ?>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Disematkan</span>
                                <?php endif; ?>
                                <span class="text-xs text-slate-400 font-semibold"><?php echo date('d M Y', strtotime($item->created_at)); ?></span>
                            </div>
                            <h3 class="font-extrabold text-base text-emerald-950 group-hover:text-emerald-700 transition leading-snug"><?php echo esc_html($item->title); ?></h3>
                            <p class="text-slate-600 text-xs sm:text-sm line-clamp-2 leading-relaxed"><?php echo esc_html(strip_tags($item->content)); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center items-center space-x-3 pt-8 border-t border-emerald-100">
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo base_url('pengumuman?page=' . ($current_page - 1)); ?>" class="px-5 py-2.5 rounded-xl bg-white border border-emerald-200 text-xs font-bold text-emerald-800 hover:bg-emerald-50 transition shadow-sm">Sebelumnya</a>
                <?php endif; ?>
                <span class="px-4 py-2 text-xs font-semibold text-slate-600">Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?></span>
                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo base_url('pengumuman?page=' . ($current_page + 1)); ?>" class="px-5 py-2.5 rounded-xl bg-white border border-emerald-200 text-xs font-bold text-emerald-800 hover:bg-emerald-50 transition shadow-sm">Selanjutnya</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

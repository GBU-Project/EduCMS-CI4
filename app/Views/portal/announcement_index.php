<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Pengumuman</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Papan Informasi</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Pengumuman Sekolah</h1>
    </div>

    <?php if (empty($announcements)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="megaphone" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Pengumuman</h3>
            <p class="text-slate-500 text-sm mt-1">Pengumuman yang dipublikasikan admin akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($announcements as $item): ?>
                <a href="<?php echo base_url('pengumuman/' . $item->slug); ?>" class="flex items-start gap-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5 group">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="megaphone" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-grow">
                        <div class="flex items-center gap-2 flex-wrap">
                            <?php if (!empty($item->is_pinned)): ?>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-50 text-amber-600">Disematkan</span>
                            <?php endif; ?>
                            <span class="text-xs text-slate-400"><?php echo date('d M Y', strtotime($item->created_at)); ?></span>
                        </div>
                        <h3 class="font-bold text-slate-900 mt-1 group-hover:text-indigo-600 transition"><?php echo esc_html($item->title); ?></h3>
                        <p class="text-slate-600 text-sm mt-1 line-clamp-2"><?php echo esc_html(strip_tags($item->content)); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="flex justify-center items-center space-x-2 mt-12">
            <?php if ($current_page > 1): ?>
                <a href="<?php echo base_url('pengumuman?page=' . ($current_page - 1)); ?>" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Sebelumnya</a>
            <?php endif; ?>
            <span class="px-4 py-2 text-sm text-slate-500">Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?></span>
            <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo base_url('pengumuman?page=' . ($current_page + 1)); ?>" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Selanjutnya</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

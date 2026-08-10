<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold">Berita &amp; Artikel</span>
        </nav>

        <!-- Page Header -->
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Kabar Sekolah
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                Berita &amp; Artikel Terkini
            </h1>
            <p class="text-slate-600 text-sm sm:text-base max-w-2xl">
                Dapatkan informasi terbaru seputar kegiatan, agenda akademik, dan prestasi sekolah kami.
            </p>
        </div>

        <?php if (empty($posts)): ?>
            <div class="bg-white rounded-3xl border border-emerald-100 p-16 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                    <i data-lucide="newspaper" class="w-8 h-8"></i>
                </div>
                <h3 class="font-extrabold text-emerald-950 text-lg">Belum Ada Berita</h3>
                <p class="text-slate-500 text-sm mt-1">Berita yang dipublikasikan akan tampil di sini.</p>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($posts as $post): ?>
                    <?php $this->load->view('../../themes/islamic/views/components/news-card', array('post' => $post)); ?>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center items-center space-x-3 pt-8 border-t border-emerald-100">
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo base_url('berita?page=' . ($current_page - 1)); ?>" class="px-5 py-2.5 rounded-xl bg-white border border-emerald-200 text-xs font-bold text-emerald-800 hover:bg-emerald-50 transition shadow-sm">
                        Sebelumnya
                    </a>
                <?php endif; ?>
                <span class="px-4 py-2 text-xs font-semibold text-slate-600">
                    Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?>
                </span>
                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo base_url('berita?page=' . ($current_page + 1)); ?>" class="px-5 py-2.5 rounded-xl bg-white border border-emerald-200 text-xs font-bold text-emerald-800 hover:bg-emerald-50 transition shadow-sm">
                        Selanjutnya
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

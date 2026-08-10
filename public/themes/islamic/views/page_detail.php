<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold"><?php echo esc_html($page->title); ?></span>
        </nav>

        <!-- Page Header & Title Card -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <?php if (!empty($page->banner)): ?>
                <div class="rounded-2xl overflow-hidden h-64 sm:h-80 border border-emerald-100 shadow-sm">
                    <img src="<?php echo base_url($page->banner); ?>" alt="<?php echo esc_attr($page->title); ?>" class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="space-y-3">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-200/60 inline-block">
                    Halaman Informasi
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight">
                    <?php echo esc_html($page->title); ?>
                </h1>
            </div>

            <div class="prose prose-emerald max-w-none text-slate-700 leading-relaxed prose-headings:text-emerald-950 prose-headings:font-extrabold prose-a:text-emerald-700 prose-a:font-bold hover:prose-a:text-emerald-800 pt-4 border-t border-emerald-100/60">
                <?php echo $page->content; ?>
            </div>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

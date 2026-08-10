<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <a href="<?php echo base_url('pengumuman'); ?>" class="hover:text-emerald-700 transition">Pengumuman</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($item->title); ?></span>
        </nav>

        <!-- Main Card Container -->
        <article class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                    Pengumuman Resmi
                </span>
                <?php if (!empty($item->is_pinned)): ?>
                    <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Disematkan</span>
                <?php endif; ?>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight"><?php echo esc_html($item->title); ?></h1>

            <div class="flex items-center space-x-4 text-xs font-medium text-slate-500 py-3 border-y border-emerald-100/60">
                <span class="inline-flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i><span>Dipublikasikan: <?php echo date('d M Y, H:i', strtotime($item->created_at)); ?> WIB</span></span>
            </div>

            <div class="prose prose-emerald max-w-none text-slate-700 leading-relaxed prose-headings:text-emerald-950 prose-headings:font-extrabold prose-a:text-emerald-700 prose-a:font-bold hover:prose-a:text-emerald-800">
                <?php echo $item->content; ?>
            </div>
        </article>

        <div class="pt-2">
            <a href="<?php echo base_url('pengumuman'); ?>" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-white px-5 py-3 rounded-2xl border border-emerald-200 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Pengumuman</span>
            </a>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

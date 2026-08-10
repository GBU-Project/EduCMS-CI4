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
            <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="hover:text-emerald-700 transition">Ekstrakurikuler</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($item->name); ?></span>
        </nav>

        <!-- Main Card Container -->
        <article class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <?php if (!empty($item->image)): ?>
                <div class="rounded-2xl overflow-hidden h-64 sm:h-80 border border-emerald-100 shadow-sm">
                    <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->name); ?>" class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                    Program Kegiatan
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight"><?php echo esc_html($item->name); ?></h1>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-600 py-3 border-y border-emerald-100/60">
                <?php if (!empty($item->coach)): ?><span class="inline-flex items-center gap-1.5"><i data-lucide="user" class="w-4 h-4 text-emerald-600"></i><span>Pembina: <strong class="text-emerald-950"><?php echo esc_html($item->coach); ?></strong></span></span><?php endif; ?>
                <?php if (!empty($item->schedule)): ?><span class="inline-flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4 text-emerald-600"></i><span>Jadwal: <?php echo esc_html($item->schedule); ?></span></span><?php endif; ?>
            </div>

            <div class="prose prose-emerald max-w-none text-slate-700 leading-relaxed pt-2">
                <?php echo nl2br(esc_html($item->description)); ?>
            </div>
        </article>

        <div class="pt-2">
            <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-white px-5 py-3 rounded-2xl border border-emerald-200 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Ekstrakurikuler</span>
            </a>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

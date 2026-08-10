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
            <a href="<?php echo base_url('agenda'); ?>" class="hover:text-emerald-700 transition">Agenda</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($item->title); ?></span>
        </nav>

        <!-- Main Card Container -->
        <article class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                    Agenda Kegiatan
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight text-emerald-950 leading-tight"><?php echo esc_html($item->title); ?></h1>
            </div>

            <!-- Schedule Meta Badges -->
            <div class="bg-slate-50 rounded-2xl border border-emerald-100 p-6 grid sm:grid-cols-3 gap-4">
                <div>
                    <div class="text-[10px] text-emerald-800 uppercase font-bold tracking-wider">Mulai</div>
                    <div class="font-extrabold text-emerald-950 text-sm mt-0.5"><?php echo date('d M Y, H:i', strtotime($item->start_date)); ?> WIB</div>
                </div>
                <?php if (!empty($item->end_date)): ?>
                <div>
                    <div class="text-[10px] text-emerald-800 uppercase font-bold tracking-wider">Selesai</div>
                    <div class="font-extrabold text-emerald-950 text-sm mt-0.5"><?php echo date('d M Y, H:i', strtotime($item->end_date)); ?> WIB</div>
                </div>
                <?php endif; ?>
                <?php if (!empty($item->location)): ?>
                <div>
                    <div class="text-[10px] text-emerald-800 uppercase font-bold tracking-wider">Lokasi</div>
                    <div class="font-extrabold text-emerald-950 text-sm mt-0.5"><?php echo esc_html($item->location); ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="prose prose-emerald max-w-none text-slate-700 leading-relaxed pt-2">
                <?php echo nl2br(esc_html($item->description)); ?>
            </div>

            <?php if (!empty($item->coordinator)): ?>
                <div class="pt-4 border-t border-emerald-100/60 text-xs font-medium text-slate-600">
                    Penanggung Jawab: <span class="font-bold text-emerald-950"><?php echo esc_html($item->coordinator); ?></span>
                </div>
            <?php endif; ?>
        </article>

        <div class="pt-2">
            <a href="<?php echo base_url('agenda'); ?>" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-white px-5 py-3 rounded-2xl border border-emerald-200 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Kalender Agenda</span>
            </a>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url('agenda'); ?>" class="hover:text-indigo-600 transition">Agenda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium"><?php echo esc_html($item->title); ?></span>
    </nav>

    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-6"><?php echo esc_html($item->title); ?></h1>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-8 grid sm:grid-cols-3 gap-4">
        <div>
            <div class="text-xs text-slate-400 uppercase">Mulai</div>
            <div class="font-semibold text-slate-800"><?php echo date('d M Y, H:i', strtotime($item->start_date)); ?> WIB</div>
        </div>
        <?php if (!empty($item->end_date)): ?>
        <div>
            <div class="text-xs text-slate-400 uppercase">Selesai</div>
            <div class="font-semibold text-slate-800"><?php echo date('d M Y, H:i', strtotime($item->end_date)); ?> WIB</div>
        </div>
        <?php endif; ?>
        <?php if (!empty($item->location)): ?>
        <div>
            <div class="text-xs text-slate-400 uppercase">Lokasi</div>
            <div class="font-semibold text-slate-800"><?php echo esc_html($item->location); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="prose prose-slate max-w-none">
        <?php echo nl2br(esc_html($item->description)); ?>
    </div>

    <?php if (!empty($item->coordinator)): ?>
        <p class="mt-6 text-sm text-slate-500">Penanggung Jawab: <span class="font-medium text-slate-700"><?php echo esc_html($item->coordinator); ?></span></p>
    <?php endif; ?>

    <div class="mt-10 pt-8 border-t border-slate-100">
        <a href="<?php echo base_url('agenda'); ?>" class="inline-flex items-center space-x-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Agenda</span>
        </a>
    </div>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

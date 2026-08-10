<?php echo view('portal/partials/header'); ?>

<main class="flex-grow max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="hover:text-indigo-600 transition">Ekstrakurikuler</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium"><?php echo esc_html($item->name); ?></span>
    </nav>

    <?php if (!empty($item->image)): ?>
        <div class="rounded-2xl overflow-hidden mb-8 h-64">
            <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->name); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>

    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 mb-4"><?php echo esc_html($item->name); ?></h1>

    <div class="flex flex-wrap gap-4 text-sm text-slate-500 mb-8 pb-6 border-b border-slate-100">
        <?php if (!empty($item->coach)): ?><span class="inline-flex items-center gap-1.5"><i data-lucide="user" class="w-4 h-4"></i>Pembina: <?php echo esc_html($item->coach); ?></span><?php endif; ?>
        <?php if (!empty($item->schedule)): ?><span class="inline-flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4"></i><?php echo esc_html($item->schedule); ?></span><?php endif; ?>
    </div>

    <div class="prose prose-slate max-w-none">
        <?php echo nl2br(esc_html($item->description)); ?>
    </div>

    <div class="mt-10 pt-8 border-t border-slate-100">
        <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="inline-flex items-center space-x-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Ekstrakurikuler</span>
        </a>
    </div>
</main>

<?php echo view('portal/partials/footer'); ?>

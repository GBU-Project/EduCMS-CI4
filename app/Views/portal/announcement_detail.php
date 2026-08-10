<?php echo view('portal/partials/header'); ?>

<main class="flex-grow max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url('pengumuman'); ?>" class="hover:text-indigo-600 transition">Pengumuman</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium truncate max-w-xs"><?php echo esc_html($item->title); ?></span>
    </nav>

    <?php if (!empty($item->is_pinned)): ?>
        <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full bg-amber-50 text-amber-600 mb-4">Disematkan</span>
    <?php endif; ?>

    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-4"><?php echo esc_html($item->title); ?></h1>

    <div class="flex items-center space-x-4 text-sm text-slate-500 mb-8 pb-6 border-b border-slate-100">
        <span class="inline-flex items-center space-x-1.5"><i data-lucide="calendar" class="w-4 h-4"></i><span><?php echo date('d M Y', strtotime($item->created_at)); ?></span></span>
    </div>

    <div class="prose prose-slate max-w-none prose-headings:font-bold prose-a:text-indigo-600">
        <?php echo sanitize_html($item->content); ?>
    </div>

    <div class="mt-10 pt-8 border-t border-slate-100">
        <a href="<?php echo base_url('pengumuman'); ?>" class="inline-flex items-center space-x-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Pengumuman</span>
        </a>
    </div>
</main>

<?php echo view('portal/partials/footer'); ?>

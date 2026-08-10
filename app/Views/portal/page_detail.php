<?php echo view('portal/partials/header'); ?>

<main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium"><?php echo esc_html($page->title); ?></span>
    </nav>

    <?php if (!empty($page->banner)): ?>
        <div class="rounded-2xl overflow-hidden mb-8 h-64">
            <img src="<?php echo base_url($page->banner); ?>" alt="<?php echo esc_attr($page->title); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>

    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-6"><?php echo esc_html($page->title); ?></h1>

    <div class="prose prose-slate max-w-none prose-headings:font-bold prose-a:text-indigo-600">
        <?php echo sanitize_html($page->content); ?>
    </div>
</main>

<?php echo view('portal/partials/footer'); ?>

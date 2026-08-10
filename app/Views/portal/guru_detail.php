<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url('guru'); ?>" class="hover:text-indigo-600 transition">Direktori Guru</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium"><?php echo esc_html($teacher->name); ?></span>
    </nav>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 flex flex-col sm:flex-row gap-8 items-center sm:items-start text-center sm:text-left">
        <div class="w-32 h-32 rounded-2xl overflow-hidden bg-slate-100 shrink-0">
            <?php if (!empty($teacher->photo)): ?>
                <img src="<?php echo base_url($teacher->photo); ?>" alt="<?php echo esc_attr($teacher->name); ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400">
                    <i data-lucide="user-round" class="w-14 h-14"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-slate-900"><?php echo esc_html($teacher->name); ?></h1>
            <p class="text-indigo-600 font-medium"><?php echo esc_html($teacher->position); ?></p>
            <?php if (!empty($teacher->email)): ?><p class="text-sm text-slate-500 flex items-center justify-center sm:justify-start gap-1.5"><i data-lucide="mail" class="w-4 h-4"></i><?php echo esc_html($teacher->email); ?></p><?php endif; ?>
            <?php if (!empty($teacher->phone)): ?><p class="text-sm text-slate-500 flex items-center justify-center sm:justify-start gap-1.5"><i data-lucide="phone" class="w-4 h-4"></i><?php echo esc_html($teacher->phone); ?></p><?php endif; ?>
        </div>
    </div>

    <div class="mt-6">
        <a href="<?php echo base_url('guru'); ?>" class="inline-flex items-center space-x-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Direktori Guru</span>
        </a>
    </div>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

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
            <a href="<?php echo base_url('guru'); ?>" class="hover:text-emerald-700 transition">Direktori Guru</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($teacher->name); ?></span>
        </nav>

        <!-- Profile Card Container -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md flex flex-col sm:flex-row gap-8 items-center sm:items-start text-center sm:text-left">
            <div class="w-36 h-36 rounded-3xl overflow-hidden bg-emerald-900 shrink-0 border-4 border-emerald-50 shadow-md relative">
                <?php if (!empty($teacher->photo)): ?>
                    <img src="<?php echo base_url($teacher->photo); ?>" alt="<?php echo esc_attr($teacher->name); ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex items-center justify-center text-emerald-200">
                        <i data-lucide="user-round" class="w-16 h-16 text-amber-400"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="space-y-3 flex-grow">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                    Tenaga Pendidik
                </span>
                <h1 class="text-3xl font-extrabold text-emerald-950 leading-tight"><?php echo esc_html($teacher->name); ?></h1>
                <p class="text-amber-600 font-bold text-sm"><?php echo esc_html($teacher->position); ?></p>
                
                <div class="pt-2 space-y-1.5 text-xs font-semibold text-slate-600">
                    <?php if (!empty($teacher->email)): ?>
                        <p class="flex items-center justify-center sm:justify-start gap-2"><i data-lucide="mail" class="w-4 h-4 text-emerald-600"></i><span><?php echo esc_html($teacher->email); ?></span></p>
                    <?php endif; ?>
                    <?php if (!empty($teacher->phone)): ?>
                        <p class="flex items-center justify-center sm:justify-start gap-2"><i data-lucide="phone" class="w-4 h-4 text-emerald-600"></i><span><?php echo esc_html($teacher->phone); ?></span></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="pt-2">
            <a href="<?php echo base_url('guru'); ?>" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-white px-5 py-3 rounded-2xl border border-emerald-200 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Direktori Guru</span>
            </a>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

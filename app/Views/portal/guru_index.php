<?php echo view('portal/partials/header'); ?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Direktori Guru</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Tenaga Pendidik</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Direktori Guru</h1>
    </div>

    <?php if (empty($teachers)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="users" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Data Guru</h3>
            <p class="text-slate-500 text-sm mt-1">Data guru yang ditambahkan admin akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($teachers as $teacher): ?>
                <a href="<?php echo base_url('guru/' . $teacher->id); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition text-center group">
                    <div class="h-44 bg-slate-100 relative overflow-hidden">
                        <?php if (!empty($teacher->photo)): ?>
                            <img src="<?php echo base_url($teacher->photo); ?>" alt="<?php echo esc_attr($teacher->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400">
                                <i data-lucide="user-round" class="w-12 h-12"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition"><?php echo esc_html($teacher->name); ?></h3>
                        <p class="text-xs text-slate-500 mt-1"><?php echo esc_html($teacher->position); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php echo view('portal/partials/footer'); ?>

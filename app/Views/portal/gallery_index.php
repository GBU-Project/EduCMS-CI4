<?php echo view('portal/partials/header'); ?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium"><?php echo esc_html($page_title); ?></span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Dokumentasi Sekolah</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2"><?php echo esc_html($page_title); ?></h1>
    </div>

    <?php if (empty($albums)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="<?php echo $type === 'video' ? 'video' : 'image'; ?>" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Album</h3>
            <p class="text-slate-500 text-sm mt-1">Album yang ditambahkan admin akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($albums as $album): ?>
                <a href="<?php echo base_url(($type === 'video' ? 'galeri-video/' : 'galeri-foto/') . $album->slug); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                    <div class="h-44 bg-slate-200 relative overflow-hidden">
                        <?php if (!empty($album->cover_image)): ?>
                            <img src="<?php echo base_url($album->cover_image); ?>" alt="<?php echo esc_attr($album->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-300">
                                <i data-lucide="<?php echo $type === 'video' ? 'video' : 'image'; ?>" class="w-10 h-10"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 space-y-2">
                        <h3 class="font-bold text-lg text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($album->title); ?></h3>
                        <?php if (!empty($album->description)): ?>
                            <p class="text-sm text-slate-500 line-clamp-2"><?php echo esc_html($album->description); ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php echo view('portal/partials/footer'); ?>

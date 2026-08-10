<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <a href="<?php echo base_url($album->type === 'video' ? 'galeri-video' : 'galeri-foto'); ?>" class="hover:text-emerald-700 transition"><?php echo $album->type === 'video' ? 'Galeri Video' : 'Galeri Foto'; ?></a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($album->title); ?></span>
        </nav>

        <!-- Main Card Container -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                    Album <?php echo $album->type === 'video' ? 'Video' : 'Foto'; ?>
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight"><?php echo esc_html($album->title); ?></h1>
                <?php if (!empty($album->description)): ?>
                    <p class="text-slate-600 text-sm sm:text-base pt-1"><?php echo esc_html($album->description); ?></p>
                <?php endif; ?>
            </div>

            <?php if (empty($items)): ?>
                <div class="bg-slate-50 rounded-2xl border border-emerald-100 p-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100/60 text-emerald-700 flex items-center justify-center mx-auto mb-4 border border-emerald-200">
                        <i data-lucide="<?php echo $album->type === 'video' ? 'video' : 'image'; ?>" class="w-8 h-8"></i>
                    </div>
                    <h3 class="font-extrabold text-emerald-950 text-lg">Belum Ada Item dalam Album ini</h3>
                </div>
            <?php elseif ($album->type === 'video'): ?>
                <div class="grid md:grid-cols-2 gap-8 pt-4">
                    <?php foreach ($items as $it): ?>
                        <?php $this->load->helper('video'); ?>
                        <div class="islamic-card bg-slate-900 rounded-3xl overflow-hidden shadow-md border border-emerald-100 aspect-video relative">
                            <?php if (stripos($it->file_path, '<iframe') !== FALSE): ?>
                                <?php echo strip_tags($it->file_path, '<iframe>'); ?>
                            <?php else: ?>
                                <iframe src="<?php echo esc_attr(youtube_embed_url($it->file_path)); ?>" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 pt-4">
                    <?php foreach ($items as $it): ?>
                        <a href="<?php echo base_url($it->file_path); ?>" target="_blank" rel="noopener" class="islamic-card block rounded-2xl overflow-hidden aspect-square bg-slate-100 border border-emerald-100 shadow-sm group">
                            <img src="<?php echo base_url($it->file_path); ?>" alt="<?php echo esc_attr($it->caption ?? ''); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="pt-2">
            <a href="<?php echo base_url($album->type === 'video' ? 'galeri-video' : 'galeri-foto'); ?>" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-white px-5 py-3 rounded-2xl border border-emerald-200 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Galeri</span>
            </a>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

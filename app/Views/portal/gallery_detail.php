<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url($album->type === 'video' ? 'galeri-video' : 'galeri-foto'); ?>" class="hover:text-indigo-600 transition"><?php echo $album->type === 'video' ? 'Galeri Video' : 'Galeri Foto'; ?></a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium truncate max-w-xs"><?php echo esc_html($album->title); ?></span>
    </nav>

    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-3"><?php echo esc_html($album->title); ?></h1>
    <?php if (!empty($album->description)): ?>
        <p class="text-slate-600 mb-8 max-w-2xl"><?php echo esc_html($album->description); ?></p>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="<?php echo $album->type === 'video' ? 'video' : 'image'; ?>" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Item</h3>
        </div>
    <?php elseif ($album->type === 'video'): ?>
        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($items as $it): ?>
                <?php $this->load->helper('video'); ?>
                <div class="rounded-2xl overflow-hidden bg-black aspect-video">
                    <?php if (stripos($it->file_path, '<iframe') !== FALSE): ?>
                        <?php echo strip_tags($it->file_path, '<iframe>'); ?>
                    <?php else: ?>
                        <iframe src="<?php echo esc_attr(youtube_embed_url($it->file_path)); ?>" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
                <?php if (!empty($it->caption)): ?>
                    <p class="text-sm text-slate-500 mt-[-1.5rem]"><?php echo esc_html($it->caption); ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php foreach ($items as $it): ?>
                <a href="<?php echo base_url($it->file_path); ?>" target="_blank" rel="noopener" class="block rounded-xl overflow-hidden aspect-square bg-slate-100 group">
                    <img src="<?php echo base_url($it->file_path); ?>" alt="<?php echo esc_attr($it->caption ?? ''); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-10 pt-8 border-t border-slate-100">
        <a href="<?php echo base_url($album->type === 'video' ? 'galeri-video' : 'galeri-foto'); ?>" class="inline-flex items-center space-x-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke <?php echo $album->type === 'video' ? 'Galeri Video' : 'Galeri Foto'; ?></span>
        </a>
    </div>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

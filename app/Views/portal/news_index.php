<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Berita</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Portal Berita</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Berita &amp; Artikel Sekolah</h1>
    </div>

    <?php if (empty($posts)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="newspaper" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Berita</h3>
            <p class="text-slate-500 text-sm mt-1">Berita yang dipublikasikan akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
                <a href="<?php echo base_url('berita/' . $post->slug); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        <?php if (!empty($post->image)): ?>
                            <img src="<?php echo base_url($post->image); ?>" alt="<?php echo esc_attr($post->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400">
                                <i data-lucide="image" class="w-10 h-10"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <div class="text-[10px] uppercase font-bold text-indigo-600 tracking-wider"><?php echo date('d M Y', strtotime($post->created_at)); ?></div>
                            <h3 class="font-bold text-lg text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($post->title); ?></h3>
                            <p class="text-slate-600 text-sm line-clamp-3"><?php echo esc_html(strip_tags($post->content)); ?></p>
                            <span class="inline-flex items-center space-x-1 text-sm font-medium text-indigo-600">
                                <span>Baca Selengkapnya</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-slate-50 text-xs text-slate-500">
                            <span class="inline-flex items-center space-x-1"><i data-lucide="user" class="w-3.5 h-3.5"></i><span><?php echo esc_html($post->author_name ?: 'Admin'); ?></span></span>
                            <span class="inline-flex items-center space-x-1"><i data-lucide="eye" class="w-3.5 h-3.5"></i><span><?php echo (int) $post->view_count; ?></span></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="flex justify-center items-center space-x-2 mt-12">
            <?php if ($current_page > 1): ?>
                <a href="<?php echo base_url('berita?page=' . ($current_page - 1)); ?>" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Sebelumnya</a>
            <?php endif; ?>
            <span class="px-4 py-2 text-sm text-slate-500">Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?></span>
            <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo base_url('berita?page=' . ($current_page + 1)); ?>" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Selanjutnya</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

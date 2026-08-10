<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url('berita'); ?>" class="hover:text-indigo-600 transition">Berita</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium truncate max-w-xs"><?php echo esc_html($post->title); ?></span>
    </nav>

    <?php if (!empty($post->categories)): ?>
        <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ($post->categories as $cat): ?>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-indigo-600"><?php echo esc_html($cat->name); ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-4"><?php echo esc_html($post->title); ?></h1>

    <div class="flex items-center space-x-4 text-sm text-slate-500 mb-8 pb-6 border-b border-slate-100">
        <span class="inline-flex items-center space-x-1.5"><i data-lucide="user" class="w-4 h-4"></i><span><?php echo esc_html($post->author_name ?: 'Admin'); ?></span></span>
        <span class="inline-flex items-center space-x-1.5"><i data-lucide="calendar" class="w-4 h-4"></i><span><?php echo date('d M Y', strtotime($post->created_at)); ?></span></span>
        <span class="inline-flex items-center space-x-1.5"><i data-lucide="eye" class="w-4 h-4"></i><span><?php echo (int) $post->view_count; ?> views</span></span>
    </div>

    <?php if (!empty($post->image)): ?>
        <div class="rounded-2xl overflow-hidden mb-8 h-72 sm:h-96">
            <img src="<?php echo base_url($post->image); ?>" alt="<?php echo esc_attr($post->title); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>

    <div class="prose prose-slate max-w-none prose-headings:font-bold prose-a:text-indigo-600">
        <?php $this->load->helper('sanitize'); echo sanitize_html($post->content); ?>
    </div>

    <?php if (!empty($post->tags)): ?>
        <div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-slate-100">
            <?php foreach ($post->tags as $tag): ?>
                <span class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-600">#<?php echo esc_html($tag->name); ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Prev / Next Navigation -->
    <div class="grid sm:grid-cols-2 gap-4 mt-10 pt-8 border-t border-slate-100">
        <?php if ($prev_post): ?>
            <a href="<?php echo base_url('berita/' . $prev_post->slug); ?>" class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5">
                <div class="text-xs text-slate-400 uppercase tracking-wider flex items-center space-x-1.5 mb-2"><i data-lucide="arrow-left" class="w-3.5 h-3.5"></i><span>Sebelumnya</span></div>
                <div class="font-semibold text-slate-800 group-hover:text-indigo-600 transition line-clamp-2"><?php echo esc_html($prev_post->title); ?></div>
            </a>
        <?php else: ?>
            <div></div>
        <?php endif; ?>

        <?php if ($next_post): ?>
            <a href="<?php echo base_url('berita/' . $next_post->slug); ?>" class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5 text-right">
                <div class="text-xs text-slate-400 uppercase tracking-wider flex items-center justify-end space-x-1.5 mb-2"><span>Selanjutnya</span><i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></div>
                <div class="font-semibold text-slate-800 group-hover:text-indigo-600 transition line-clamp-2"><?php echo esc_html($next_post->title); ?></div>
            </a>
        <?php endif; ?>
    </div>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <a href="<?php echo base_url('berita'); ?>" class="hover:text-emerald-700 transition">Berita</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($post->title); ?></span>
        </nav>

        <!-- Main Article Container -->
        <article class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <?php if (!empty($post->categories)): ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($post->categories as $cat): ?>
                        <span class="text-xs font-bold uppercase tracking-wider px-3.5 py-1 rounded-full bg-emerald-100/60 text-emerald-800 border border-emerald-200"><?php echo esc_html($cat->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight">
                <?php echo esc_html($post->title); ?>
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 py-3 border-y border-emerald-100/60">
                <span class="inline-flex items-center gap-1.5"><i data-lucide="user" class="w-4 h-4 text-emerald-600"></i><span><?php echo esc_html($post->author_name ?: 'Admin'); ?></span></span>
                <span class="inline-flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i><span><?php echo date('d M Y', strtotime($post->created_at)); ?></span></span>
                <span class="inline-flex items-center gap-1.5"><i data-lucide="eye" class="w-4 h-4 text-emerald-600"></i><span><?php echo (int) $post->view_count; ?> views</span></span>
            </div>

            <?php if (!empty($post->image)): ?>
                <div class="rounded-2xl overflow-hidden h-72 sm:h-96 border border-emerald-100 shadow-sm">
                    <img src="<?php echo base_url($post->image); ?>" alt="<?php echo esc_attr($post->title); ?>" class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="prose prose-emerald max-w-none text-slate-700 leading-relaxed prose-headings:text-emerald-950 prose-headings:font-extrabold prose-a:text-emerald-700 prose-a:font-bold hover:prose-a:text-emerald-800">
                <?php echo $post->content; ?>
            </div>

            <?php if (!empty($post->tags)): ?>
                <div class="flex flex-wrap gap-2 pt-6 border-t border-emerald-100/60">
                    <?php foreach ($post->tags as $tag): ?>
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600">#<?php echo esc_html($tag->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <!-- Prev / Next Navigation -->
        <div class="grid sm:grid-cols-2 gap-4 pt-4">
            <?php if ($prev_post): ?>
                <a href="<?php echo base_url('berita/' . $prev_post->slug); ?>" class="islamic-card bg-white rounded-3xl border border-emerald-100 shadow-sm hover:shadow-md transition p-6 space-y-2">
                    <div class="text-xs text-emerald-700 font-bold uppercase tracking-wider flex items-center gap-1.5"><i data-lucide="arrow-left" class="w-4 h-4"></i><span>Berita Sebelumnya</span></div>
                    <div class="font-extrabold text-emerald-950 group-hover:text-emerald-700 transition line-clamp-2"><?php echo esc_html($prev_post->title); ?></div>
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($next_post): ?>
                <a href="<?php echo base_url('berita/' . $next_post->slug); ?>" class="islamic-card bg-white rounded-3xl border border-emerald-100 shadow-sm hover:shadow-md transition p-6 text-right space-y-2">
                    <div class="text-xs text-emerald-700 font-bold uppercase tracking-wider flex items-center justify-end gap-1.5"><span>Berita Selanjutnya</span><i data-lucide="arrow-right" class="w-4 h-4"></i></div>
                    <div class="font-extrabold text-emerald-950 group-hover:text-emerald-700 transition line-clamp-2"><?php echo esc_html($next_post->title); ?></div>
                </a>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

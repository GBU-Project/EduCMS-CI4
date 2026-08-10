<!-- News Card Component -->
<a href="<?php echo base_url('berita/' . $post->slug); ?>" class="islamic-card bg-white rounded-3xl overflow-hidden border border-emerald-100 shadow-sm hover:shadow-xl transition flex flex-col group">
    <div class="h-48 bg-emerald-900 relative overflow-hidden">
        <?php if (!empty($post->image)): ?>
            <img src="<?php echo base_url($post->image); ?>" alt="<?php echo esc_attr($post->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex items-center justify-center text-emerald-300">
                <i data-lucide="newspaper" class="w-10 h-10"></i>
            </div>
        <?php endif; ?>
        <span class="absolute top-4 left-4 bg-emerald-800/90 text-amber-300 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full border border-emerald-700/60 shadow">
            <?php echo date('d M Y', strtotime($post->created_at)); ?>
        </span>
    </div>
    <div class="p-6 space-y-3 flex-grow flex flex-col justify-between">
        <div class="space-y-2">
            <h3 class="font-extrabold text-base text-emerald-950 leading-snug group-hover:text-emerald-700 transition line-clamp-2"><?php echo esc_html($post->title); ?></h3>
            <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed"><?php echo esc_html(strip_tags($post->content)); ?></p>
        </div>
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span class="inline-flex items-center gap-1.5"><i data-lucide="user" class="w-3.5 h-3.5 text-emerald-600"></i><span><?php echo esc_html($post->author_name ?: 'Admin'); ?></span></span>
            <span class="inline-flex items-center gap-1 font-bold text-emerald-700 group-hover:translate-x-1 transition">
                <span>Baca</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </span>
        </div>
    </div>
</a>

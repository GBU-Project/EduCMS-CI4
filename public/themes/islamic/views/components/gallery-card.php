<!-- Gallery Card Component -->
<a href="<?php echo base_url(($album->type === 'video' ? 'galeri-video/' : 'galeri-foto/') . $album->slug); ?>" class="islamic-card bg-white rounded-3xl overflow-hidden border border-emerald-100 shadow-sm hover:shadow-xl transition flex flex-col group">
    <div class="h-44 bg-emerald-900 relative overflow-hidden">
        <?php if (!empty($album->cover_image)): ?>
            <img src="<?php echo base_url($album->cover_image); ?>" alt="<?php echo esc_attr($album->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex items-center justify-center text-emerald-300">
                <i data-lucide="<?php echo $album->type === 'video' ? 'video' : 'image'; ?>" class="w-10 h-10"></i>
            </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent"></div>
    </div>
    <div class="p-5 space-y-1.5">
        <h3 class="font-extrabold text-base text-emerald-950 leading-snug group-hover:text-emerald-700 transition line-clamp-2"><?php echo esc_html($album->title); ?></h3>
        <?php if (!empty($album->description)): ?>
            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed"><?php echo esc_html($album->description); ?></p>
        <?php endif; ?>
    </div>
</a>

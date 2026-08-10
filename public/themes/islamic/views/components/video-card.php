<!-- Video Card Component -->
<?php $this->load->helper('video'); $vid_thumb = resolve_video_thumbnail($vid->thumbnail, $vid->platform, $vid->video_url); ?>
<a href="<?php echo base_url('video/' . $vid->slug); ?>" class="islamic-card bg-white rounded-3xl overflow-hidden border border-emerald-100 shadow-sm hover:shadow-xl transition flex flex-col group">
    <div class="h-40 bg-emerald-950 relative overflow-hidden">
        <?php if (!empty($vid_thumb)): ?>
            <img src="<?php echo esc_attr($vid_thumb); ?>" alt="<?php echo esc_attr($vid->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex items-center justify-center text-emerald-300">
                <i data-lucide="video" class="w-10 h-10"></i>
            </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-emerald-950/30 flex items-center justify-center">
            <div class="w-11 h-11 rounded-full bg-white/95 text-emerald-800 flex items-center justify-center shadow-lg group-hover:scale-110 transition">
                <i data-lucide="play" class="w-5 h-5 ml-0.5 fill-emerald-800"></i>
            </div>
        </div>
    </div>
    <div class="p-5">
        <h3 class="font-extrabold text-sm sm:text-base text-emerald-950 leading-snug line-clamp-2 group-hover:text-emerald-700 transition"><?php echo esc_html($vid->title); ?></h3>
    </div>
</a>

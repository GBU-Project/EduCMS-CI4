<?php $this->load->view('../../themes/islamic/views/partials/header');
$this->load->helper('video');
$platform_labels = array('youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'other' => 'Lainnya');

$is_iframe_markup = (stripos($item->video_url, '<iframe') !== FALSE);
$embed_src = '';
if ($item->platform === 'youtube') {
    $embed_src = youtube_embed_url($item->video_url);
} elseif ($is_iframe_markup) {
    $embed_src = '';
} else {
    $embed_src = $item->video_url;
}
?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <a href="<?php echo base_url('video'); ?>" class="hover:text-emerald-700 transition">Video</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($item->title); ?></span>
        </nav>

        <!-- Main Card Container -->
        <article class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                    <?php echo $platform_labels[$item->platform] ?? $item->platform; ?>
                </span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight"><?php echo esc_html($item->title); ?></h1>

            <div class="islamic-card bg-black rounded-3xl overflow-hidden shadow-lg border border-emerald-100 aspect-video relative">
                <?php if ($is_iframe_markup): ?>
                    <?php echo strip_tags($item->video_url, '<iframe>'); ?>
                <?php elseif (!empty($embed_src)): ?>
                    <iframe src="<?php echo esc_attr($embed_src); ?>" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <?php else: ?>
                    <div class="w-full h-full flex flex-col items-center justify-center text-white/70 py-16">
                        <i data-lucide="video" class="w-10 h-10 mb-3 text-amber-400"></i>
                        <a href="<?php echo esc_attr($item->video_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center space-x-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-6 py-3 rounded-xl font-bold shadow-md transition">
                            <span>Tonton di <?php echo $platform_labels[$item->platform] ?? $item->platform; ?></span>
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($item->description)): ?>
                <div class="prose prose-emerald max-w-none text-slate-700 leading-relaxed pt-2">
                    <?php echo nl2br(esc_html($item->description)); ?>
                </div>
            <?php endif; ?>
        </article>

        <?php if (!empty($related)): ?>
        <div class="space-y-6 pt-4">
            <h2 class="text-xl font-extrabold text-emerald-950 border-b border-emerald-100 pb-2">Video Lainnya</h2>
            <div class="grid sm:grid-cols-3 gap-6">
                <?php foreach ($related as $rel): ?>
                    <?php $this->load->view('../../themes/islamic/views/components/video-card', array('vid' => $rel)); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="pt-2">
            <a href="<?php echo base_url('video'); ?>" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-white px-5 py-3 rounded-2xl border border-emerald-200 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Daftar Video</span>
            </a>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

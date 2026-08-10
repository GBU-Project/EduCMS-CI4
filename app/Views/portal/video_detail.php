<?php $this->load->view('portal/partials/header');
$this->load->helper('video');
$platform_labels = array('youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'other' => 'Lainnya');

// Embed resolution: YouTube URLs are transformed into a proper embed
// src automatically. For other platforms, if the admin pasted raw
// <iframe> embed markup (e.g. copied from Vimeo/Facebook's own "Embed"
// dialog) it is rendered as-is but restricted to the <iframe> tag only
// (same sanitization approach as the Google Maps embed field in Data
// Sekolah) — never as freeform HTML/script. A plain URL for those
// platforms falls back to an external "Tonton di ..." link instead of a
// guessed iframe src, since their embed URL formats aren't predictable
// from a share link alone.
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

<main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url('video'); ?>" class="hover:text-indigo-600 transition">Video</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium truncate max-w-xs"><?php echo esc_html($item->title); ?></span>
    </nav>

    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-indigo-600"><?php echo $platform_labels[$item->platform] ?? $item->platform; ?></span>
    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-3 mb-6"><?php echo esc_html($item->title); ?></h1>

    <div class="rounded-2xl overflow-hidden mb-8 bg-black aspect-video">
        <?php if ($is_iframe_markup): ?>
            <?php echo strip_tags($item->video_url, '<iframe>'); ?>
        <?php elseif (!empty($embed_src)): ?>
            <iframe src="<?php echo esc_attr($embed_src); ?>" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <?php else: ?>
            <div class="w-full h-full flex flex-col items-center justify-center text-white/70 py-16">
                <i data-lucide="video" class="w-10 h-10 mb-3"></i>
                <a href="<?php echo esc_attr($item->video_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-medium transition">
                    <span>Tonton di <?php echo $platform_labels[$item->platform] ?? $item->platform; ?></span>
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($item->description)): ?>
        <div class="prose prose-slate max-w-none prose-headings:font-bold">
            <?php echo nl2br(esc_html($item->description)); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($related)): ?>
    <div class="mt-14 pt-8 border-t border-slate-100">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Video Lainnya</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            <?php foreach ($related as $rel): $rel_thumb = resolve_video_thumbnail($rel->thumbnail, $rel->platform, $rel->video_url); ?>
                <a href="<?php echo base_url('video/' . $rel->slug); ?>" class="bg-white rounded-xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition group">
                    <div class="h-28 bg-slate-800 relative overflow-hidden">
                        <?php if (!empty($rel_thumb)): ?>
                            <img src="<?php echo esc_attr($rel_thumb); ?>" alt="" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="p-3">
                        <h3 class="font-semibold text-sm text-slate-900 line-clamp-2 group-hover:text-indigo-600"><?php echo esc_html($rel->title); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-10 pt-8 border-t border-slate-100">
        <a href="<?php echo base_url('video'); ?>" class="inline-flex items-center space-x-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Video</span>
        </a>
    </div>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

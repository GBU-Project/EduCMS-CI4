<?php echo view('portal/partials/header');

$platform_labels = array('youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'other' => 'Lainnya');
?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Video</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Dokumentasi</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Video Sekolah</h1>
    </div>

    <?php if (empty($videos)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="video" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Video</h3>
            <p class="text-slate-500 text-sm mt-1">Video yang ditambahkan admin akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($videos as $item): $thumb = resolve_video_thumbnail($item->thumbnail, $item->platform, $item->video_url); ?>
                <a href="<?php echo base_url('video/' . $item->slug); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                    <div class="h-44 bg-slate-800 relative overflow-hidden">
                        <?php if (!empty($thumb)): ?>
                            <img src="<?php echo esc_attr($thumb); ?>" alt="<?php echo esc_attr($item->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center text-white/60">
                                <i data-lucide="video" class="w-10 h-10"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center">
                                <i data-lucide="play" class="w-5 h-5 text-indigo-600 ml-0.5"></i>
                            </div>
                        </div>
                        <?php if (!empty($item->is_featured)): ?>
                            <span class="absolute top-3 left-3 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-400 text-slate-900">Unggulan</span>
                        <?php endif; ?>
                        <span class="absolute top-3 right-3 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-white/90 text-slate-700"><?php echo $platform_labels[$item->platform] ?? $item->platform; ?></span>
                    </div>
                    <div class="p-6 space-y-2">
                        <h3 class="font-bold text-lg text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($item->title); ?></h3>
                        <?php if (!empty($item->description)): ?>
                            <p class="text-sm text-slate-500 line-clamp-2"><?php echo esc_html($item->description); ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="flex justify-center gap-2 mt-10">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="<?php echo base_url('video?page=' . $i); ?>" class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium <?php echo $i === $current_page ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-300'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php echo view('portal/partials/footer'); ?>

<?php echo view('portal/partials/header');
$level_labels = array('kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten/Kota', 'provinsi' => 'Provinsi', 'nasional' => 'Nasional', 'internasional' => 'Internasional');
$type_labels = array('academic' => 'Akademik', 'non-academic' => 'Non-Akademik');
?>

<main class="flex-grow max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <a href="<?php echo base_url('prestasi'); ?>" class="hover:text-indigo-600 transition">Prestasi</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium truncate max-w-xs"><?php echo esc_html($item->title); ?></span>
    </nav>

    <div class="flex flex-wrap gap-2 mb-4">
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-indigo-600"><?php echo $type_labels[$item->type] ?? $item->type; ?></span>
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-amber-50 text-amber-600">Tingkat <?php echo $level_labels[$item->level] ?? $item->level; ?></span>
    </div>

    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-4"><?php echo esc_html($item->title); ?></h1>

    <div class="flex items-center space-x-4 text-sm text-slate-500 mb-8 pb-6 border-b border-slate-100">
        <span class="inline-flex items-center space-x-1.5"><i data-lucide="user" class="w-4 h-4"></i><span><?php echo esc_html($item->winner); ?></span></span>
        <span class="inline-flex items-center space-x-1.5"><i data-lucide="calendar" class="w-4 h-4"></i><span><?php echo date('d M Y', strtotime($item->date)); ?></span></span>
    </div>

    <?php if (!empty($item->image)): ?>
        <div class="rounded-2xl overflow-hidden mb-8 h-72 sm:h-96">
            <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->title); ?>" class="w-full h-full object-cover">
        </div>
    <?php endif; ?>

    <div class="prose prose-slate max-w-none prose-headings:font-bold">
        <?php echo nl2br(esc_html($item->description)); ?>
    </div>

    <div class="mt-10 pt-8 border-t border-slate-100">
        <a href="<?php echo base_url('prestasi'); ?>" class="inline-flex items-center space-x-2 text-sm font-medium text-indigo-600 hover:text-indigo-700">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Prestasi</span>
        </a>
    </div>
</main>

<?php echo view('portal/partials/footer'); ?>

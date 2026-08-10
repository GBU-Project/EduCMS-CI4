<?php $this->load->view('../../themes/islamic/views/partials/header');
$level_labels = array('kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten/Kota', 'provinsi' => 'Provinsi', 'nasional' => 'Nasional', 'internasional' => 'Internasional');
$type_labels = array('academic' => 'Akademik', 'non-academic' => 'Non-Akademik');
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
            <a href="<?php echo base_url('prestasi'); ?>" class="hover:text-emerald-700 transition">Prestasi</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold truncate max-w-xs"><?php echo esc_html($item->title); ?></span>
        </nav>

        <!-- Main Card Container -->
        <article class="bg-white rounded-3xl p-8 sm:p-10 border border-emerald-100 shadow-md space-y-6">
            <div class="flex flex-wrap gap-2">
                <span class="text-xs font-bold uppercase tracking-wider px-3.5 py-1 rounded-full bg-emerald-100/60 text-emerald-800 border border-emerald-200"><?php echo $type_labels[$item->type] ?? $item->type; ?></span>
                <span class="text-xs font-bold uppercase tracking-wider px-3.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">Tingkat <?php echo $level_labels[$item->level] ?? $item->level; ?></span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight"><?php echo esc_html($item->title); ?></h1>

            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 py-3 border-y border-emerald-100/60">
                <span class="inline-flex items-center gap-1.5"><i data-lucide="user" class="w-4 h-4 text-emerald-600"></i><span>Pemenang / Peraih: <strong class="text-emerald-950"><?php echo esc_html($item->winner); ?></strong></span></span>
                <span class="inline-flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i><span>Tanggal: <?php echo date('d M Y', strtotime($item->date)); ?></span></span>
            </div>

            <?php if (!empty($item->image)): ?>
                <div class="rounded-2xl overflow-hidden h-72 sm:h-96 border border-emerald-100 shadow-sm">
                    <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->title); ?>" class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="prose prose-emerald max-w-none text-slate-700 leading-relaxed pt-2">
                <?php echo nl2br(esc_html($item->description)); ?>
            </div>
        </article>

        <div class="pt-2">
            <a href="<?php echo base_url('prestasi'); ?>" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-white px-5 py-3 rounded-2xl border border-emerald-200 shadow-sm transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Daftar Prestasi</span>
            </a>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

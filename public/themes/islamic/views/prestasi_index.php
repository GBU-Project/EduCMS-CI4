<?php $this->load->view('../../themes/islamic/views/partials/header');
$level_labels = array('kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten/Kota', 'provinsi' => 'Provinsi', 'nasional' => 'Nasional', 'internasional' => 'Internasional');
$type_labels = array('academic' => 'Akademik', 'non-academic' => 'Non-Akademik');
?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold">Prestasi Sekolah</span>
        </nav>

        <!-- Header -->
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Galeri Prestasi
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                Prestasi Siswa &amp; Sekolah
            </h1>
        </div>

        <?php if (empty($achievements)): ?>
            <div class="bg-white rounded-3xl border border-emerald-100 p-16 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                    <i data-lucide="trophy" class="w-8 h-8 text-amber-500"></i>
                </div>
                <h3 class="font-extrabold text-emerald-950 text-lg">Belum Ada Data Prestasi</h3>
                <p class="text-slate-500 text-sm mt-1">Prestasi yang ditambahkan admin akan tampil di sini.</p>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($achievements as $item): ?>
                    <a href="<?php echo base_url('prestasi/' . $item->slug); ?>" class="islamic-card bg-white rounded-3xl overflow-hidden border border-emerald-100 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                        <div class="h-48 bg-emerald-900 relative overflow-hidden">
                            <?php if (!empty($item->image)): ?>
                                <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex items-center justify-center text-amber-300">
                                    <i data-lucide="trophy" class="w-12 h-12"></i>
                                </div>
                            <?php endif; ?>
                            <span class="absolute top-3 left-3 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full bg-emerald-900/90 text-amber-300 border border-emerald-700/60 shadow"><?php echo $level_labels[$item->level] ?? $item->level; ?></span>
                        </div>
                        <div class="p-6 space-y-3 flex-grow flex flex-col justify-between">
                            <div class="space-y-2">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-100 inline-block"><?php echo $type_labels[$item->type] ?? $item->type; ?></span>
                                <h3 class="font-extrabold text-base text-emerald-950 leading-snug group-hover:text-emerald-700 transition line-clamp-2"><?php echo esc_html($item->title); ?></h3>
                                <div class="text-xs text-slate-600 font-medium flex items-center gap-1.5 pt-1"><i data-lucide="user" class="w-3.5 h-3.5 text-emerald-600"></i><span><?php echo esc_html($item->winner); ?></span></div>
                            </div>
                            <div class="pt-3 border-t border-slate-100 text-xs text-slate-400 font-semibold">
                                <?php echo date('d M Y', strtotime($item->date)); ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

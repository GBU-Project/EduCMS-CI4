<?php $this->load->view('portal/partials/header');
$level_labels = array('kecamatan' => 'Kecamatan', 'kabupaten' => 'Kabupaten/Kota', 'provinsi' => 'Provinsi', 'nasional' => 'Nasional', 'internasional' => 'Internasional');
$type_labels = array('academic' => 'Akademik', 'non-academic' => 'Non-Akademik');
?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Prestasi Sekolah</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Galeri Prestasi</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Prestasi Siswa &amp; Sekolah</h1>
    </div>

    <?php if (empty($achievements)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="trophy" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Data Prestasi</h3>
            <p class="text-slate-500 text-sm mt-1">Prestasi yang ditambahkan admin akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($achievements as $item): ?>
                <a href="<?php echo base_url('prestasi/' . $item->slug); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                    <div class="h-44 bg-slate-200 relative overflow-hidden">
                        <?php if (!empty($item->image)): ?>
                            <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center text-amber-400">
                                <i data-lucide="trophy" class="w-10 h-10"></i>
                            </div>
                        <?php endif; ?>
                        <span class="absolute top-3 left-3 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-white/90 text-slate-700"><?php echo $level_labels[$item->level] ?? $item->level; ?></span>
                    </div>
                    <div class="p-6 space-y-3">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-indigo-600"><?php echo $type_labels[$item->type] ?? $item->type; ?></span>
                        <h3 class="font-bold text-lg text-slate-900 leading-snug group-hover:text-indigo-600 transition"><?php echo esc_html($item->title); ?></h3>
                        <div class="text-sm text-slate-500 flex items-center space-x-1.5"><i data-lucide="user" class="w-3.5 h-3.5"></i><span><?php echo esc_html($item->winner); ?></span></div>
                        <div class="text-xs text-slate-400"><?php echo date('d M Y', strtotime($item->date)); ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

<?php $this->load->view('portal/partials/header');
$now = date('Y-m-d H:i:s');
$upcoming = array();
$past = array();
foreach ($agendas as $item) {
    if ($item->start_date >= $now) { $upcoming[] = $item; } else { $past[] = $item; }
}
?>

<main class="flex-grow max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Agenda Kegiatan</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Kalender Sekolah</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Agenda Kegiatan</h1>
    </div>

    <?php if (empty($agendas)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="calendar-days" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Agenda</h3>
            <p class="text-slate-500 text-sm mt-1">Agenda kegiatan yang ditambahkan admin akan tampil di sini.</p>
        </div>
    <?php else: ?>

        <?php if (!empty($upcoming)): ?>
        <h2 class="text-lg font-bold text-slate-900 mb-4">Akan Datang</h2>
        <div class="space-y-4 mb-10">
            <?php foreach ($upcoming as $item): ?>
                <a href="<?php echo base_url('agenda/' . $item->slug); ?>" class="flex items-start gap-5 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5 group">
                    <div class="w-16 shrink-0 text-center bg-indigo-50 rounded-xl py-2">
                        <div class="text-xs font-bold text-indigo-600 uppercase"><?php echo date('M', strtotime($item->start_date)); ?></div>
                        <div class="text-xl font-extrabold text-indigo-700"><?php echo date('d', strtotime($item->start_date)); ?></div>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition"><?php echo esc_html($item->title); ?></h3>
                        <div class="flex flex-wrap gap-3 text-xs text-slate-500 mt-2">
                            <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i><?php echo date('H:i', strtotime($item->start_date)); ?> WIB</span>
                            <?php if (!empty($item->location)): ?><span class="inline-flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i><?php echo esc_html($item->location); ?></span><?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($past)): ?>
        <h2 class="text-lg font-bold text-slate-900 mb-4">Sudah Berlalu</h2>
        <div class="space-y-3">
            <?php foreach ($past as $item): ?>
                <a href="<?php echo base_url('agenda/' . $item->slug); ?>" class="flex items-center gap-4 bg-white rounded-xl border border-slate-100 p-4 opacity-70 hover:opacity-100 transition group">
                    <div class="text-xs text-slate-400 w-20 shrink-0"><?php echo date('d M Y', strtotime($item->start_date)); ?></div>
                    <div class="text-sm font-medium text-slate-700 group-hover:text-indigo-600 transition"><?php echo esc_html($item->title); ?></div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

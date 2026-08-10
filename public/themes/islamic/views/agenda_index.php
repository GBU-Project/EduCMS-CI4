<?php $this->load->view('../../themes/islamic/views/partials/header');
$now = date('Y-m-d H:i:s');
$upcoming = array();
$past = array();
foreach ($agendas as $item) {
    if ($item->start_date >= $now) { $upcoming[] = $item; } else { $past[] = $item; }
}
?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold">Agenda Kegiatan</span>
        </nav>

        <!-- Header -->
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Kalender Sekolah
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                Agenda &amp; Kegiatan Sekolah
            </h1>
        </div>

        <?php if (empty($agendas)): ?>
            <div class="bg-white rounded-3xl border border-emerald-100 p-16 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                    <i data-lucide="calendar-days" class="w-8 h-8"></i>
                </div>
                <h3 class="font-extrabold text-emerald-950 text-lg">Belum Ada Agenda</h3>
                <p class="text-slate-500 text-sm mt-1">Agenda kegiatan yang ditambahkan admin akan tampil di sini.</p>
            </div>
        <?php else: ?>

            <?php if (!empty($upcoming)): ?>
            <div class="space-y-4">
                <h2 class="text-lg font-extrabold text-emerald-950 border-b border-emerald-100 pb-2">Akan Datang</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <?php foreach ($upcoming as $item): ?>
                        <?php $this->load->view('../../themes/islamic/views/components/agenda-card', array('item' => $item)); ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($past)): ?>
            <div class="space-y-4 pt-6">
                <h2 class="text-lg font-extrabold text-slate-700 border-b border-slate-200 pb-2">Sudah Berlalu</h2>
                <div class="space-y-3">
                    <?php foreach ($past as $item): ?>
                        <a href="<?php echo base_url('agenda/' . $item->slug); ?>" class="flex items-center gap-4 bg-white rounded-2xl border border-emerald-100/60 p-4 opacity-75 hover:opacity-100 transition group shadow-sm">
                            <div class="text-xs font-bold text-slate-400 w-24 shrink-0"><?php echo date('d M Y', strtotime($item->start_date)); ?></div>
                            <div class="text-sm font-bold text-emerald-950 group-hover:text-emerald-700 transition"><?php echo esc_html($item->title); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

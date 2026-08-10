<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Ekstrakurikuler</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Kegiatan Siswa</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Ekstrakurikuler</h1>
    </div>

    <?php if (empty($items)): ?>
        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 text-center">
            <i data-lucide="volleyball" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
            <h3 class="font-bold text-slate-700">Belum Ada Data Ekstrakurikuler</h3>
            <p class="text-slate-500 text-sm mt-1">Kegiatan ekstrakurikuler yang ditambahkan admin akan tampil di sini.</p>
        </div>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach ($items as $item): ?>
                <a href="<?php echo base_url('ekstrakurikuler/' . $item->slug); ?>" class="bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md transition group">
                    <div class="h-40 bg-slate-100 relative overflow-hidden">
                        <?php if (!empty($item->image)): ?>
                            <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-400">
                                <i data-lucide="volleyball" class="w-10 h-10"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-5 space-y-2">
                        <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition"><?php echo esc_html($item->name); ?></h3>
                        <?php if (!empty($item->schedule)): ?>
                            <p class="text-xs text-slate-500 flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5"></i><?php echo esc_html($item->schedule); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($item->coach)): ?>
                            <p class="text-xs text-slate-500 flex items-center gap-1.5"><i data-lucide="user" class="w-3.5 h-3.5"></i>Pembina: <?php echo esc_html($item->coach); ?></p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

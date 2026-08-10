<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold">Ekstrakurikuler</span>
        </nav>

        <!-- Header -->
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Kegiatan Siswa
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                Program Ekstrakurikuler
            </h1>
        </div>

        <?php if (empty($items)): ?>
            <div class="bg-white rounded-3xl border border-emerald-100 p-16 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                    <i data-lucide="sparkles" class="w-8 h-8 text-amber-500"></i>
                </div>
                <h3 class="font-extrabold text-emerald-950 text-lg">Belum Ada Data Ekstrakurikuler</h3>
                <p class="text-slate-500 text-sm mt-1">Kegiatan ekstrakurikuler yang ditambahkan admin akan tampil di sini.</p>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php foreach ($items as $item): ?>
                    <a href="<?php echo base_url('ekstrakurikuler/' . $item->slug); ?>" class="islamic-card bg-white rounded-3xl overflow-hidden border border-emerald-100 shadow-sm hover:shadow-md transition group flex flex-col">
                        <div class="h-44 bg-emerald-900 relative overflow-hidden">
                            <?php if (!empty($item->image)): ?>
                                <img src="<?php echo base_url($item->image); ?>" alt="<?php echo esc_attr($item->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex flex-col items-center justify-center text-amber-300">
                                    <i data-lucide="sparkles" class="w-12 h-12"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-6 space-y-3 flex-grow flex flex-col justify-between">
                            <div class="space-y-2">
                                <h3 class="font-extrabold text-lg text-emerald-950 group-hover:text-emerald-700 transition leading-snug"><?php echo esc_html($item->name); ?></h3>
                                <?php if (!empty($item->schedule)): ?>
                                    <p class="text-xs text-slate-600 flex items-center gap-1.5 font-medium"><i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i><?php echo esc_html($item->schedule); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($item->coach)): ?>
                                <div class="pt-3 border-t border-slate-100 text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Pembina: <strong class="text-emerald-950"><?php echo esc_html($item->coach); ?></strong></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

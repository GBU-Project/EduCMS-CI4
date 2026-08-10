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
            <span class="text-emerald-950 font-bold">Direktori Staf</span>
        </nav>

        <!-- Header -->
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Tenaga Kependidikan
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                Direktori Staf Kependidikan
            </h1>
        </div>

        <?php if (empty($staff_list)): ?>
            <div class="bg-white rounded-3xl border border-emerald-100 p-16 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h3 class="font-extrabold text-emerald-950 text-lg">Belum Ada Data Staf</h3>
                <p class="text-slate-500 text-sm mt-1">Data staf yang ditambahkan admin akan tampil di sini.</p>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($staff_list as $staff): ?>
                    <a href="<?php echo base_url('staff/' . $staff->id); ?>" class="islamic-card bg-white rounded-3xl overflow-hidden border border-emerald-100 shadow-sm hover:shadow-md transition text-center group flex flex-col">
                        <div class="h-48 bg-emerald-900 relative overflow-hidden">
                            <?php if (!empty($staff->photo)): ?>
                                <img src="<?php echo base_url($staff->photo); ?>" alt="<?php echo esc_attr($staff->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex flex-col items-center justify-center text-emerald-200">
                                    <i data-lucide="user-round" class="w-12 h-12 text-amber-400"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent"></div>
                        </div>
                        <div class="p-5 flex-grow flex flex-col justify-center space-y-1">
                            <h3 class="font-extrabold text-base text-emerald-950 group-hover:text-emerald-700 transition leading-snug"><?php echo esc_html($staff->name); ?></h3>
                            <p class="text-xs text-slate-500 font-medium"><?php echo esc_html($staff->position); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

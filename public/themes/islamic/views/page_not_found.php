<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-20 sm:py-24 bg-slate-50 border-b border-emerald-100 flex items-center">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <div class="w-20 h-20 rounded-3xl bg-emerald-100/70 text-emerald-800 flex items-center justify-center mx-auto border border-emerald-200 shadow-md">
            <i data-lucide="file-question" class="w-10 h-10"></i>
        </div>
        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Informasi System
            </span>
            <h1 class="text-3xl font-extrabold text-emerald-950">Halaman Belum Tersedia</h1>
            <p class="text-slate-600 text-sm sm:text-base max-w-lg mx-auto leading-relaxed">
                Konten untuk halaman ini belum dipublikasikan oleh admin sekolah. Silakan kembali beberapa saat lagi.
            </p>
        </div>
        <div class="pt-2">
            <a href="<?php echo base_url(); ?>" class="inline-flex items-center space-x-2 bg-gradient-to-r from-emerald-800 to-emerald-950 hover:from-emerald-900 hover:to-emerald-900 text-white font-bold px-8 py-3.5 rounded-2xl shadow-lg shadow-emerald-900/20 transition">
                <i data-lucide="home" class="w-4 h-4 text-amber-400"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-24 w-full text-center">
    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-6">
        <i data-lucide="file-question" class="w-9 h-9"></i>
    </div>
    <h1 class="text-2xl font-extrabold text-slate-900">Halaman Belum Tersedia</h1>
    <p class="text-slate-600 mt-3">
        Konten untuk halaman ini belum dipublikasikan oleh admin sekolah. Silakan kembali beberapa saat lagi.
    </p>
    <a href="<?php echo base_url(); ?>" class="inline-flex items-center space-x-2 mt-6 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg shadow-indigo-100 transition">
        <i data-lucide="home" class="w-4 h-4"></i>
        <span>Kembali ke Beranda</span>
    </a>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

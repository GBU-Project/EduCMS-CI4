<?php echo view('portal/partials/header'); ?>

<main class="flex-grow max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-10 text-center space-y-6">
        <div class="w-16 h-16 rounded-full bg-green-50 text-green-600 flex items-center justify-center mx-auto">
            <i data-lucide="circle-check" class="w-9 h-9"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Pendaftaran Berhasil!</h1>
            <p class="text-slate-600 mt-2">
                Terima kasih, <strong><?php echo esc_html($full_name); ?></strong>. Data pendaftaran Anda telah kami terima dan akan diverifikasi oleh pihak sekolah.
            </p>
        </div>
        <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl py-5 px-4">
            <div class="text-xs text-slate-500 uppercase tracking-wider">Nomor Pendaftaran Anda</div>
            <div class="text-2xl font-bold text-indigo-600 tracking-wide mt-1"><?php echo esc_html($registration_number); ?></div>
            <p class="text-xs text-slate-500 mt-2">Simpan nomor ini sebagai bukti pendaftaran.</p>
        </div>
        <a href="<?php echo base_url(); ?>" class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg shadow-indigo-100 transition">
            <i data-lucide="home" class="w-4 h-4"></i>
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</main>

<?php echo view('portal/partials/footer'); ?>

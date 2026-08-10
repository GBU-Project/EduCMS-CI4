<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-16 sm:py-20 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl border border-emerald-100 shadow-xl p-10 text-center space-y-6">
            <div class="w-20 h-20 rounded-full bg-emerald-100/70 text-emerald-700 flex items-center justify-center mx-auto border border-emerald-200">
                <i data-lucide="circle-check" class="w-10 h-10"></i>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-emerald-950">Pendaftaran Berhasil!</h1>
                <p class="text-slate-600 text-sm sm:text-base mt-2">
                    Terima kasih, <strong class="text-emerald-950"><?php echo esc_html($full_name); ?></strong>. Data pendaftaran Anda telah kami terima dan akan diverifikasi oleh pihak sekolah.
                </p>
            </div>
            <div class="bg-emerald-50/60 border border-dashed border-emerald-200 rounded-2xl py-6 px-4 space-y-1">
                <div class="text-xs text-emerald-800 uppercase font-bold tracking-widest">Nomor Pendaftaran Anda</div>
                <div class="text-3xl font-extrabold text-emerald-900 tracking-wide"><?php echo esc_html($registration_number); ?></div>
                <p class="text-xs text-slate-500 font-medium pt-1">Simpan nomor pendaftaran ini sebagai bukti verifikasi berkas.</p>
            </div>
            <div class="pt-2">
                <a href="<?php echo base_url(); ?>" class="inline-flex items-center space-x-2 bg-gradient-to-r from-emerald-800 to-emerald-950 hover:from-emerald-900 hover:to-emerald-900 text-white font-bold px-8 py-3.5 rounded-2xl shadow-lg shadow-emerald-900/20 transition">
                    <i data-lucide="home" class="w-4 h-4 text-amber-400"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

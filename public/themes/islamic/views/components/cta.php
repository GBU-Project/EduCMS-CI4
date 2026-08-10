<!-- CTA Component (PPDB & Contact Banner) -->
<?php if (!empty($show_ppdb_section)): ?>
<section class="py-16 sm:py-20 bg-gradient-to-br from-emerald-900 via-emerald-800 to-emerald-950 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-islamic-pattern pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 relative">
        <span class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-400"></i>
            <span><?php echo !empty($hp_cta_subtitle) ? esc_html($hp_cta_subtitle) : (!empty($hp_ppdb_subtitle) ? esc_html($hp_ppdb_subtitle) : 'Tahun Ajaran Baru'); ?></span>
        </span>
        
        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
            <?php echo !empty($hp_cta_title) ? esc_html($hp_cta_title) : (!empty($hp_ppdb_title) ? esc_html($hp_ppdb_title) : 'Penerimaan Peserta Didik Baru (PPDB)'); ?>
        </h2>
        
        <p class="text-emerald-100 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
            Bergabunglah menjadi bagian dari keluarga besar sekolah kami. Proses pendaftaran cepat, transparan, dan dapat dilakukan secara online.
        </p>

        <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
            <a href="<?php echo base_url('ppdb'); ?>" class="inline-flex items-center justify-center space-x-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-amber-900/30 transition transform hover:-translate-y-0.5">
                <span>Daftar PPDB Online</span>
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>
            <a href="<?php echo base_url('kontak'); ?>" class="inline-flex items-center justify-center space-x-2 bg-emerald-800/80 hover:bg-emerald-700 text-emerald-100 px-8 py-4 rounded-2xl font-bold border border-emerald-700 transition">
                <span>Konsultasi &amp; Kontak</span>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

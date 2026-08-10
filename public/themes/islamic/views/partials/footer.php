<!-- Footer -->
<footer class="bg-gradient-to-b from-emerald-950 to-slate-950 text-slate-300 border-t-4 border-amber-500 pt-16 pb-12 mt-auto relative overflow-hidden">
    <div class="absolute inset-0 opacity-5 pointer-events-none bg-islamic-pattern"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-10">
        
        <!-- School Information Column -->
        <div class="md:col-span-5 space-y-5">
            <div class="flex items-center space-x-3.5">
                <?php if (site_logo()): ?>
                    <img src="<?php echo esc_attr(site_logo()); ?>" alt="<?php echo esc_attr(site_name()); ?>" class="w-10 h-10 object-contain rounded-lg">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-emerald-950 font-bold shadow-md">
                        <i data-lucide="building-2" class="w-6 h-6"></i>
                    </div>
                <?php endif; ?>
                <span class="text-xl font-bold tracking-tight text-white">
                    <?php echo esc_html(site_name()); ?>
                </span>
            </div>
            <p class="text-sm text-emerald-100/75 leading-relaxed max-w-md">
                <?php echo esc_html(site_tagline('Mewujudkan generasi unggul, berakhlak mulia, berprestasi, dan berwawasan global berbasis nilai-nilai Islam.')); ?>
            </p>
            <div class="space-y-2 text-xs text-emerald-200/80 pt-2">
                <p class="flex items-start gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i><span><?php echo esc_html(site_address('Alamat sekolah belum diatur.')); ?></span></p>
                <p class="flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4 text-amber-400 shrink-0"></i><span><?php echo esc_html(site_phone('Telepon sekolah belum diatur')); ?></span></p>
                <p class="flex items-center gap-2"><i data-lucide="mail" class="w-4 h-4 text-amber-400 shrink-0"></i><span><?php echo esc_html(site_email('Email sekolah belum diatur')); ?></span></p>
            </div>
        </div>

        <!-- Navigation Links Column -->
        <div class="md:col-span-3 space-y-4">
            <h4 class="text-white font-bold text-base tracking-wide border-b border-emerald-800/80 pb-2 inline-block">Navigasi Utama</h4>
            <ul class="space-y-2.5 text-sm">
                <?php if (!empty($footer_menu)): ?>
                    <?php echo render_menu_footer($footer_menu); ?>
                <?php else: ?>
                    <li><a href="<?php echo base_url(); ?>" class="hover:text-amber-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-500"></i>Beranda</a></li>
                    <li><a href="<?php echo base_url('berita'); ?>" class="hover:text-amber-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-500"></i>Berita &amp; Artikel</a></li>
                    <li><a href="<?php echo base_url('pengumuman'); ?>" class="hover:text-amber-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-500"></i>Pengumuman</a></li>
                    <li><a href="<?php echo base_url('agenda'); ?>" class="hover:text-amber-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-500"></i>Agenda Sekolah</a></li>
                    <li><a href="<?php echo base_url('ekstrakurikuler'); ?>" class="hover:text-amber-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-500"></i>Program Ekstrakurikuler</a></li>
                    <li><a href="<?php echo base_url('ppdb'); ?>" class="hover:text-amber-400 transition flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-amber-500"></i>Pendaftaran PPDB</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Social Media & Accreditation Column -->
        <div class="md:col-span-4 space-y-4">
            <h4 class="text-white font-bold text-base tracking-wide border-b border-emerald-800/80 pb-2 inline-block">Media Sosial &amp; Jejaring</h4>
            <p class="text-xs text-slate-400">Ikuti perkembangan kegiatan dan informasi harian sekolah kami melalui kanal resmi:</p>
            <div class="flex items-center space-x-3 pt-1">
                <?php if (!empty($site_settings['social']['facebook'])): ?>
                    <a href="<?php echo esc_attr($site_settings['social']['facebook']); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-emerald-900/80 hover:bg-amber-500 hover:text-emerald-950 text-emerald-100 flex items-center justify-center transition border border-emerald-800/60 shadow-sm"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                <?php endif; ?>
                <?php if (!empty($site_settings['social']['instagram'])): ?>
                    <a href="<?php echo esc_attr($site_settings['social']['instagram']); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-emerald-900/80 hover:bg-amber-500 hover:text-emerald-950 text-emerald-100 flex items-center justify-center transition border border-emerald-800/60 shadow-sm"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                <?php endif; ?>
                <?php if (!empty($site_settings['social']['youtube'])): ?>
                    <a href="<?php echo esc_attr($site_settings['social']['youtube']); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-emerald-900/80 hover:bg-amber-500 hover:text-emerald-950 text-emerald-100 flex items-center justify-center transition border border-emerald-800/60 shadow-sm"><i data-lucide="youtube" class="w-5 h-5"></i></a>
                <?php endif; ?>
                <?php if (empty($site_settings['social']['facebook']) && empty($site_settings['social']['instagram']) && empty($site_settings['social']['youtube'])): ?>
                    <span class="text-xs text-emerald-300/60 italic">Media sosial dapat diatur di admin settings.</span>
                <?php endif; ?>
            </div>
            <div class="pt-4 border-t border-emerald-900 text-xs text-emerald-300/70">
                <?php echo site_footer(); ?>
            </div>
        </div>
    </div>
</footer>

<!-- Global Floating Actions (Shared Component) -->
<?php $this->load->view('portal/partials/floating_actions'); ?>

<script src="<?php echo base_url('themes/islamic/assets/js/theme-islamic.js?v=' . time()); ?>"></script>

</body>
</html>


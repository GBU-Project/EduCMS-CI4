    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-4 gap-8">
            <div class="space-y-4 col-span-2">
                <div class="flex items-center space-x-2 text-white">
                    <?php if (site_logo()): ?>
                        <img src="<?php echo esc_attr(site_logo()); ?>" alt="<?php echo esc_attr(site_name()); ?>" class="w-8 h-8 object-contain">
                    <?php else: ?>
                        <i data-lucide="graduation-cap" class="w-8 h-8 text-indigo-500"></i>
                    <?php endif; ?>
                    <span class="text-lg font-bold tracking-tight">
                        <?php echo esc_html(site_name()); ?>
                    </span>
                </div>
                <p class="text-sm max-w-sm">
                    <?php echo esc_html(site_tagline('Platform website sekolah yang aman, cepat, dan modern berbasis kerangka kerja CodeIgniter 3 dengan integrasi sistem manajemen konten (CMS) yang lengkap.')); ?>
                </p>
            </div>
            <div class="space-y-4">
                <h4 class="text-white font-semibold text-sm">Tautan Cepat</h4>
                <ul class="space-y-2 text-sm">
                    <?php echo render_menu_footer($footer_menu); ?>
                </ul>
            </div>
            <div class="space-y-4">
                <h4 class="text-white font-semibold text-sm">Media Sosial</h4>
                <div class="flex space-x-4">
                    <?php if (!empty($site_settings['social']['facebook'])): ?>
                        <a href="<?php echo esc_attr($site_settings['social']['facebook']); ?>" target="_blank" rel="noopener" class="hover:text-white transition"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($site_settings['social']['instagram'])): ?>
                        <a href="<?php echo esc_attr($site_settings['social']['instagram']); ?>" target="_blank" rel="noopener" class="hover:text-white transition"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($site_settings['social']['youtube'])): ?>
                        <a href="<?php echo esc_attr($site_settings['social']['youtube']); ?>" target="_blank" rel="noopener" class="hover:text-white transition"><i data-lucide="youtube" class="w-5 h-5"></i></a>
                    <?php endif; ?>
                    <?php if (empty($site_settings['social']['facebook']) && empty($site_settings['social']['instagram']) && empty($site_settings['social']['youtube'])): ?>
                        <span class="text-xs text-slate-600">Belum diatur di Settings</span>
                    <?php endif; ?>
                </div>
                <div class="text-xs text-slate-500 pt-4">
                    <?php echo site_footer(); ?>
                </div>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
        var mobileToggle = document.getElementById('mobile-menu-toggle');
        var mobilePanel = document.getElementById('mobile-menu-panel');
        if (mobileToggle && mobilePanel) {
            mobileToggle.addEventListener('click', function () {
                mobilePanel.classList.toggle('hidden');
            });
        }
    </script>
    <?php echo view('portal/partials/floating_actions', $data ?? []); ?>
</body>
</html>



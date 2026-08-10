<!-- Top Contact & Utility Bar -->
<div class="bg-emerald-900 text-emerald-100 text-xs py-2 px-4 sm:px-6 lg:px-8 border-b border-emerald-800/60">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center space-x-4">
            <span class="flex items-center gap-1.5"><i data-lucide="phone" class="w-3.5 h-3.5 text-amber-400"></i><?php echo esc_html(site_phone('Telepon Sekolah')); ?></span>
            <span class="hidden md:flex items-center gap-1.5"><i data-lucide="mail" class="w-3.5 h-3.5 text-amber-400"></i><?php echo esc_html(site_email('info@sekolah.sch.id')); ?></span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="bg-emerald-800/80 px-2.5 py-0.5 rounded-full text-[11px] font-medium border border-emerald-700/50">
                NPSN: <?php echo isset($site_settings['school']['npsn']) ? esc_html($site_settings['school']['npsn']) : '10293847'; ?> • Akreditasi <?php echo isset($site_settings['school']['accreditation']) ? esc_html($site_settings['school']['accreditation']) : 'A'; ?>
            </span>
            <a href="<?php echo base_url('admin/login'); ?>" class="hover:text-amber-300 transition flex items-center gap-1 font-semibold">
                <i data-lucide="log-in" class="w-3.5 h-3.5"></i> Admin
            </a>
        </div>
    </div>
</div>

<!-- Main Navigation Bar -->
<header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-emerald-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            
            <!-- Brand / School Logo & Title -->
            <a href="<?php echo base_url(); ?>" class="flex items-center space-x-3.5 group">
                <?php if (site_logo()): ?>
                    <img src="<?php echo esc_attr(site_logo()); ?>" alt="<?php echo esc_attr(site_name()); ?>" class="w-12 h-12 rounded-xl object-contain shadow-sm">
                <?php else: ?>
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 flex items-center justify-center text-white shadow-md shadow-emerald-900/20 group-hover:scale-105 transition">
                        <i data-lucide="building-2" class="w-6 h-6 text-amber-300"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <span class="text-xl font-bold tracking-tight text-emerald-950 group-hover:text-emerald-700 transition block">
                        <?php echo esc_html(site_name()); ?>
                    </span>
                    <span class="text-xs text-slate-500 font-medium tracking-wide block">
                        <?php echo esc_html(site_tagline('Pendidikan Islam Berkualitas & Berkarakter')); ?>
                    </span>
                </div>
            </a>

            <!-- Desktop Menu Dynamic Header Menu -->
            <nav class="hidden lg:flex items-center space-x-1">
                <?php if (!empty($header_menu)): ?>
                    <?php foreach ($header_menu as $item): ?>
                        <?php if (!empty($item->children)): ?>
                            <!-- Dropdown Menu Item -->
                            <div class="relative group">
                                <a href="<?php echo resolve_menu_url($item->url); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition inline-flex items-center gap-1">
                                    <span><?php echo esc_html($item->title); ?></span>
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </a>
                                <div class="islamic-desktop-dropdown absolute left-0 mt-1 w-56 bg-white rounded-2xl shadow-xl border border-emerald-100 py-2 hidden group-hover:block animate-in fade-in duration-200 z-50">
                                    <?php foreach ($item->children as $child): ?>
                                        <a href="<?php echo resolve_menu_url($child->url); ?>" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition font-medium">
                                            <?php echo esc_html($child->title); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo resolve_menu_url($item->url); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition">
                                <?php echo esc_html($item->title); ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default Nav Fallback with Submenus -->
                    <a href="<?php echo base_url(); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-emerald-700 bg-emerald-50">Beranda</a>
                    <a href="<?php echo base_url('berita'); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition">Berita</a>
                    <a href="<?php echo base_url('pengumuman'); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition">Pengumuman</a>
                    <a href="<?php echo base_url('agenda'); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition">Agenda</a>
                    <div class="relative group">
                        <button type="button" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition inline-flex items-center gap-1">
                            <span>Direktori</span>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="islamic-desktop-dropdown absolute left-0 mt-1 w-56 bg-white rounded-2xl shadow-xl border border-emerald-100 py-2 hidden group-hover:block animate-in fade-in duration-200 z-50">
                            <a href="<?php echo base_url('guru'); ?>" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition font-medium">Direktori Guru</a>
                            <a href="<?php echo base_url('staff'); ?>" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition font-medium">Direktori Staf</a>
                            <a href="<?php echo base_url('prestasi'); ?>" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition font-medium">Prestasi Sekolah</a>
                            <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition font-medium">Ekstrakurikuler</a>
                        </div>
                    </div>
                    <a href="<?php echo base_url('galeri-foto'); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition">Galeri</a>
                    <a href="<?php echo base_url('video'); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 transition">Video</a>
                <?php endif; ?>

                <a href="<?php echo base_url('ppdb'); ?>" class="ml-3 inline-flex items-center space-x-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-amber-500/20 transition transform hover:-translate-y-0.5">
                    <span>PPDB Online</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </nav>

            <!-- Mobile Toggle Button -->
            <button type="button" 
                    id="islamic-mobile-toggle" 
                    class="lg:hidden p-2.5 rounded-xl text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition focus:outline-none focus:ring-2 focus:ring-emerald-500" 
                    aria-label="Navigasi Utama Mobile" 
                    aria-expanded="false" 
                    aria-controls="islamic-mobile-menu">
                <svg class="islamic-menu-open-icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg class="islamic-menu-close-icon w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Panel -->
    <div id="islamic-mobile-menu" class="hidden lg:hidden border-t border-emerald-100 bg-white px-4 pt-3 pb-6 space-y-1 shadow-lg transition-all duration-300 ease-in-out">
        <?php if (!empty($header_menu)): ?>
            <?php foreach ($header_menu as $item): ?>
                <?php $has_children = !empty($item->children); ?>
                <?php if ($has_children && (trim((string)$item->url) === '' || trim((string)$item->url) === '#')): ?>
                    <div class="px-4 pt-3 pb-1 text-xs font-bold uppercase tracking-wider text-emerald-800/80 flex items-center gap-1.5">
                        <span><?php echo esc_html($item->title); ?></span>
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                <?php else: ?>
                    <a href="<?php echo resolve_menu_url($item->url); ?>" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-800 hover:bg-emerald-50 hover:text-emerald-700 transition flex items-center justify-between">
                        <span><?php echo esc_html($item->title); ?></span>
                        <?php if ($has_children): ?>
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if ($has_children): ?>
                    <div class="pl-4 ml-3 border-l-2 border-emerald-200/80 space-y-1 my-1">
                        <?php foreach ($item->children as $child): ?>
                            <a href="<?php echo resolve_menu_url($child->url); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span><?php echo esc_html($child->title); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Default Mobile Fallback with Dropdown Submenus -->
            <a href="<?php echo base_url(); ?>" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-emerald-700 bg-emerald-50">Beranda</a>
            <a href="<?php echo base_url('berita'); ?>" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50">Berita &amp; Artikel</a>
            <a href="<?php echo base_url('pengumuman'); ?>" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50">Pengumuman</a>
            <a href="<?php echo base_url('agenda'); ?>" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50">Agenda</a>

            <!-- Direktori Submenu -->
            <div class="px-4 pt-3 pb-1 text-xs font-bold uppercase tracking-wider text-emerald-800/80 flex items-center gap-1.5">
                <span>Direktori Sekolah</span>
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div class="pl-4 ml-3 border-l-2 border-emerald-200/80 space-y-1 my-1">
                <a href="<?php echo base_url('guru'); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Direktori Guru</span>
                </a>
                <a href="<?php echo base_url('staff'); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Direktori Staf</span>
                </a>
                <a href="<?php echo base_url('prestasi'); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Prestasi Sekolah</span>
                </a>
                <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Program Ekstrakurikuler</span>
                </a>
            </div>

            <a href="<?php echo base_url('galeri-foto'); ?>" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50">Galeri Foto</a>
            <a href="<?php echo base_url('video'); ?>" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50">Video Profil</a>
        <?php endif; ?>
        <a href="<?php echo base_url('ppdb'); ?>" class="block text-center bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold px-4 py-3 rounded-xl shadow-md mt-4">
            Daftar PPDB Online
        </a>
    </div>
</header>

<script>
(function() {
    function bindIslamicMobileMenu() {
        var btn = document.getElementById('islamic-mobile-toggle');
        var menu = document.getElementById('islamic-mobile-menu');
        if (btn && menu && !btn.getAttribute('data-bound')) {
            btn.setAttribute('data-bound', 'true');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var isExpanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', !isExpanded);
                menu.classList.toggle('hidden');

                var openIcon = btn.querySelector('.islamic-menu-open-icon');
                var closeIcon = btn.querySelector('.islamic-menu-close-icon');
                if (openIcon && closeIcon) {
                    openIcon.classList.toggle('hidden');
                    closeIcon.classList.toggle('hidden');
                }
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindIslamicMobileMenu);
    } else {
        bindIslamicMobileMenu();
    }
})();
</script>





<!-- Hero / Slider Component -->
<?php if (!empty($show_hero_section)): ?>
<section class="relative bg-emerald-950 text-white overflow-hidden" id="islamic-hero-slider">
    <div class="absolute inset-0 opacity-10 bg-islamic-pattern pointer-events-none"></div>

    <?php if (!empty($sliders)): ?>
    <div class="relative h-[480px] lg:h-[600px]">
        <?php foreach ($sliders as $i => $slide): ?>
        <div class="islamic-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?php echo $i === 0 ? 'opacity-100 z-10' : 'opacity-0 pointer-events-none z-0'; ?>" data-slide-index="<?php echo $i; ?>">
            <div class="absolute inset-0">
                <img src="<?php echo base_url($slide->image); ?>" alt="<?php echo esc_attr($slide->title); ?>" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/75 to-transparent"></div>
            </div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
                <div class="max-w-2xl space-y-6">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Selamat Datang di Portal Resmi</span>
                    </div>
                    <?php if (!empty($slide->title)): ?>
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-white"><?php echo esc_html($slide->title); ?></h1>
                    <?php endif; ?>
                    <?php if (!empty($slide->subtitle)): ?>
                        <p class="text-emerald-100 text-base sm:text-lg leading-relaxed"><?php echo esc_html($slide->subtitle); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($slide->link)): ?>
                        <div class="pt-2">
                            <a href="<?php echo resolve_menu_url($slide->link); ?>" class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-7 py-3.5 rounded-xl font-bold shadow-xl shadow-amber-900/30 transition transform hover:-translate-y-0.5">
                                <span>Selengkapnya</span>
                                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($sliders) > 1): ?>
    <!-- Prev / Next Navigation Arrows -->
    <button type="button" class="islamic-prev-slide absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-2xl bg-emerald-900/60 hover:bg-emerald-800 text-amber-300 border border-emerald-700/50 flex items-center justify-center backdrop-blur-sm transition focus:outline-none" aria-label="Slide Sebelumnya">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
    </button>
    <button type="button" class="islamic-next-slide absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-2xl bg-emerald-900/60 hover:bg-emerald-800 text-amber-300 border border-emerald-700/50 flex items-center justify-center backdrop-blur-sm transition focus:outline-none" aria-label="Slide Berikutnya">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
    </button>

    <!-- Pagination Dots -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex items-center space-x-2">
        <?php foreach ($sliders as $i => $slide): ?>
            <button type="button" class="islamic-dot h-2.5 rounded-full transition-all duration-300 <?php echo $i === 0 ? 'bg-amber-400 w-8' : 'bg-white/40 w-2.5'; ?>" data-goto="<?php echo $i; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <script>
    (function () {
        function initHeroSlider() {
            var sliderContainer = document.getElementById('islamic-hero-slider');
            if (!sliderContainer) return;

            var slides = sliderContainer.querySelectorAll('.islamic-slide');
            var dots = sliderContainer.querySelectorAll('.islamic-dot');
            var prevBtn = sliderContainer.querySelector('.islamic-prev-slide');
            var nextBtn = sliderContainer.querySelector('.islamic-next-slide');

            if (slides.length < 2) return;

            var currentIndex = 0;
            var slideInterval = null;

            function showSlide(index) {
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;

                slides.forEach(function (slide, i) {
                    if (i === index) {
                        slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                        slide.classList.add('opacity-100', 'z-10');
                    } else {
                        slide.classList.remove('opacity-100', 'z-10');
                        slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                    }
                });

                dots.forEach(function (dot, i) {
                    if (i === index) {
                        dot.classList.remove('bg-white/40', 'w-2.5');
                        dot.classList.add('bg-amber-400', 'w-8');
                    } else {
                        dot.classList.remove('bg-amber-400', 'w-8');
                        dot.classList.add('bg-white/40', 'w-2.5');
                    }
                });

                currentIndex = index;
            }

            function startAutoplay() {
                stopAutoplay();
                slideInterval = setInterval(function () {
                    showSlide(currentIndex + 1);
                }, 6000);
            }

            function stopAutoplay() {
                if (slideInterval) {
                    clearInterval(slideInterval);
                    slideInterval = null;
                }
            }

            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    stopAutoplay();
                    var gotoIndex = parseInt(dot.getAttribute('data-goto'), 10);
                    showSlide(gotoIndex);
                    startAutoplay();
                });
            });

            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    stopAutoplay();
                    showSlide(currentIndex - 1);
                    startAutoplay();
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    stopAutoplay();
                    showSlide(currentIndex + 1);
                    startAutoplay();
                });
            }

            startAutoplay();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHeroSlider);
        } else {
            initHeroSlider();
        }
    })();
    </script>


    <?php else: ?>
    <!-- Fallback Hero Header when Sliders table is empty -->
    <div class="relative py-20 lg:py-28 bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-800/60 text-emerald-200 border border-emerald-700/60">
                        <i data-lucide="bookmark" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span><?php echo !empty($hp_hero_title) ? esc_html($hp_hero_title) : 'Selamat Datang'; ?></span>
                    </span>
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                        Membangun Generasi <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-amber-400 to-amber-200">Islami, Unggul &amp; Berkarakter</span>
                    </h1>
                    <p class="text-emerald-100/90 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0">
                        <?php echo isset($site_settings['general']['site_tagline']) ? esc_html($site_settings['general']['site_tagline']) : 'Platform pendidikan islami yang berkomitmen membentuk generasi berilmu dan berakhlak mulia.'; ?>
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 pt-2">
                        <a href="<?php echo base_url('ppdb'); ?>" class="inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 text-white px-7 py-3.5 rounded-xl font-bold shadow-lg shadow-amber-900/30 transition transform hover:-translate-y-0.5">
                            <span>Daftar PPDB Online</span>
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                        <a href="<?php echo base_url('berita'); ?>" class="inline-flex items-center justify-center space-x-2 bg-emerald-900/80 hover:bg-emerald-800 text-emerald-100 px-7 py-3.5 rounded-xl font-semibold border border-emerald-700/60 transition">
                            <span>Informasi Terbaru</span>
                        </a>
                    </div>
                </div>
                
                <div class="lg:col-span-5">
                    <div class="bg-emerald-900/40 backdrop-blur-md rounded-3xl border border-emerald-700/40 p-6 sm:p-8 space-y-6 shadow-2xl">
                        <h3 class="text-lg font-bold tracking-wide text-white border-b border-emerald-700/50 pb-3.5 flex items-center gap-2">
                            <i data-lucide="info" class="w-5 h-5 text-amber-400"></i>
                            <span>Informasi Satuan Pendidikan</span>
                        </h3>
                        <div class="space-y-4 text-sm">
                            <div class="flex items-start space-x-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-800/80 flex items-center justify-center text-amber-400 shrink-0">
                                    <i data-lucide="user-check" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-emerald-300 uppercase tracking-wider font-semibold">Kepala Sekolah</div>
                                    <div class="text-base font-bold text-white"><?php echo isset($site_settings['school']['principal_name']) ? esc_html($site_settings['school']['principal_name']) : 'Belum diatur'; ?></div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-800/80 flex items-center justify-center text-amber-400 shrink-0">
                                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-emerald-300 uppercase tracking-wider font-semibold">Alamat Lengkap</div>
                                    <div class="text-sm font-medium text-white"><?php echo esc_html(site_address('Alamat belum diatur')); ?></div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-800/80 flex items-center justify-center text-amber-400 shrink-0">
                                    <i data-lucide="phone-call" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-emerald-300 uppercase tracking-wider font-semibold">Kontak Layanan</div>
                                    <div class="text-sm font-medium text-white"><?php echo esc_html(site_phone('Kontak belum diatur')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>
<?php endif; ?>

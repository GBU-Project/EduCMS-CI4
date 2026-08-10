/**
 * EduCMS Theme Islamic v1.0 - Interactive Scripting
 * Lightweight client interactions: Hero slider, Mobile navigation, Back to top button.
 */
function initIslamicThemeScript() {
    // 1. Initialize Lucide Icons
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
    }

    // 2. Mobile Menu Drawer Toggle
    var mobileToggle = document.getElementById('islamic-mobile-toggle');
    var mobileMenu = document.getElementById('islamic-mobile-menu');
    if (mobileToggle && mobileMenu) {
        if (!mobileToggle.getAttribute('data-bound')) {
            mobileToggle.setAttribute('data-bound', 'true');
            mobileToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var isExpanded = mobileToggle.getAttribute('aria-expanded') === 'true';
                mobileToggle.setAttribute('aria-expanded', !isExpanded);
                mobileMenu.classList.toggle('hidden');

                var openIcon = mobileToggle.querySelector('.islamic-menu-open-icon');
                var closeIcon = mobileToggle.querySelector('.islamic-menu-close-icon');
                if (openIcon && closeIcon) {
                    openIcon.classList.toggle('hidden');
                    closeIcon.classList.toggle('hidden');
                }
            });
        }
    }

    // 3. Hero Slider Auto Play & Manual Controls
    var sliderEl = document.getElementById('islamic-hero-slider');
    if (sliderEl && !sliderEl.getAttribute('data-bound')) {
        sliderEl.setAttribute('data-bound', 'true');
        var slides = sliderEl.querySelectorAll('.islamic-slide');
        var dots = sliderEl.querySelectorAll('.islamic-dot');
        if (slides.length > 1) {
            var currentIndex = 0;
            var slideInterval;

            function showSlide(index) {
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
                        dot.classList.remove('bg-white/40');
                        dot.classList.add('bg-amber-400', 'w-8');
                    } else {
                        dot.classList.remove('bg-amber-400', 'w-8');
                        dot.classList.add('bg-white/40', 'w-2.5');
                    }
                });
                currentIndex = index;
            }

            function startTimer() {
                slideInterval = setInterval(function () {
                    showSlide((currentIndex + 1) % slides.length);
                }, 6000);
            }

            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    clearInterval(slideInterval);
                    var gotoIndex = parseInt(dot.getAttribute('data-goto'), 10);
                    showSlide(gotoIndex);
                    startTimer();
                });
            });

            startTimer();
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initIslamicThemeScript);
} else {
    initIslamicThemeScript();
}


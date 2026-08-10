<?php
$wa_url = site_wa_url();
?>
<!-- Global Floating Action Buttons (Shared Component) -->
<div id="global-floating-actions" 
     style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99999 !important; display: flex !important; flex-direction: column !important; align-items: center !important; gap: 12px !important; pointer-events: none !important;">
    
    <?php if (!empty($wa_url)): ?>
    <!-- Floating WhatsApp Button -->
    <a href="<?php echo esc_attr($wa_url); ?>" 
       target="_blank" 
       rel="noopener noreferrer" 
       aria-label="Hubungi kami via WhatsApp"
       title="Chat WhatsApp"
       style="pointer-events: auto !important; width: 50px !important; height: 50px !important; min-width: 50px !important; min-height: 50px !important; border-radius: 50% !important; background-color: #25D366 !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; box-shadow: 0 4px 16px rgba(0,0,0,0.3) !important; text-decoration: none !important; transition: transform 0.2s ease, box-shadow 0.2s ease;"
       onmouseover="this.style.transform='scale(1.12)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.4)';"
       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 16px rgba(0,0,0,0.3)';">
        <svg style="width: 28px !important; height: 28px !important; fill: #ffffff !important;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
    </a>
    <?php endif; ?>

    <!-- Back to Top Button -->
    <button id="global-back-to-top" 
            type="button" 
            aria-label="Kembali ke atas"
            title="Kembali ke Atas"
            style="pointer-events: auto !important; width: 44px !important; height: 44px !important; min-width: 44px !important; min-height: 44px !important; border-radius: 50% !important; background-color: #0f172a !important; color: #ffffff !important; display: flex !important; align-items: center !important; justify-content: center !important; box-shadow: 0 4px 14px rgba(0,0,0,0.3) !important; border: none !important; cursor: pointer !important; opacity: 0; pointer-events: none; transition: opacity 0.3s ease, transform 0.2s ease;"
            onmouseover="this.style.transform='scale(1.12)'"
            onmouseout="this.style.transform='scale(1)'">
        <svg style="width: 22px !important; height: 22px !important; stroke: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
        </svg>
    </button>
</div>

<script>
(function() {
    function initFloatingActions() {
        var backToTopBtn = document.getElementById('global-back-to-top');
        if (backToTopBtn) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 200) {
                    backToTopBtn.style.opacity = '1';
                    backToTopBtn.style.pointerEvents = 'auto';
                } else {
                    backToTopBtn.style.opacity = '0';
                    backToTopBtn.style.pointerEvents = 'none';
                }
            });

            backToTopBtn.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFloatingActions);
    } else {
        initFloatingActions();
    }
})();
</script>

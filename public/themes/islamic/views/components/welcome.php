<?php 
$welcome_text = isset($site_settings['school']['welcome_speech']) ? trim($site_settings['school']['welcome_speech']) : '';
$principal_name = !empty($site_settings['school']['headmaster']) ? $site_settings['school']['headmaster'] : (!empty($site_settings['school']['principal_name']) ? $site_settings['school']['principal_name'] : 'Kepala Sekolah');

// Option 1: Render section ONLY if real welcome_speech data exists in CMS.
// No fictional or hardcoded fallback paragraphs are generated.
if (!empty($welcome_text)):
?>
<!-- Principal Welcome Component -->
<section class="py-16 sm:py-20 bg-white border-b border-emerald-100 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            
            <!-- Principal Photo Card -->
            <div class="lg:col-span-5 relative">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-emerald-50 bg-emerald-900 group">
                    <?php if (!empty($site_settings['school']['principal_photo'])): ?>
                        <img src="<?php echo base_url($site_settings['school']['principal_photo']); ?>" alt="Kepala Sekolah" class="w-full h-[400px] object-cover object-top group-hover:scale-105 transition duration-500">
                    <?php else: ?>
                        <div class="w-full h-[380px] bg-gradient-to-br from-emerald-800 to-emerald-950 flex flex-col items-center justify-center text-emerald-200 p-6 text-center">
                            <div class="w-24 h-24 rounded-full bg-emerald-700/60 flex items-center justify-center mb-4 text-amber-400">
                                <i data-lucide="user" class="w-12 h-12"></i>
                            </div>
                            <span class="text-sm font-semibold text-emerald-100"><?php echo esc_html($principal_name); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-transparent to-transparent flex flex-col justify-end p-6 text-white">
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-400">Kepala Sekolah</span>
                        <h3 class="text-xl font-bold mt-0.5"><?php echo esc_html($principal_name); ?></h3>
                    </div>
                </div>
            </div>

            <!-- Welcome Text -->
            <div class="lg:col-span-7 space-y-6">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/60 inline-block">
                        <?php echo !empty($hp_welcome_subtitle) ? esc_html($hp_welcome_subtitle) : 'Sambutan Kepala Sekolah'; ?>
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950 leading-tight">
                        <?php echo !empty($hp_welcome_title) ? esc_html($hp_welcome_title) : ('Selamat Datang di Portal Resmi ' . esc_html(site_name())); ?>
                    </h2>
                </div>

                <div class="prose prose-emerald text-slate-600 text-base leading-relaxed space-y-4">
                    <?php echo nl2br(esc_html($welcome_text)); ?>
                </div>

                <div class="pt-2 flex items-center gap-4">
                    <a href="<?php echo base_url('kontak'); ?>" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-6 py-3 rounded-xl shadow-md transition">
                        <span>Hubungi Kami</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>




<!-- Programs / Ekstrakurikuler Component -->
<?php if (!empty($show_programs_section) && !empty($extracurriculars)): ?>
<section class="py-16 sm:py-20 bg-white border-b border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-200 inline-block">
                    <?php echo !empty($hp_programs_subtitle) ? esc_html($hp_programs_subtitle) : 'Program Pembentukan Karakter'; ?>
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">
                    <?php echo !empty($hp_programs_title) ? esc_html($hp_programs_title) : 'Program Unggulan &amp; Kegiatan'; ?>
                </h2>
            </div>
            <a href="<?php echo base_url('ekstrakurikuler'); ?>" class="inline-flex items-center space-x-2 text-sm font-bold text-emerald-700 hover:text-emerald-800 transition">
                <span>Lihat Semua Program</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8">
            <?php foreach ($extracurriculars as $ex): ?>
                <a href="<?php echo base_url('ekstrakurikuler/' . $ex->slug); ?>" class="islamic-card bg-slate-50 rounded-3xl overflow-hidden border border-emerald-100/80 shadow-sm hover:shadow-xl transition flex flex-col group">
                    <div class="h-44 bg-emerald-900 relative overflow-hidden">
                        <?php if (!empty($ex->image)): ?>
                            <img src="<?php echo base_url($ex->image); ?>" alt="<?php echo esc_attr($ex->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex items-center justify-center text-amber-400">
                                <i data-lucide="book-open" class="w-10 h-10"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent"></div>
                    </div>
                    <div class="p-6 space-y-3 flex-grow flex flex-col justify-between">
                        <div class="space-y-2">
                            <h3 class="font-extrabold text-lg text-emerald-950 leading-snug group-hover:text-emerald-700 transition"><?php echo esc_html($ex->name); ?></h3>
                            <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed"><?php echo esc_html(strip_tags($ex->description)); ?></p>
                        </div>
                        <?php if (!empty($ex->schedule)): ?>
                            <div class="pt-3 border-t border-emerald-100/60 flex items-center text-xs text-emerald-700 font-semibold gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-500"></i>
                                <span><?php echo esc_html($ex->schedule); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

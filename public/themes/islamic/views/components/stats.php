<!-- Quick Stats Bar Component -->
<?php if (!empty($show_stats_section)): ?>
<section class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-emerald-950 py-14 text-white relative overflow-hidden border-y border-emerald-700/50">
    <div class="absolute inset-0 opacity-10 bg-islamic-pattern pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center divide-y sm:divide-y-0 sm:divide-x divide-emerald-700/60">
            <div class="flex flex-col items-center space-y-2 pt-6 sm:pt-0">
                <div class="w-12 h-12 rounded-2xl bg-emerald-800/80 flex items-center justify-center text-amber-400 mb-1 border border-emerald-700">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div class="text-4xl font-extrabold tracking-tight text-white"><?php echo (int) $stats['staff_count']; ?></div>
                <div class="text-sm font-semibold text-emerald-200 uppercase tracking-wider">Guru &amp; Tenaga Pendidik</div>
            </div>
            <div class="flex flex-col items-center space-y-2 pt-6 sm:pt-0">
                <div class="w-12 h-12 rounded-2xl bg-emerald-800/80 flex items-center justify-center text-amber-400 mb-1 border border-emerald-700">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div class="text-4xl font-extrabold tracking-tight text-white"><?php echo (int) $stats['extracurricular_count']; ?></div>
                <div class="text-sm font-semibold text-emerald-200 uppercase tracking-wider">Program &amp; Ekstrakurikuler</div>
            </div>
            <div class="flex flex-col items-center space-y-2 pt-6 sm:pt-0">
                <div class="w-12 h-12 rounded-2xl bg-emerald-800/80 flex items-center justify-center text-amber-400 mb-1 border border-emerald-700">
                    <i data-lucide="trophy" class="w-6 h-6"></i>
                </div>
                <div class="text-4xl font-extrabold tracking-tight text-white"><?php echo (int) $stats['achievement_count']; ?></div>
                <div class="text-sm font-semibold text-emerald-200 uppercase tracking-wider">Prestasi &amp; Penghargaan</div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

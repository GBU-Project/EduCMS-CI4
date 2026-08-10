<!-- Agenda Card Component -->
<a href="<?php echo base_url('agenda/' . $item->slug); ?>" class="islamic-card bg-white rounded-3xl border border-emerald-100 p-6 shadow-sm hover:shadow-xl transition flex items-start space-x-5 group">
    <div class="shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-b from-emerald-700 to-emerald-900 text-white flex flex-col items-center justify-center shadow-md shadow-emerald-900/20">
        <span class="text-xl font-extrabold leading-none text-amber-300"><?php echo date('d', strtotime($item->start_date)); ?></span>
        <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-100 mt-1"><?php echo date('M', strtotime($item->start_date)); ?></span>
    </div>
    <div class="min-w-0 space-y-2">
        <h3 class="font-extrabold text-base text-emerald-950 leading-snug group-hover:text-emerald-700 transition"><?php echo esc_html($item->title); ?></h3>
        <div class="flex flex-wrap gap-3 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i><?php echo date('H:i', strtotime($item->start_date)); ?> WIB</span>
            <?php if (!empty($item->location)): ?>
                <span class="inline-flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600"></i><?php echo esc_html($item->location); ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>

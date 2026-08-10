<?php $this->load->view('portal/partials/header'); ?>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">

    <!-- Breadcrumb -->
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">Hubungi Kami</span>
    </nav>

    <div class="mb-10 max-w-2xl">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Kontak</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Hubungi Kami</h1>
        <p class="text-slate-600 mt-3">
            Punya pertanyaan seputar sekolah, pendaftaran, atau kerja sama? Kirimkan pesan Anda melalui formulir di bawah ini, atau hubungi kami langsung melalui informasi kontak yang tersedia.
        </p>
    </div>

    <?php if (session()->getFlashdata('contact_success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <span><?php echo esc_html((string) session()->getFlashdata('contact_success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if (validation_errors()): ?>
        <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
            <?php echo validation_errors('<p class="flex items-start space-x-2"><i data-lucide="triangle-alert" class="w-4 h-4 mt-0.5 shrink-0"></i><span>', '</span></p>'); ?>
        </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-5 gap-10">
        <!-- Contact Form -->
        <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
            <?php echo form_open('kontak', array('class' => 'space-y-5')); ?>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="<?php echo set_value('name'); ?>" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="<?php echo set_value('email'); ?>" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Telepon (opsional)</label>
                    <input type="text" name="phone" value="<?php echo set_value('phone'); ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Subjek</label>
                    <input type="text" name="subject" value="<?php echo set_value('subject'); ?>" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pesan</label>
                    <textarea name="message" rows="5" required
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition"><?php echo set_value('message'); ?></textarea>
                </div>
                <button type="submit" class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium shadow-lg shadow-indigo-100 transition">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim Pesan</span>
                </button>
            <?php echo form_close(); ?>
        </div>

        <!-- School Info + Map -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
                <h3 class="font-bold text-slate-900">Informasi Sekolah</h3>
                <div class="flex items-start space-x-3">
                    <i data-lucide="map-pin" class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-xs text-slate-400 uppercase">Alamat</div>
                        <div class="text-sm font-medium text-slate-800"><?php echo esc_html(site_address('Belum diatur di Settings')); ?></div>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i data-lucide="phone" class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-xs text-slate-400 uppercase">Telepon</div>
                        <div class="text-sm font-medium text-slate-800"><?php echo esc_html(site_phone('Belum diatur')); ?></div>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i data-lucide="mail" class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5"></i>
                    <div>
                        <div class="text-xs text-slate-400 uppercase">Email</div>
                        <div class="text-sm font-medium text-slate-800"><?php echo esc_html(site_email('Belum diatur')); ?></div>
                    </div>
                </div>
                <?php $address_query = site_address(site_name('Indonesia')); ?>
                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($address_query); ?>" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center space-x-2 w-full bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
                    <i data-lucide="navigation" class="w-4 h-4"></i>
                    <span>Petunjuk Arah</span>
                </a>
            </div>

            <!-- Google Maps Embed -->
            <?php
                $custom_maps_embed = trim($this->site_settings['school']['maps_embed'] ?? '');
            ?>
            <div class="rounded-2xl overflow-hidden border border-slate-100 shadow-sm h-64">
                <?php if (!empty($custom_maps_embed)): ?>
                    <!-- RC5-002: admin-provided embed (Data Sekolah > Google Maps
                         Embed) takes priority over the address-derived map below.
                         Sanitized to the <iframe> tag only — never freeform HTML. -->
                    <?php echo strip_tags($custom_maps_embed, '<iframe>'); ?>
                <?php else: ?>
                    <!-- Fallback (unchanged from RC4): auto-generated from the
                         school address text, no API key required. Kept as the
                         default so nothing regresses for sites that haven't
                         filled in the new field yet. -->
                    <iframe
                        src="https://www.google.com/maps?q=<?php echo urlencode($address_query); ?>&output=embed"
                        width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php $this->load->view('portal/partials/footer'); ?>

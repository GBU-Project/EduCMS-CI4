<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold">Hubungi Kami</span>
        </nav>

        <!-- Header -->
        <div class="space-y-3 max-w-2xl">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Kontak Resmi
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">Hubungi Kami</h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Punya pertanyaan seputar sekolah, pendaftaran, atau kerja sama? Kirimkan pesan Anda melalui formulir di bawah ini, atau hubungi kami langsung melalui informasi kontak yang tersedia.
            </p>
        </div>

        <?php if ($this->session->flashdata('contact_success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-2xl flex items-center gap-3 text-xs font-bold shadow-sm">
                <i data-lucide="circle-check" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span><?php echo esc_html($this->session->flashdata('contact_success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if (validation_errors()): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl text-xs space-y-1">
                <?php echo validation_errors('<p class="flex items-start gap-2"><i data-lucide="triangle-alert" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i><span>', '</span></p>'); ?>
            </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-5 gap-10">
            <!-- Contact Form -->
            <div class="lg:col-span-3 bg-white rounded-3xl border border-emerald-100 shadow-md p-6 sm:p-10 space-y-6">
                <h3 class="font-extrabold text-emerald-950 text-lg border-b border-emerald-100/60 pb-3 flex items-center gap-2">
                    <i data-lucide="send" class="w-5 h-5 text-emerald-600"></i>
                    <span>Kirim Pesan</span>
                </h3>
                <?php echo form_open('kontak', array('class' => 'space-y-5')); ?>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="<?php echo set_value('name'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo set_value('email'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Nomor Telepon (opsional)</label>
                        <input type="text" name="phone" value="<?php echo set_value('phone'); ?>" class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Subjek</label>
                        <input type="text" name="subject" value="<?php echo set_value('subject'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pesan</label>
                        <textarea name="message" rows="5" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition"><?php echo set_value('message'); ?></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold px-8 py-3.5 rounded-2xl shadow-lg shadow-amber-900/20 transition transform hover:-translate-y-0.5">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirim Pesan</span>
                    </button>
                <?php echo form_close(); ?>
            </div>

            <!-- School Info + Map -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-emerald-100 shadow-md p-6 sm:p-8 space-y-5">
                    <h3 class="font-extrabold text-emerald-950 text-base border-b border-emerald-100/60 pb-3">Informasi Sekolah</h3>
                    <div class="flex items-start space-x-3">
                        <i data-lucide="map-pin" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                        <div>
                            <div class="text-[10px] text-emerald-800 uppercase font-bold tracking-wider">Alamat</div>
                            <div class="text-xs font-bold text-slate-800 mt-0.5"><?php echo esc_html(site_address('Belum diatur di Settings')); ?></div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i data-lucide="phone" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                        <div>
                            <div class="text-[10px] text-emerald-800 uppercase font-bold tracking-wider">Telepon</div>
                            <div class="text-xs font-bold text-slate-800 mt-0.5"><?php echo esc_html(site_phone('Belum diatur')); ?></div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i data-lucide="mail" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                        <div>
                            <div class="text-[10px] text-emerald-800 uppercase font-bold tracking-wider">Email</div>
                            <div class="text-xs font-bold text-slate-800 mt-0.5"><?php echo esc_html(site_email('Belum diatur')); ?></div>
                        </div>
                    </div>
                    <?php $address_query = site_address(site_name('Indonesia')); ?>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($address_query); ?>" target="_blank" rel="noopener" class="inline-flex items-center justify-center space-x-2 w-full bg-emerald-950 hover:bg-emerald-900 text-white px-4 py-3 rounded-2xl text-xs font-bold transition shadow-md">
                        <i data-lucide="navigation" class="w-4 h-4 text-amber-400"></i>
                        <span>Petunjuk Arah Google Maps</span>
                    </a>
                </div>

                <!-- Google Maps Embed -->
                <?php $custom_maps_embed = trim($this->site_settings['school']['maps_embed'] ?? ''); ?>
                <div class="rounded-3xl overflow-hidden border border-emerald-100 shadow-md h-64 bg-slate-100">
                    <?php if (!empty($custom_maps_embed)): ?>
                        <?php echo strip_tags($custom_maps_embed, '<iframe>'); ?>
                    <?php else: ?>
                        <iframe src="https://www.google.com/maps?q=<?php echo urlencode($address_query); ?>&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

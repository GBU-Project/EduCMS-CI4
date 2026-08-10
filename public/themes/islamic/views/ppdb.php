<?php $this->load->view('../../themes/islamic/views/partials/header'); ?>

<main class="flex-grow w-full py-12 sm:py-16 bg-slate-50 border-b border-emerald-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-2xl border border-emerald-100/80 shadow-sm w-fit">
            <a href="<?php echo base_url(); ?>" class="hover:text-emerald-700 transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Beranda</span>
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-emerald-500"></i>
            <span class="text-emerald-950 font-bold">PPDB Online</span>
        </nav>

        <!-- Header -->
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 bg-emerald-100/60 px-3.5 py-1 rounded-full border border-emerald-200">
                Penerimaan Peserta Didik Baru
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-emerald-950">Formulir Pendaftaran PPDB Online</h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Lengkapi data di bawah ini dengan benar. Setelah pendaftaran berhasil, Anda akan menerima Nomor Pendaftaran yang wajib disimpan untuk keperluan verifikasi berkas oleh pihak sekolah.
            </p>
        </div>

        <?php if (validation_errors()): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl text-xs space-y-1">
                <?php echo validation_errors('<p class="flex items-start gap-2"><i data-lucide="triangle-alert" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i><span>', '</span></p>'); ?>
            </div>
        <?php endif; ?>

        <?php echo form_open_multipart('ppdb', array('class' => 'space-y-8')); ?>

            <!-- Data Pribadi -->
            <div class="bg-white rounded-3xl border border-emerald-100 shadow-md p-6 sm:p-10 space-y-6">
                <h3 class="font-extrabold text-emerald-950 flex items-center gap-2 text-lg border-b border-emerald-100/60 pb-3">
                    <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                    <span>Data Pribadi Calon Siswa</span>
                </h3>
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="full_name" value="<?php echo set_value('full_name'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">NISN</label>
                        <input type="text" name="nisn" value="<?php echo set_value('nisn'); ?>" class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">NIK</label>
                        <input type="text" name="nik" value="<?php echo set_value('nik'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Kelamin</label>
                        <select name="gender" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                            <option value="">- Pilih -</option>
                            <option value="L" <?php echo set_select('gender', 'L'); ?>>Laki-laki</option>
                            <option value="P" <?php echo set_select('gender', 'P'); ?>>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Tempat Lahir</label>
                        <input type="text" name="place_of_birth" value="<?php echo set_value('place_of_birth'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="date_of_birth" value="<?php echo set_value('date_of_birth'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" rows="3" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition"><?php echo set_value('address'); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Kontak & Asal Sekolah -->
            <div class="bg-white rounded-3xl border border-emerald-100 shadow-md p-6 sm:p-10 space-y-6">
                <h3 class="font-extrabold text-emerald-950 flex items-center gap-2 text-lg border-b border-emerald-100/60 pb-3">
                    <i data-lucide="contact" class="w-5 h-5 text-emerald-600"></i>
                    <span>Kontak &amp; Asal Sekolah</span>
                </h3>
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" value="<?php echo set_value('email'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">No. HP Calon Siswa</label>
                        <input type="text" name="phone" value="<?php echo set_value('phone'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Nama Orang Tua/Wali</label>
                        <input type="text" name="parent_name" value="<?php echo set_value('parent_name'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">No. HP Orang Tua/Wali</label>
                        <input type="text" name="parent_phone" value="<?php echo set_value('parent_phone'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Asal Sekolah</label>
                        <input type="text" name="previous_school" value="<?php echo set_value('previous_school'); ?>" required class="w-full rounded-xl border border-emerald-200 px-4 py-3 text-sm focus:border-emerald-600 focus:ring focus:ring-emerald-100 transition">
                    </div>
                </div>
            </div>

            <!-- Upload Berkas -->
            <div class="bg-white rounded-3xl border border-emerald-100 shadow-md p-6 sm:p-10 space-y-6">
                <h3 class="font-extrabold text-emerald-950 flex items-center gap-2 text-lg border-b border-emerald-100/60 pb-3">
                    <i data-lucide="upload" class="w-5 h-5 text-emerald-600"></i>
                    <span>Unggah Berkas Pendukung</span>
                </h3>
                <p class="text-xs text-slate-500 font-medium -mt-3">Format JPG/PNG/PDF. Berkas dapat menyusul jika belum siap saat ini.</p>
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pas Foto</label>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs rounded-xl border border-emerald-200 p-2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Kartu Keluarga (KK)</label>
                        <input type="file" name="kk" accept="image/*,application/pdf" class="w-full text-xs rounded-xl border border-emerald-200 p-2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Akta Kelahiran</label>
                        <input type="file" name="akta" accept="image/*,application/pdf" class="w-full text-xs rounded-xl border border-emerald-200 p-2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Rapor Terakhir</label>
                        <input type="file" name="raport" accept="image/*,application/pdf" class="w-full text-xs rounded-xl border border-emerald-200 p-2 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-bold">
                    </div>
                </div>
            </div>

            <button type="submit" class="inline-flex items-center justify-center space-x-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold px-9 py-4 rounded-2xl shadow-xl shadow-amber-900/20 transition transform hover:-translate-y-0.5 w-full sm:w-auto">
                <i data-lucide="send" class="w-5 h-5"></i>
                <span>Kirim Pendaftaran PPDB</span>
            </button>
        <?php echo form_close(); ?>

    </div>
</main>

<?php $this->load->view('../../themes/islamic/views/partials/footer'); ?>

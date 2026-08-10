<?php echo view('portal/partials/header'); ?>

<main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">

    <!-- Breadcrumb -->
    <nav class="text-sm text-slate-500 mb-6 flex items-center space-x-2">
        <a href="<?php echo base_url(); ?>" class="hover:text-indigo-600 transition">Beranda</a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
        <span class="text-slate-700 font-medium">PPDB Online</span>
    </nav>

    <div class="mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-indigo-600">Penerimaan Peserta Didik Baru</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-2">Formulir Pendaftaran PPDB Online</h1>
        <p class="text-slate-600 mt-3">
            Lengkapi data di bawah ini dengan benar. Setelah pendaftaran berhasil, Anda akan menerima Nomor Pendaftaran yang wajib disimpan untuk keperluan verifikasi berkas oleh pihak sekolah.
        </p>
    </div>

    <?php if (validation_errors()): ?>
        <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
            <?php echo validation_errors('<p class="flex items-start space-x-2"><i data-lucide="triangle-alert" class="w-4 h-4 mt-0.5 shrink-0"></i><span>', '</span></p>'); ?>
        </div>
    <?php endif; ?>

    <?php echo form_open_multipart('ppdb', array('class' => 'space-y-8')); ?>

        <!-- Data Pribadi -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 space-y-5">
            <h3 class="font-bold text-slate-900 flex items-center space-x-2"><i data-lucide="user" class="w-5 h-5 text-indigo-500"></i><span>Data Pribadi Calon Siswa</span></h3>
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="full_name" value="<?php echo set_value('full_name'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NISN</label>
                    <input type="text" name="nisn" value="<?php echo set_value('nisn'); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NIK</label>
                    <input type="text" name="nik" value="<?php echo set_value('nik'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Kelamin</label>
                    <select name="gender" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                        <option value="">- Pilih -</option>
                        <option value="L" <?php echo set_select('gender', 'L'); ?>>Laki-laki</option>
                        <option value="P" <?php echo set_select('gender', 'P'); ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tempat Lahir</label>
                    <input type="text" name="place_of_birth" value="<?php echo set_value('place_of_birth'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" value="<?php echo set_value('date_of_birth'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Lengkap</label>
                <textarea name="address" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition"><?php echo set_value('address'); ?></textarea>
            </div>
        </div>

        <!-- Kontak & Asal Sekolah -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 space-y-5">
            <h3 class="font-bold text-slate-900 flex items-center space-x-2"><i data-lucide="contact" class="w-5 h-5 text-indigo-500"></i><span>Kontak &amp; Asal Sekolah</span></h3>
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="<?php echo set_value('email'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">No. HP Calon Siswa</label>
                    <input type="text" name="phone" value="<?php echo set_value('phone'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Orang Tua/Wali</label>
                    <input type="text" name="parent_name" value="<?php echo set_value('parent_name'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">No. HP Orang Tua/Wali</label>
                    <input type="text" name="parent_phone" value="<?php echo set_value('parent_phone'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Asal Sekolah</label>
                    <input type="text" name="previous_school" value="<?php echo set_value('previous_school'); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-100 transition">
                </div>
            </div>
        </div>

        <!-- Upload Berkas -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8 space-y-5">
            <h3 class="font-bold text-slate-900 flex items-center space-x-2"><i data-lucide="upload" class="w-5 h-5 text-indigo-500"></i><span>Unggah Berkas Pendukung</span></h3>
            <p class="text-xs text-slate-500 -mt-3">Format JPG/PNG/PDF. Berkas dapat menyusul jika belum siap saat ini.</p>
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pas Foto</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kartu Keluarga (KK)</label>
                    <input type="file" name="kk" accept="image/*,application/pdf" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Akta Kelahiran</label>
                    <input type="file" name="akta" accept="image/*,application/pdf" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Rapor Terakhir</label>
                    <input type="file" name="raport" accept="image/*,application/pdf" class="w-full text-sm rounded-xl border border-slate-200 px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-600 file:text-sm">
                </div>
            </div>
        </div>

        <button type="submit" class="inline-flex items-center justify-center space-x-2 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3.5 rounded-xl font-medium shadow-lg shadow-indigo-100 transition">
            <i data-lucide="send" class="w-5 h-5"></i>
            <span>Daftar Sekarang</span>
        </button>
    <?php echo form_close(); ?>
</main>

<?php echo view('portal/partials/footer'); ?>

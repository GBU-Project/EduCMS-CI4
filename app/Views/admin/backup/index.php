<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-md-8">
        <?php echo educard_start('Backup Database', 'database'); ?>
            <p class="text-secondary">Buat cadangan (backup) seluruh database EduCMS dalam format SQL terkompresi (Gzip). Simpan file ini di tempat yang aman sebagai antisipasi pemulihan data.</p>
            <a href="<?php echo base_url('admin/backup/run'); ?>" class="btn btn-indigo"><?php echo render_icon('download', 'mr-1'); ?> Unduh Backup Sekarang</a>
            <div class="alert alert-warning mt-4 mb-0">
                <?php echo render_icon('triangle-exclamation', 'mr-1'); ?>
                Proses ini bisa memakan waktu beberapa saat tergantung ukuran database. Jangan tutup halaman ini selama proses berlangsung.
            </div>
        <?php echo educard_end(); ?>

        <div class="mt-4">
        <?php echo educard_start('Restore Database', 'upload'); ?>
            <?php if (!$enable_web_db_restore): ?>
                <div class="alert alert-secondary mb-0">
                    <?php echo render_icon('circle-info', 'mr-1'); ?>
                    <strong>Restore via web dinonaktifkan.</strong> Fitur ini mengeksekusi seluruh isi file .sql langsung ke
                    database, sehingga sesi admin yang disusupi (phishing, XSS, credential stuffing) bisa mengambil alih
                    seluruh database. Untuk keamanan, gunakan restore via CLI (<code>scripts/restore_db.php</code>) yang
                    dijalankan lewat SSH oleh pihak dengan akses server, bukan lewat browser. Jika benar-benar diperlukan,
                    fitur ini dapat diaktifkan sementara oleh administrator server melalui environment variable
                    <code>ENABLE_WEB_DB_RESTORE</code>.
                </div>
            <?php else: ?>
            <div class="alert alert-danger">
                <?php echo render_icon('triangle-exclamation', 'mr-1'); ?>
                <strong>Tindakan berisiko tinggi.</strong> Restore akan menjalankan seluruh isi file .sql ke database yang sedang aktif dan dapat menimpa data yang ada. Pastikan Anda sudah mengunduh Backup Database terbaru sebelum melanjutkan. Hanya Super Admin yang dapat melakukan ini.
            </div>
            <?php echo form_open_multipart('admin/backup/restore', array('id' => 'restore-db-form')); ?>
                <div class="form-group mb-3">
                    <label class="form-label font-weight-semibold">File Backup (.sql)</label>
                    <input type="file" name="sql_file" id="sql_file" class="form-control-file" accept=".sql" required>
                    <span class="text-muted text-xs d-block mt-1">Hanya menerima file berekstensi .sql (hasil ekspor Backup Database atau phpMyAdmin/mysqldump).</span>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label font-weight-semibold">Konfirmasi Password Anda</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="current-password" required>
                    <span class="text-muted text-xs d-block mt-1">Masukkan kembali password login Anda untuk membuktikan ini benar-benar Anda, bukan sesi yang disusupi.</span>
                </div>
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" name="confirm_restore" value="1" id="confirm_restore" class="custom-control-input" required>
                    <label class="custom-control-label text-secondary small" for="confirm_restore">Saya memahami bahwa proses ini akan menimpa data pada database yang sedang aktif, dan saya sudah memiliki cadangan terbaru.</label>
                </div>
                <button type="submit" id="restore-db-submit" class="btn btn-danger"><?php echo render_icon('upload', 'mr-1'); ?> Restore Database</button>
                <span id="restore-db-progress" class="text-muted small ml-2" style="display:none;"><?php echo render_icon('spinner', 'fa-spin mr-1'); ?>Memproses restore, mohon tunggu dan jangan tutup halaman ini...</span>
            <?php echo form_close(); ?>
            <?php endif; ?>
        <?php echo educard_end(); ?>
        </div>
    </div></div>
</div></section>
<script>
// RC5-008: confirm before submit (server re-validates confirm_restore
// independently — see Backup::restore()), then show a progress message
// while the synchronous upload+restore request is in flight.
$(document).ready(function () {
    var confirmed = false;
    $('#restore-db-form').on('submit', function (e) {
        if (confirmed) {
            $('#restore-db-submit').prop('disabled', true);
            $('#restore-db-progress').show();
            return true;
        }
        e.preventDefault();
        var $form = $(this);
        EduAlert.confirm('Konfirmasi Restore Database', 'Proses Restore akan menggantikan data database saat ini. Pastikan Anda telah membuat backup terbaru. Lanjutkan proses restore?', function () {
            confirmed = true;
            $form.trigger('submit');
        });
        return false;
    });
});
</script>

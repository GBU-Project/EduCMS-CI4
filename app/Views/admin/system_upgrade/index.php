<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>

    <div class="row">
        <div class="col-md-8">
            <?php echo educard_start('Status Database', 'database'); ?>

                <?php if ($status['up_to_date']): ?>
                    <div class="d-flex align-items-center p-4" style="background:#f0fdf4;border-radius:12px;">
                        <div class="mr-3" style="width:48px;height:48px;border-radius:50%;background:#22c55e;color:#fff;display:flex;align-items:center;justify-content:center;">
                            <?php echo render_icon('check', ''); ?>
                        </div>
                        <div>
                            <h5 class="mb-1 font-weight-bold">Database Sudah Terbaru</h5>
                            <p class="mb-0 text-secondary">Semua <?php echo (int) $status['total_migrations']; ?> pembaruan struktur database sudah diterapkan. Tidak ada tindakan yang diperlukan.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center p-4 mb-3" style="background:#fffbeb;border-radius:12px;">
                        <div class="mr-3" style="width:48px;height:48px;border-radius:50%;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;">
                            <?php echo render_icon('triangle-exclamation', ''); ?>
                        </div>
                        <div>
                            <h5 class="mb-1 font-weight-bold">Ada <?php echo (int) $status['pending_count']; ?> Pembaruan Menunggu</h5>
                            <p class="mb-0 text-secondary">Klik tombol di bawah untuk menerapkan pembaruan struktur database secara otomatis. Proses ini aman dijalankan dan tidak menghapus data yang sudah ada.</p>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-secondary text-uppercase" style="font-size:0.75rem;letter-spacing:0.5px;">Menunggu Diterapkan</h6>
                    <ul class="list-group mb-4">
                        <?php foreach ($status['pending_migrations'] as $m): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><code>#<?php echo esc_html($m['version']); ?></code> &nbsp; <?php echo esc_html(str_replace('_', ' ', ucfirst($m['name']))); ?></span>
                                <span class="badge badge-warning text-dark">Belum Diterapkan</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <form action="<?php echo base_url('admin/system-upgrade/run'); ?>" method="post" id="upgrade-form">
                        <?php echo $this->security->get_csrf_token_name() ? '<input type="hidden" name="' . $this->security->get_csrf_token_name() . '" value="' . $this->security->get_csrf_hash() . '">' : ''; ?>
                        <button type="submit" class="btn btn-indigo btn-lg" id="upgrade-btn">
                            <?php echo render_icon('arrows-rotate', 'mr-1'); ?> Upgrade Database Sekarang
                        </button>
                    </form>
                <?php endif; ?>

            <?php echo educard_end(); ?>

            <?php // RC4 Final Hotfix, Part A: Configuration Integrity Check ?>
            <?php if (!empty($homepage_integrity) && !$homepage_integrity['valid']): ?>
            <?php echo educard_start('Configuration Integrity Check', 'triangle-exclamation'); ?>
                <div class="p-3 mb-3" style="background:#fef2f2;border-radius:12px;border-left:4px solid #ef4444;">
                    <h6 class="mb-1 font-weight-bold text-danger"><?php echo render_icon('circle-xmark', 'mr-1'); ?> Konfigurasi Homepage Tidak Valid</h6>
                    <p class="mb-0 text-secondary small">Ditemukan <?php echo count($homepage_integrity['issues']); ?> masalah pada grup setelan <code>homepage</code>. Tab Homepage di Setelan Website dan/atau tampilan depan situs mungkin tidak berfungsi dengan benar.</p>
                </div>
                <ul class="list-group mb-3">
                    <?php foreach ($homepage_integrity['issues'] as $issue): ?>
                        <li class="list-group-item small text-danger"><?php echo esc_html($issue); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php echo form_open('admin/system-upgrade/repair_homepage', array('id' => 'repair-homepage-form', 'onsubmit' => "return confirm('Ini akan MENGHAPUS seluruh konfigurasi Homepage saat ini dan menggantinya dengan nilai default. Konfigurasi lain (Umum, Data Sekolah, dll) TIDAK terpengaruh. Lanjutkan?');")); ?>
                    <button type="submit" class="btn btn-danger" id="repair-homepage-btn">
                        <?php echo render_icon('wrench', 'mr-1'); ?> Repair Homepage Configuration
                    </button>
                    <p class="text-muted small mt-2 mb-0">Tindakan ini menghapus seluruh baris <code>homepage.*</code> lalu menjalankan ulang seeding default (migration 012). Setelan grup lain (Umum, Data Sekolah, SEO, dll) tidak disentuh.</p>
                <?php echo form_close(); ?>
            <?php echo educard_end(); ?>
            <?php elseif (!empty($homepage_integrity) && $homepage_integrity['row_count'] > 0): ?>
            <?php echo educard_start('Configuration Integrity Check', 'circle-check'); ?>
                <p class="text-success small mb-0"><?php echo render_icon('circle-check', 'mr-1'); ?> Konfigurasi grup <code>homepage</code> (<?php echo (int) $homepage_integrity['row_count']; ?> key) valid.</p>
            <?php echo educard_end(); ?>
            <?php endif; ?>

            <?php echo educard_start('Riwayat Migrasi', 'clock-rotate-left'); ?>
                <?php if (empty($status['applied_migrations'])): ?>
                    <p class="text-secondary mb-0">Belum ada riwayat migrasi yang tercatat.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Versi</th><th>Nama Migrasi</th><th>Diterapkan Pada</th></tr></thead>
                        <tbody>
                        <?php foreach ($status['applied_migrations'] as $version => $row): ?>
                            <tr>
                                <td><code>#<?php echo esc_html($version); ?></code></td>
                                <td><?php echo esc_html(str_replace('_', ' ', ucfirst($row->migration_name))); ?></td>
                                <td><?php echo esc_html($row->applied_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            <?php echo educard_end(); ?>
        </div>

        <div class="col-md-4">
            <?php echo educard_start('Tentang Fitur Ini', 'circle-info'); ?>
                <p class="text-secondary small mb-2">Database Upgrade Manager menjalankan pembaruan struktur database secara otomatis — tidak perlu phpMyAdmin, tidak perlu mengimpor file SQL manual.</p>
                <p class="text-secondary small mb-2">Setiap pembaruan aman dijalankan berulang kali; pembaruan yang sudah diterapkan akan otomatis dilewati.</p>
                <p class="text-secondary small mb-0">Data yang sudah ada tidak akan hilang saat proses berjalan.</p>
            <?php echo educard_end(); ?>
        </div>
    </div>
</div></section>
<script>
$(document).ready(function () {
    $('#upgrade-form').on('submit', function () {
        $('#upgrade-btn').prop('disabled', true).html('<?php echo render_icon("spinner", "fa-spin mr-1"); ?> Menjalankan Upgrade...');
    });
    $('#repair-homepage-form').on('submit', function () {
        // Fires only after the confirm() dialog in the inline onsubmit
        // handler returns true — a cancelled confirm never reaches here.
        $('#repair-homepage-btn').prop('disabled', true).html('<?php echo render_icon("spinner", "fa-spin mr-1"); ?> Memperbaiki Konfigurasi...');
    });
});
</script>

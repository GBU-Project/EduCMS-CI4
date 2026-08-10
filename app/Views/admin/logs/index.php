<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php echo educard_start('Log Aktivitas Sistem', 'clock-rotate-left'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div></div>
            <form action="<?php echo base_url('admin/logs/clear'); ?>" method="post" onsubmit="return confirm('Yakin ingin membersihkan seluruh log aktivitas? Tindakan ini tidak dapat dibatalkan.');">
                <button type="submit" class="btn btn-sm btn-outline-danger"><?php echo render_icon('trash-can', 'mr-1'); ?> Bersihkan Log</button>
            </form>
        </div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Aktivitas', 'Log aktivitas pengguna akan muncul di sini.', 'clock-rotate-left'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="logs-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th>Waktu</th><th>Pengguna</th><th>Modul</th><th>Aksi</th><th>IP Address</th><th>Browser / OS</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo esc_html($item->created_at ?? '-'); ?></td>
                        <td><?php echo esc_html($item->user_name ?? 'System'); ?></td>
                        <td><span class="badge badge-info"><?php echo esc_html($item->module ?? '-'); ?></span></td>
                        <td><?php echo esc_html($item->action ?? '-'); ?></td>
                        <td><code><?php echo esc_html($item->ip_address ?? '-'); ?></code></td>
                        <td class="text-secondary small"><?php echo esc_html($item->browser ?? '-'); ?> / <?php echo esc_html($item->operating_system ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#logs-table').length){ EduTable.init('#logs-table'); } });</script>

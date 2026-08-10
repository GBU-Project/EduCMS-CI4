<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php echo educard_start('Pesan Kontak', 'envelope'); ?>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Pesan', 'Pesan dari formulir kontak website akan muncul di sini.', 'envelope'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="messages-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th>Status</th><th>Pengirim</th><th>Subjek</th><th>Tanggal</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr class="<?php echo empty($item->is_read) ? 'font-weight-bold' : ''; ?>">
                        <td><?php echo $no++; ?></td>
                        <td><span class="badge badge-<?php echo empty($item->is_read) ? 'primary' : 'secondary'; ?>"><?php echo empty($item->is_read) ? 'Baru' : 'Dibaca'; ?></span></td>
                        <td><?php echo esc_html($item->name ?? '-'); ?><br><span class="text-secondary small font-weight-normal"><?php echo esc_html($item->email ?? ''); ?></span></td>
                        <td><?php echo esc_html($item->subject ?? '-'); ?></td>
                        <td><?php echo esc_html($item->created_at ?? '-'); ?></td>
                        <td class="text-right">
                            <a href="<?php echo base_url('admin/messages/view/' . $item->id); ?>" class="btn btn-sm btn-outline-indigo"><?php echo render_icon('eye'); ?></a>
                            <a href="<?php echo base_url('admin/messages/delete/' . $item->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pesan ini?');"><?php echo render_icon('trash-can'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#messages-table').length){ EduTable.init('#messages-table'); } });</script>

<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php echo educard_start('Daftar Peran (Roles)', 'user-shield'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <a href="<?php echo base_url('admin/roles/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Role</a>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Role', 'Tambahkan peran pengguna baru.', 'user-shield', 'Tambah Role', 'admin/roles/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="roles-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th>Nama Role</th><th>Deskripsi</th><th>Jumlah Permission</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td class="font-weight-medium"><?php echo esc_html($item->name ?? '-'); ?></td>
                        <td><?php echo esc_html($item->description ?? '-'); ?></td>
                        <td><span class="badge badge-info"><?php echo (int)($item->permission_count ?? 0); ?> permission</span></td>
                        <td class="text-right"><?php echo render_action_buttons($item->id, '', 'admin/roles', FALSE); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#roles-table').length){ EduTable.init('#roles-table'); } });</script>

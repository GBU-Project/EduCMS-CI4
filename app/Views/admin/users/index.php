<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php echo educard_start('Daftar Pengguna', 'users'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <a href="<?php echo base_url('admin/users/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Pengguna</a>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Pengguna', 'Tambahkan pengguna admin baru.', 'users', 'Tambah Pengguna', 'admin/users/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="users-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th>Nama Lengkap</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td class="font-weight-medium"><?php echo esc_html($item->full_name ?? '-'); ?></td>
                        <td><?php echo esc_html($item->username ?? '-'); ?></td>
                        <td><?php echo esc_html($item->email ?? '-'); ?></td>
                        <td><?php echo esc_html($item->role_names ?? '-'); ?></td>
                        <td><span class="badge badge-<?php echo (isset($item->status) && $item->status === 'active') ? 'success' : 'secondary'; ?>"><?php echo (isset($item->status) && $item->status === 'active') ? 'Aktif' : 'Nonaktif'; ?></span></td>
                        <td class="text-right"><?php echo render_action_buttons($item->id, '', 'admin/users', FALSE); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#users-table').length){ EduTable.init('#users-table'); } });</script>

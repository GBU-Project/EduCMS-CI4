<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php echo educard_start('Daftar Pendaftar PPDB', 'user-graduate'); ?>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Pendaftar PPDB', 'Pendaftar calon siswa baru dari formulir online akan muncul di sini. Pastikan fitur PPDB sudah diaktifkan pada Setelan Website.', 'user-graduate', 'Setelan Website', 'admin/settings'); ?>
        <?php else: ?>
        <?php $status_labels = array('pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'accepted' => 'Diterima', 'rejected' => 'Ditolak');
              $status_badges = array('pending' => 'secondary', 'verified' => 'info', 'accepted' => 'success', 'rejected' => 'danger'); ?>
        <div class="table-responsive">
            <table id="ppdb-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th>No. Registrasi</th><th>Nama Lengkap</th><th>Asal Sekolah</th><th>No. HP</th><th>Status</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): $st = $item->status ?? 'pending'; ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><code><?php echo esc_html($item->registration_number ?? '-'); ?></code></td>
                        <td class="font-weight-medium"><?php echo esc_html($item->full_name ?? '-'); ?></td>
                        <td><?php echo esc_html($item->previous_school ?? '-'); ?></td>
                        <td><?php echo esc_html($item->phone ?? '-'); ?></td>
                        <td><span class="badge badge-<?php echo $status_badges[$st] ?? 'secondary'; ?>"><?php echo esc_html($status_labels[$st] ?? ucfirst($st)); ?></span></td>
                        <td class="text-right">
                            <a href="<?php echo base_url('admin/ppdb/view/' . $item->id); ?>" class="btn btn-sm btn-outline-indigo"><?php echo render_icon('eye'); ?></a>
                            <a href="<?php echo base_url('admin/ppdb/delete/' . $item->id); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data pendaftar ini?');"><?php echo render_icon('trash-can'); ?></a>
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
<script>$(document).ready(function(){ if($('#ppdb-table').length){ EduTable.init('#ppdb-table'); } });</script>

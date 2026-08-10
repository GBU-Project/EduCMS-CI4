<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php $show_trash = isset($show_trash) && $show_trash; echo educard_start($show_trash ? 'Sampah — URL Redirects' : 'Daftar URL Redirects', 'route'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <?php if ($show_trash): ?><a href="<?php echo base_url('admin/redirects'); ?>" class="btn btn-sm btn-outline-secondary"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali</a>
            <?php else: ?><a href="<?php echo base_url('admin/redirects/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Redirect</a>
                <a href="<?php echo base_url('admin/redirects?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2"><?php echo render_icon('trash-can', 'mr-1'); ?> Sampah</a>
            <?php endif; ?>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Redirect', $show_trash ? 'Tempat sampah kosong.' : 'Tambahkan aturan redirect URL 301/302.', 'route', $show_trash ? '' : 'Tambah Redirect', $show_trash ? '' : 'admin/redirects/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="redirects-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th>URL Asal</th><th>URL Tujuan</th><th>Kode</th><th>Hit</th><th>Terakhir Diakses</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td class="font-weight-medium"><code><?php echo esc_html($item->source_url ?? '-'); ?></code></td>
                        <td><code><?php echo esc_html($item->target_url ?? '-'); ?></code></td>
                        <td><span class="badge badge-info"><?php echo esc_html($item->status_code ?? '301'); ?></span></td>
                        <td><?php echo (int)($item->hit_count ?? 0); ?></td>
                        <td><?php echo esc_html($item->last_hit ?? 'Belum pernah'); ?></td>
                        <td class="text-right"><?php echo render_action_buttons($item->id, '', 'admin/redirects', $show_trash); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#redirects-table').length){ EduTable.init('#redirects-table'); } });</script>

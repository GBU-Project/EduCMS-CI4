<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php $show_trash = isset($show_trash) && $show_trash; echo educard_start($show_trash ? 'Sampah — Mitra' : 'Mitra Sekolah', 'handshake'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <?php if ($show_trash): ?><a href="<?php echo base_url('admin/partners'); ?>" class="btn btn-sm btn-outline-secondary"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali</a>
            <?php else: ?><a href="<?php echo base_url('admin/partners/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Mitra</a>
                <a href="<?php echo base_url('admin/partners?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2"><?php echo render_icon('trash-can', 'mr-1'); ?> Sampah</a>
            <?php endif; ?>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Mitra', $show_trash ? 'Tempat sampah kosong.' : 'Tambahkan logo mitra/sponsor sekolah.', 'handshake', $show_trash ? '' : 'Tambah Mitra', $show_trash ? '' : 'admin/partners/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="partners-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th style="width:10%">Logo</th><th>Nama Mitra</th><th>Tautan</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <?php if (!empty($item->logo)): ?>
                                <img src="<?php echo base_url($item->logo); ?>" alt="" style="width:60px;height:40px;object-fit:contain;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width:60px;height:40px;border-radius:6px;"><?php echo render_icon('image'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="font-weight-medium"><?php echo esc_html($item->name ?? '-'); ?></td>
                        <td><?php echo !empty($item->link) ? '<a href="' . esc_attr($item->link) . '" target="_blank" rel="noopener">' . esc_html($item->link) . '</a>' : '<span class="text-muted">-</span>'; ?></td>
                        <td class="text-right"><?php echo render_action_buttons($item->id, '', 'admin/partners', $show_trash); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#partners-table').length){ EduTable.init('#partners-table'); } });</script>

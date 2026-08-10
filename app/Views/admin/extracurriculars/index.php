<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php $show_trash = isset($show_trash) && $show_trash; echo educard_start($show_trash ? 'Sampah — Ekstrakurikuler' : 'Daftar Ekstrakurikuler', 'volleyball'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <?php if ($show_trash): ?><a href="<?php echo base_url('admin/extracurriculars'); ?>" class="btn btn-sm btn-outline-secondary"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali</a>
            <?php else: ?><a href="<?php echo base_url('admin/extracurriculars/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Ekstrakurikuler</a>
                <a href="<?php echo base_url('admin/extracurriculars?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2"><?php echo render_icon('trash-can', 'mr-1'); ?> Sampah</a>
            <?php endif; ?>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Ekstrakurikuler', $show_trash ? 'Tempat sampah kosong.' : 'Tambahkan kegiatan ekstrakurikuler sekolah.', 'volleyball', $show_trash ? '' : 'Tambah Ekstrakurikuler', $show_trash ? '' : 'admin/extracurriculars/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="extracurriculars-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th>Foto</th><th>Nama</th><th>Pembina</th><th>Jadwal</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php if (!empty($item->image)): ?><img src="<?php echo base_url($item->image); ?>" class="img-circle" style="width:36px;height:36px;object-fit:cover;"><?php else: ?><span class="bg-secondary d-inline-flex align-items-center justify-content-center text-white img-circle" style="width:36px;height:36px;"><?php echo render_icon('volleyball'); ?></span><?php endif; ?></td>
                        <td class="font-weight-medium"><?php echo esc_html($item->name ?? '-'); ?></td>
                        <td><?php echo esc_html($item->coach ?? '-'); ?></td>
                        <td><?php echo esc_html($item->schedule ?? '-'); ?></td>
                        <td class="text-right"><?php echo render_action_buttons($item->id, '', 'admin/extracurriculars', $show_trash); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#extracurriculars-table').length){ EduTable.init('#extracurriculars-table'); } });</script>

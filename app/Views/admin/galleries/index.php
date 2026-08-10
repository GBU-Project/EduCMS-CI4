<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php $show_trash = isset($show_trash) && $show_trash; echo educard_start($show_trash ? 'Sampah — Galeri' : 'Galeri Sekolah', 'images'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <?php if ($show_trash): ?><a href="<?php echo base_url('admin/galleries'); ?>" class="btn btn-sm btn-outline-secondary"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali</a>
            <?php else: ?><a href="<?php echo base_url('admin/galleries/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Album</a>
                <a href="<?php echo base_url('admin/galleries?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2"><?php echo render_icon('trash-can', 'mr-1'); ?> Sampah</a>
            <?php endif; ?>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Album Galeri', $show_trash ? 'Tempat sampah kosong.' : 'Buat album dokumentasi foto atau video kegiatan sekolah.', 'images', $show_trash ? '' : 'Tambah Album', $show_trash ? '' : 'admin/galleries/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="galleries-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th style="width:8%">Cover</th><th>Judul Album</th><th>Tipe</th><th style="width:20%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <?php if (!empty($item->cover_image)): ?>
                                <img src="<?php echo base_url($item->cover_image); ?>" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:6px;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="width:60px;height:40px;border-radius:6px;"><?php echo render_icon($item->type === 'video' ? 'video' : 'image'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="font-weight-medium"><?php echo esc_html($item->title ?? '-'); ?></td>
                        <td><span class="badge badge-info"><?php echo $item->type === 'video' ? 'Galeri Video' : 'Galeri Foto'; ?></span></td>
                        <td class="text-right">
                            <?php if (!$show_trash): ?>
                                <a href="<?php echo base_url('admin/galleries/items/' . $item->id); ?>" class="btn btn-xs btn-outline-indigo mr-1" title="Kelola Item"><?php echo render_icon('list'); ?> Item</a>
                            <?php endif; ?>
                            <?php echo render_action_buttons($item->id, $item->slug ?? '', 'admin/galleries', $show_trash, !empty($item->slug) ? ($item->type === 'video' ? 'galeri-video/' . $item->slug : 'galeri-foto/' . $item->slug) : ''); ?>
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
<script>$(document).ready(function(){ if($('#galleries-table').length){ EduTable.init('#galleries-table'); } });</script>

<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row"><div class="col-12">
        <?php $show_trash = isset($show_trash) && $show_trash; echo educard_start($show_trash ? 'Sampah — Testimoni' : 'Testimoni', 'quote-left'); ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div>
            <?php if ($show_trash): ?><a href="<?php echo base_url('admin/testimonials'); ?>" class="btn btn-sm btn-outline-secondary"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali</a>
            <?php else: ?><a href="<?php echo base_url('admin/testimonials/create'); ?>" class="btn btn-sm btn-indigo"><?php echo render_icon('plus', 'mr-1'); ?> Tambah Testimoni</a>
                <a href="<?php echo base_url('admin/testimonials?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2"><?php echo render_icon('trash-can', 'mr-1'); ?> Sampah</a>
            <?php endif; ?>
        </div></div>
        <?php if (empty($list)): ?>
            <?php echo eduempty_state('Belum Ada Testimoni', $show_trash ? 'Tempat sampah kosong.' : 'Tambahkan testimoni dari orang tua, alumni, atau siswa.', 'quote-left', $show_trash ? '' : 'Tambah Testimoni', $show_trash ? '' : 'admin/testimonials/create'); ?>
        <?php else: ?>
        <div class="table-responsive">
            <table id="testimonials-table" class="table table-hover table-striped">
                <thead><tr><th style="width:5%">No</th><th style="width:8%">Avatar</th><th>Nama</th><th>Peran</th><th>Rating</th><th>Status</th><th style="width:15%" class="text-right">Aksi</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <?php if (!empty($item->avatar)): ?>
                                <img src="<?php echo base_url($item->avatar); ?>" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:50%;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width:36px;height:36px;"><?php echo render_icon('user'); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="font-weight-medium"><?php echo esc_html($item->name ?? '-'); ?></td>
                        <td><?php echo esc_html($item->role ?? '-'); ?></td>
                        <td><?php echo str_repeat('★', (int) ($item->rating ?? 5)); ?></td>
                        <td>
                            <?php if (!empty($item->is_active)): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?php echo render_action_buttons($item->id, '', 'admin/testimonials', $show_trash); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php echo educard_end(); ?>
    </div></div>
</div></section>
<script>$(document).ready(function(){ if($('#testimonials-table').length){ EduTable.init('#testimonials-table'); } });</script>

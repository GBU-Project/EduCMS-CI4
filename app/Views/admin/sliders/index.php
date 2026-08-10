<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark" style="letter-spacing: -0.8px; font-size: 1.7rem;">
                    <?php echo esc_html($title); ?>
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <?php echo generate_breadcrumb($breadcrumbs); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <?php echo render_flash_messages(); ?>

        <!-- List Card -->
        <div class="row">
            <div class="col-12">
                <?php
                    $card_title = (isset($show_trash) && $show_trash) ? 'Tempat Sampah — ' . esc_html($title) : esc_html($title);
                    echo educard_start($card_title, 'images');
                ?>
                    <!-- Action toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <?php if (isset($show_trash) && $show_trash): ?>
                                <a href="<?php echo base_url('admin/sliders'); ?>" class="btn btn-sm btn-outline-secondary">
                                    <?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali ke Daftar
                                </a>
                            <?php else: ?>
                                <a href="<?php echo base_url('admin/sliders/create'); ?>" class="btn btn-sm btn-indigo">
                                    <?php echo render_icon('plus', 'mr-1'); ?> Tambah Slide Baru
                                </a>
                                <a href="<?php echo base_url('admin/sliders?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2">
                                    <?php echo render_icon('trash-can', 'mr-1'); ?> Tempat Sampah
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (empty($list)): ?>
                        <?php echo eduempty_state(
                            'Belum Ada Slide',
                            (isset($show_trash) && $show_trash) ? 'Tempat sampah kosong.' : 'Belum ada slide yang ditambahkan.',
                            'images',
                            (isset($show_trash) && $show_trash) ? '' : 'Tambah Slide',
                            (isset($show_trash) && $show_trash) ? '' : 'admin/sliders/create'
                        ); ?>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="sliders-table" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th>Judul Slide</th>
                                        <th>Gambar</th>
                                        <th>Urutan</th>
                                        <th>Status</th>
                                        <th style="width:15%" class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($list as $item): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td class="font-weight-medium"><?php echo esc_html($item->title); ?></td>
                                            <td>
                                                <?php if (!empty($item->image)): ?>
                                                    <img src="<?php echo base_url($item->image); ?>" alt="slide" style="height:40px;border-radius:4px;">
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo (int)($item->order_num ?? 0); ?></td>
                                            <td>
                                                <?php if (isset($item->status) && $item->status === 'active'): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo render_action_buttons($item->id, '', 'admin/sliders', isset($show_trash) && $show_trash); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php echo educard_end(); ?>
            </div>
        </div>

    </div>
</section>

<script>
    $(document).ready(function() {
        if ($('#sliders-table').length) {
            EduTable.init('#sliders-table');
        }
    });
</script>

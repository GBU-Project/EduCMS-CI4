<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark text-lg" style="letter-spacing: -0.8px; font-size: 1.7rem;">
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

        <!-- Flash Messages -->
        <?php echo render_flash_messages(); ?>

        <!-- List Card -->
        <div class="row">
            <div class="col-12">
                <?php 
                    $card_title = $show_trash ? 'Daftar Tempat Sampah Halaman' : 'Semua Halaman Statis';
                    echo educard_start($card_title, 'file-lines'); 
                ?>
                    <!-- Action toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <?php if ($show_trash): ?>
                                <a href="<?php echo base_url('admin/pages'); ?>" class="btn btn-sm btn-outline-secondary">
                                    <?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali ke Daftar
                                </a>
                            <?php else: ?>
                                <a href="<?php echo base_url('admin/pages/create'); ?>" class="btn btn-sm btn-indigo">
                                    <?php echo render_icon('plus', 'mr-1'); ?> Buat Halaman Baru
                                </a>
                                <a href="<?php echo base_url('admin/pages?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2">
                                    <?php echo render_icon('trash-can', 'mr-1'); ?> Tempat Sampah
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (empty($list)): ?>
                        <?php echo eduempty_state(
                            'Tidak Ada Halaman',
                            $show_trash ? 'Tempat sampah kosong.' : 'Belum ada halaman statis yang dibuat.',
                            'file-lines',
                            $show_trash ? '' : 'Buat Halaman',
                            $show_trash ? '' : 'admin/pages/create'
                        ); ?>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="pages-table" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Judul Halaman</th>
                                        <th>Slug / URL</th>
                                        <th>Template</th>
                                        <th>Status</th>
                                        <th style="width: 15%" class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($list as $item): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td class="font-weight-medium"><?php echo esc_html($item->title); ?></td>
                                            <td><code>/pages/<?php echo esc_html($item->slug); ?></code></td>
                                            <td><span class="badge badge-secondary"><?php echo esc_html($item->template); ?></span></td>
                                            <td>
                                                <?php if ($item->status === 'published'): ?>
                                                    <span class="badge badge-success">Diterbitkan</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning text-dark">Draft</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo render_action_buttons($item->id, $item->slug, 'admin/pages', $show_trash, 'admin/pages/preview/' . $item->id); ?>
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
        if ($('#pages-table').length) {
            EduTable.init('#pages-table');
        }
    });
</script>

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
                    $card_title = $show_trash ? 'Daftar Tempat Sampah Tag' : 'Semua Tag Berita';
                    echo educard_start($card_title, 'tags'); 
                ?>
                    <!-- Action toolbar -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <?php if ($show_trash): ?>
                                <a href="<?php echo base_url('admin/tags'); ?>" class="btn btn-sm btn-outline-secondary">
                                    <?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali ke Daftar
                                </a>
                            <?php else: ?>
                                <a href="<?php echo base_url('admin/tags/create'); ?>" class="btn btn-sm btn-indigo">
                                    <?php echo render_icon('plus', 'mr-1'); ?> Buat Tag Baru
                                </a>
                                <a href="<?php echo base_url('admin/tags?trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2">
                                    <?php echo render_icon('trash-can', 'mr-1'); ?> Tempat Sampah
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (empty($list)): ?>
                        <?php echo eduempty_state(
                            'Tidak Ada Tag',
                            $show_trash ? 'Tempat sampah kosong.' : 'Belum ada tag berita yang dibuat.',
                            'tags',
                            $show_trash ? '' : 'Buat Tag',
                            $show_trash ? '' : 'admin/tags/create'
                        ); ?>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="tags-table" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Nama Tag</th>
                                        <th>Slug</th>
                                        <th style="width: 15%" class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($list as $item): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td class="font-weight-medium"><?php echo esc_html($item->name); ?></td>
                                            <td><code><?php echo esc_html($item->slug); ?></code></td>
                                            <td class="text-right">
                                                <?php echo render_action_buttons($item->id, $item->slug, 'admin/tags', $show_trash); ?>
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
        if ($('#tags-table').length) {
            EduTable.init('#tags-table');
        }
    });
</script>

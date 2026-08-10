<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;">
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

<section class="content">
    <div class="container-fluid">
        <?php echo render_flash_messages(); ?>
        <div class="row">
            <div class="col-12">
                <?php echo educard_start(esc_html($title), 'bars-staggered'); ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <a href="<?php echo base_url('admin/menu-groups/create'); ?>" class="btn btn-sm btn-indigo">
                                <?php echo render_icon('plus', 'mr-1'); ?> Tambah Menu Baru
                            </a>
                        </div>
                    </div>
                    <?php if (empty($list)): ?>
                        <?php echo eduempty_state('Belum Ada Menu', 'Buat menu navigasi baru untuk portal sekolah.', 'bars-staggered', 'Tambah Menu', 'admin/menu-groups/create'); ?>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="menus-table" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th>Nama Menu</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Item</th>
                                        <th style="width:15%" class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($list as $item): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td class="font-weight-medium"><?php echo esc_html($item->name ?? '-'); ?></td>
                                            <td><?php echo esc_html(mb_strimwidth($item->description ?? '-', 0, 60, '...')); ?></td>
                                            <td><span class="badge badge-secondary"><?php echo (int)($item->item_count ?? 0); ?></span></td>
                                            <td class="text-right">
                                                <a href="<?php echo base_url('admin/menus?group_id=' . $item->id); ?>" class="btn btn-xs btn-indigo mr-1" title="Kelola Item Menu">
                                                    <?php echo render_icon('bars-staggered', 'mr-1'); ?> Kelola Item
                                                </a>
                                                <?php echo render_action_buttons($item->id, '', 'admin/menu-groups', FALSE); ?>
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
        if ($('#menus-table').length) { EduTable.init('#menus-table'); }
    });
</script>

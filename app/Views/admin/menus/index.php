<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark text-lg" style="letter-spacing:-0.8px;font-size:1.7rem;">
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
                <div class="card card-premium shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-weight-bold mb-0 text-dark">
                                <?php echo render_icon('bars-staggered', 'text-indigo mr-2'); ?>
                                Kelola Navigasi Website
                            </h5>
                            <div>
                                <a href="<?php echo base_url('admin/menu-groups'); ?>" class="btn btn-xs btn-outline-secondary">
                                    <?php echo render_icon('arrow-left', 'mr-1'); ?> Daftar Menu Group
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Tabs for Menu Groups -->
                        <ul class="nav nav-tabs mb-4">
                            <?php foreach ($menu_groups as $mg): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo ($mg->id == $group_id) ? 'active font-weight-bold text-indigo' : 'text-secondary'; ?>" href="<?php echo base_url('admin/menus?group_id=' . $mg->id); ?>">
                                        <?php echo esc_html($mg->name); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Action Toolbar -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <?php if ($show_trash): ?>
                                    <a href="<?php echo base_url('admin/menus?group_id=' . $group_id); ?>" class="btn btn-sm btn-outline-secondary">
                                        <?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali ke Item Aktif
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo base_url('admin/menus/create?group_id=' . $group_id); ?>" class="btn btn-sm btn-indigo">
                                        <?php echo render_icon('plus', 'mr-1'); ?> Tambah Item Menu
                                    </a>
                                    <a href="<?php echo base_url('admin/menus?group_id=' . $group_id . '&trash=1'); ?>" class="btn btn-sm btn-outline-danger ml-2">
                                        <?php echo render_icon('trash-can', 'mr-1'); ?> Tempat Sampah (<?php echo $this->db->where('group_id', $group_id)->where('deleted_at !=', NULL)->count_all_results('menus'); ?>)
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (empty($list)): ?>
                            <?php echo eduempty_state(
                                'Tidak Ada Item Menu',
                                $show_trash ? 'Tempat sampah kosong.' : 'Belum ada item navigasi di menu group ini.',
                                'link',
                                $show_trash ? '' : 'Tambah Item Menu',
                                $show_trash ? '' : 'admin/menus/create?group_id=' . $group_id
                            ); ?>
                        <?php else: ?>
                            <form action="<?php echo base_url('admin/menus/save_order'); ?>" method="post">
                                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                                <div class="table-responsive">
                                    <table id="menus-table" class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th>Judul Navigasi</th>
                                                <th>Icon</th>
                                                <th>URL / Tujuan</th>
                                                <th>Target</th>
                                                <th>Status</th>
                                                <th style="width: 12%" class="text-center">Urutan</th>
                                                <th style="width: 18%" class="text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($list as $item): ?>
                                                <tr>
                                                    <td><?php echo $no++; ?></td>
                                                    <td class="font-weight-medium">
                                                        <?php 
                                                            // Display indents based on depth
                                                            if ($item->depth > 0) {
                                                                echo '<span class="text-indigo mr-1">' . str_repeat('— ', $item->depth) . '</span>';
                                                            }
                                                            echo esc_html($item->title); 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($item->icon)): ?>
                                                            <span class="badge badge-light py-1.5 px-2">
                                                                <?php echo render_icon($item->icon, 'text-indigo mr-1'); ?>
                                                                <code><?php echo esc_html($item->icon); ?></code>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted text-xs">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <code class="text-xs"><?php echo esc_html($item->url); ?></code>
                                                    </td>
                                                    <td>
                                                        <?php if ($item->target === '_blank'): ?>
                                                            <span class="badge badge-outline-primary text-xs">Tab Baru</span>
                                                        <?php else: ?>
                                                            <span class="text-muted text-xs">Tab Sama</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($item->status === 'published'): ?>
                                                            <span class="badge badge-success">Diterbitkan</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning text-dark">Draft</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($show_trash): ?>
                                                            <span class="text-muted"><?php echo $item->order_num; ?></span>
                                                        <?php else: ?>
                                                            <input type="number" name="orders[<?php echo $item->id; ?>]" value="<?php echo $item->order_num; ?>" class="form-control form-control-sm text-center mx-auto" style="width: 70px;" min="0" required>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php if (!$show_trash): ?>
                                                            <?php if ($item->status === 'published'): ?>
                                                                <a href="<?php echo base_url('admin/menus/toggle_status/' . $item->id); ?>" class="btn btn-xs btn-outline-warning mr-1" title="Arsipkan (Draft)">
                                                                    <?php echo render_icon('eye-slash'); ?>
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="<?php echo base_url('admin/menus/toggle_status/' . $item->id); ?>" class="btn btn-xs btn-outline-success mr-1" title="Terbitkan (Publish)">
                                                                    <?php echo render_icon('eye'); ?>
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        
                                                        <?php 
                                                            // Standard action buttons (edit, delete / restore, force_delete)
                                                            echo render_action_buttons($item->id, '', 'admin/menus', $show_trash); 
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if (!$show_trash): ?>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-sm btn-indigo">
                                            <?php echo render_icon('save', 'mr-1'); ?> Simpan Urutan
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        // We use standard EduTable for client-side search and page filters
        if ($('#menus-table').length) {
            EduTable.init('#menus-table', {
                pageLength: 50, // Higher limit since hierarchy is best viewed on a single page
                ordering: false // Disable DataTables sorting to keep hierarchical flat order
            });
        }
    });
</script>

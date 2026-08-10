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

        <div class="row">
            <div class="col-md-9">
                <?php echo educard_start($page->title, 'eye'); ?>
                    <?php if (!empty($page->banner)): ?>
                        <div class="mb-4">
                            <img src="<?php echo base_url($page->banner); ?>" alt="Banner Halaman" class="img-fluid rounded shadow-sm" style="max-height: 350px; width: 100%; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                    
                    <div class="page-content px-2 py-1">
                        <?php echo sanitize_html($page->content); // sanitized HTML content from editor ?>
                    </div>
                <?php echo educard_end(); ?>
            </div>

            <div class="col-md-3">
                <?php echo educard_start('Info Halaman', 'info-circle'); ?>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item px-0">
                            <b>Status:</b>
                            <span class="float-right badge badge-<?php echo ($page->status === 'published') ? 'success' : 'warning text-dark'; ?>">
                                <?php echo ($page->status === 'published') ? 'Diterbitkan' : 'Draft'; ?>
                            </span>
                        </li>
                        <li class="list-group-item px-0">
                            <b>Template:</b> <span class="float-right text-muted"><?php echo esc_html($page->template); ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <b>Dibuat Pada:</b> <span class="float-right text-muted"><?php echo format_date_id($page->created_at); ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <b>Kunjungan:</b> <span class="float-right text-muted"><?php echo number_format($page->view_count); ?></span>
                        </li>
                    </ul>
                    <a href="<?php echo base_url('admin/pages/edit/' . $page->id); ?>" class="btn btn-indigo btn-block">
                        <?php echo render_icon('edit', 'mr-1'); ?> Edit Halaman
                    </a>
                    <a href="<?php echo base_url('admin/pages'); ?>" class="btn btn-outline-secondary btn-block mt-2">
                        Kembali
                    </a>
                <?php echo educard_end(); ?>
            </div>
        </div>

    </div>
</section>

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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                <?php echo educard_start($post->title, 'eye'); ?>
                    <?php if (!empty($post->image)): ?>
                        <div class="mb-4">
                            <img src="<?php echo base_url($post->image); ?>" alt="Gambar Berita" class="img-fluid rounded shadow-sm" style="max-height: 350px; width: 100%; object-fit: cover;">
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($post->categories)): ?>
                        <div class="mb-3">
                            <?php foreach ($post->categories as $cat): ?>
                                <span class="badge badge-indigo mr-1"><?php echo esc_html($cat->name); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-content px-2 py-1">
                        <?php $this->load->helper('sanitize'); echo sanitize_html($post->content); // sanitized HTML content from editor ?>
                    </div>

                    <?php if (!empty($post->tags)): ?>
                        <hr>
                        <div>
                            <?php foreach ($post->tags as $tag): ?>
                                <span class="badge badge-outline-secondary mr-1">#<?php echo esc_html($tag->name); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php echo educard_end(); ?>
            </div>

            <div class="col-md-3">
                <?php echo educard_start('Info Berita', 'info-circle'); ?>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item px-0">
                            <b>Status:</b>
                            <span class="float-right badge badge-<?php echo ($post->status === 'published') ? 'success' : (($post->status === 'archived') ? 'secondary' : 'warning text-dark'); ?>">
                                <?php echo array('draft' => 'Draft', 'published' => 'Diterbitkan', 'archived' => 'Diarsipkan')[$post->status] ?? ucfirst($post->status); ?>
                            </span>
                        </li>
                        <li class="list-group-item px-0">
                            <b>Unggulan:</b> <span class="float-right text-muted"><?php echo !empty($post->is_featured) ? 'Ya' : 'Tidak'; ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <b>Diterbitkan:</b> <span class="float-right text-muted"><?php echo !empty($post->published_at) ? format_date_id($post->published_at) : '-'; ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <b>Dibuat Pada:</b> <span class="float-right text-muted"><?php echo format_date_id($post->created_at); ?></span>
                        </li>
                        <li class="list-group-item px-0">
                            <b>Kunjungan:</b> <span class="float-right text-muted"><?php echo number_format($post->view_count); ?></span>
                        </li>
                    </ul>
                    <a href="<?php echo base_url('admin/posts/edit/' . $post->id); ?>" class="btn btn-indigo btn-block">
                        <?php echo render_icon('edit', 'mr-1'); ?> Edit Berita
                    </a>
                    <a href="<?php echo base_url('admin/posts'); ?>" class="btn btn-outline-secondary btn-block mt-2">
                        Kembali
                    </a>
                <?php echo educard_end(); ?>
            </div>
        </div>
    </div>
</section>

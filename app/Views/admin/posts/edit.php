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

        <?php echo form_open_multipart('admin/posts/edit/' . $post->id); ?>
        <div class="row">
            <div class="col-md-8">
                <!-- Post Content Form Card -->
                <?php echo educard_start('Konten Berita', 'newspaper'); ?>

                    <?php echo eduform_input(
                        'title',
                        'Judul Berita',
                        set_value('title', $post->title),
                        'text',
                        array('required' => TRUE, 'placeholder' => 'Masukkan judul berita')
                    ); ?>

                    <?php echo eduform_input(
                        'slug',
                        'Slug URL',
                        set_value('slug', $post->slug),
                        'text',
                        array('placeholder' => 'contoh: acara-sekolah-2026')
                    ); ?>

                    <?php echo eduform_textarea(
                        'content',
                        'Konten Berita',
                        set_value('content', $post->content, FALSE),
                        array('rows' => 14, 'placeholder' => 'Ketik isi berita di sini...')
                    ); ?>

                <?php echo educard_end(); ?>

                <!-- SEO Card -->
                <?php echo educard_start('Optimasi SEO', 'search'); ?>

                    <?php echo eduform_input(
                        'meta_title',
                        'Meta Title SEO',
                        set_value('meta_title', isset($seo->meta_title) ? $seo->meta_title : ''),
                        'text',
                        array('placeholder' => 'Judul pencarian Google')
                    ); ?>

                    <?php echo eduform_textarea(
                        'meta_description',
                        'Meta Description SEO',
                        set_value('meta_description', isset($seo->meta_description) ? $seo->meta_description : ''),
                        array('rows' => 3, 'placeholder' => 'Deskripsi ringkas pencarian Google')
                    ); ?>

                    <?php echo eduform_input(
                        'meta_keywords',
                        'Meta Keywords',
                        set_value('meta_keywords', isset($seo->keywords) ? $seo->keywords : ''),
                        'text',
                        array('placeholder' => 'kata, kunci, penelusuran')
                    ); ?>

                <?php echo educard_end(); ?>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <?php echo educard_start('Publikasi', 'paper-plane'); ?>

                    <?php echo eduform_select(
                        'status',
                        'Status Publikasi',
                        array('draft' => 'Draft', 'published' => 'Terbitkan', 'archived' => 'Arsipkan'),
                        set_value('status', $post->status)
                    ); ?>

                    <?php echo eduform_checkbox(
                        'is_featured',
                        'Jadikan Berita Unggulan',
                        set_value('is_featured', $post->is_featured) ? TRUE : FALSE
                    ); ?>

                    <?php if (!empty($post->published_at)): ?>
                        <p class="text-muted small mb-0">
                            <?php echo render_icon('clock', 'mr-1'); ?>
                            Terbit pertama kali: <?php echo format_date_id($post->published_at, TRUE); ?>
                        </p>
                    <?php endif; ?>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-indigo btn-block">
                            <?php echo render_icon('circle-check', 'mr-1'); ?> Perbarui Berita
                        </button>
                        <a href="<?php echo base_url('admin/posts'); ?>" class="btn btn-outline-secondary btn-block mt-2">
                            Batal
                        </a>
                    </div>

                <?php echo educard_end(); ?>

                <?php echo educard_start('Gambar Utama', 'image'); ?>

                    <?php echo eduform_file(
                        'image',
                        'Featured Image',
                        $post->image,
                        array('help' => 'Kosongkan jika tidak ingin mengganti gambar saat ini.')
                    ); ?>

                <?php echo educard_end(); ?>

                <?php echo educard_start('Kategori', 'folder'); ?>

                    <?php echo eduform_input(
                        'category_text',
                        'Kategori',
                        set_value('category_text', $category_text),
                        'text',
                        array('placeholder' => 'Contoh: Pendidikan, Prestasi', 'help' => 'Pisahkan dengan koma. Kategori yang belum ada akan otomatis dibuat.')
                    ); ?>

                <?php echo educard_end(); ?>

                <?php echo educard_start('Tag', 'tags'); ?>

                    <?php echo eduform_input(
                        'tag_text',
                        'Tag',
                        set_value('tag_text', $tag_text),
                        'text',
                        array('placeholder' => 'Contoh: Juara, Lomba, Nasional', 'help' => 'Pisahkan dengan koma. Tag yang belum ada akan otomatis dibuat.')
                    ); ?>

                <?php echo educard_end(); ?>
            </div>
        </div>
        <?php echo form_close(); ?>

    </div>
</section>

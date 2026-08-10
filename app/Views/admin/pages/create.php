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
            <div class="col-md-8">
                <!-- Page Content Form Card -->
                <?php echo educard_start('Konten Halaman', 'file-lines'); ?>
                    <?php echo form_open_multipart('admin/pages/create'); ?>
                        
                        <?php echo eduform_input(
                            'title', 
                            'Judul Halaman', 
                            set_value('title'), 
                            'text', 
                            array('required' => TRUE, 'placeholder' => 'Masukkan judul halaman')
                        ); ?>

                        <?php echo eduform_input(
                            'slug', 
                            'Slug URL', 
                            set_value('slug'), 
                            'text', 
                            array('placeholder' => 'contoh: profil-sekolah (biarkan kosong untuk auto-generate)')
                        ); ?>

                        <?php echo eduform_textarea(
                            'content', 
                            'Konten Halaman', 
                            set_value('content', '', FALSE), 
                            array('rows' => 12, 'placeholder' => 'Ketik isi konten halaman statis di sini...')
                        ); ?>

                <?php echo educard_end(); ?>
            </div>

            <!-- Page Sidebar Metadata Card -->
            <div class="col-md-4">
                <?php echo educard_start('Metadata & Setelan', 'sliders'); ?>

                    <?php echo eduform_file(
                        'banner',
                        'Gambar Banner',
                        '',
                        array('help' => 'Format JPG/PNG, digunakan sebagai gambar utama halaman.')
                    ); ?>

                    <?php echo eduform_select(
                        'template', 
                        'Template Halaman', 
                        $templates, 
                        set_value('template', 'default')
                    ); ?>

                    <?php echo eduform_select(
                        'status', 
                        'Status Publikasi', 
                        array('draft' => 'Draft', 'published' => 'Terbitkan'), 
                        set_value('status', 'published')
                    ); ?>

                    <hr class="my-4 border-light">
                    <h6 class="font-weight-bold text-dark mb-3"><i class="fa-solid fa-search mr-1 text-indigo"></i> Optimasi SEO</h6>

                    <?php echo eduform_input(
                        'meta_title', 
                        'Meta Title SEO', 
                        set_value('meta_title'), 
                        'text', 
                        array('placeholder' => 'Judul pencarian Google')
                    ); ?>

                    <?php echo eduform_textarea(
                        'meta_description', 
                        'Meta Description SEO', 
                        set_value('meta_description'), 
                        array('rows' => 3, 'placeholder' => 'Deskripsi ringkas pencarian Google')
                    ); ?>

                    <?php echo eduform_input(
                        'meta_keywords', 
                        'Meta Keywords', 
                        set_value('meta_keywords'), 
                        'text', 
                        array('placeholder' => 'kata, kunci, penelusuran')
                    ); ?>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-indigo btn-block">
                            <?php echo render_icon('circle-check', 'mr-1'); ?> Simpan Halaman
                        </button>
                        <a href="<?php echo base_url('admin/pages'); ?>" class="btn btn-outline-secondary btn-block mt-2">
                            Batal
                        </a>
                    </div>

                    <?php echo form_close(); ?>
                <?php echo educard_end(); ?>
            </div>
        </div>

    </div>
</section>

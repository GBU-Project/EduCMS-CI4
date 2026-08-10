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
                <?php echo educard_start('Form Tag', 'plus'); ?>
                    <?php echo form_open('admin/tags/create'); ?>
                        
                        <?php echo eduform_input(
                            'name', 
                            'Nama Tag', 
                            set_value('name'), 
                            'text', 
                            array('required' => TRUE, 'placeholder' => 'Masukkan nama tag')
                        ); ?>

                        <?php echo eduform_input(
                            'slug', 
                            'Slug URL', 
                            set_value('slug'), 
                            'text', 
                            array('placeholder' => 'contoh: prestasi-siswa (biarkan kosong untuk auto-generate)')
                        ); ?>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-indigo">
                                <?php echo render_icon('circle-check', 'mr-1'); ?> Simpan Tag
                            </button>
                            <a href="<?php echo base_url('admin/tags'); ?>" class="btn btn-outline-secondary ml-2">
                                Batal
                            </a>
                        </div>

                    <?php echo form_close(); ?>
                <?php echo educard_end(); ?>
            </div>
        </div>

    </div>
</section>

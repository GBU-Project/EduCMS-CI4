<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open('admin/redirects/create'); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Redirect', 'route'); ?>
            <?php echo eduform_input('source_url', 'URL Asal', set_value('source_url'), 'text', array('required' => TRUE, 'placeholder' => '/halaman-lama')); ?>
            <?php echo eduform_input('target_url', 'URL Tujuan', set_value('target_url'), 'text', array('required' => TRUE, 'placeholder' => '/halaman-baru')); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Tipe Redirect', 'paper-plane'); ?>
            <?php echo eduform_select('status_code', 'Status Code', array('301' => '301 - Permanent', '302' => '302 - Temporary'), set_value('status_code', '301')); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan</button>
                <a href="<?php echo base_url('admin/redirects'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

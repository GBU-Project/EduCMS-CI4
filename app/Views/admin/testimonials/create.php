<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open_multipart('admin/testimonials/create'); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Testimoni', 'quote-left'); ?>
            <?php echo eduform_input('name', 'Nama', set_value('name'), 'text', array('required' => TRUE, 'placeholder' => 'Nama orang tua/alumni/siswa')); ?>
            <?php echo eduform_input('role', 'Peran / Status', set_value('role'), 'text', array('required' => TRUE, 'placeholder' => 'Contoh: Orang Tua Siswa, Alumni 2024')); ?>
            <?php echo eduform_textarea('testimonial_message', 'Isi Testimoni', set_value('testimonial_message'), array('rows' => 4, 'required' => TRUE)); ?>
            <?php echo eduform_select('rating', 'Rating', array(5 => '★★★★★ (5)', 4 => '★★★★ (4)', 3 => '★★★ (3)', 2 => '★★ (2)', 1 => '★ (1)'), set_value('rating', '5')); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Publikasi', 'paper-plane'); ?>
            <?php echo eduform_checkbox('is_active', 'Tampilkan di Homepage', set_value('is_active', '1') == '1'); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan</button>
                <a href="<?php echo base_url('admin/testimonials'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
        <?php echo educard_start('Foto (Opsional)', 'image'); ?>
            <?php echo eduform_file('avatar', 'Upload / Pilih Foto', ''); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

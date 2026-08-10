<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open_multipart('admin/galleries/create'); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Album', 'images'); ?>
            <?php echo eduform_input('title', 'Judul Album', set_value('title'), 'text', array('required' => TRUE, 'placeholder' => 'Contoh: Kegiatan Perpisahan 2026')); ?>
            <?php echo eduform_select('type', 'Tipe Galeri', array('photo' => 'Galeri Foto', 'video' => 'Galeri Video'), set_value('type', 'photo'), array('help' => 'Menentukan jenis item yang bisa ditambahkan ke album ini setelah disimpan.')); ?>
            <?php echo eduform_textarea('description', 'Deskripsi', set_value('description'), array('rows' => 3)); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Publikasi', 'paper-plane'); ?>
            <div class="mt-2">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan &amp; Kelola Item</button>
                <a href="<?php echo base_url('admin/galleries'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
        <?php echo educard_start('Cover Album', 'image'); ?>
            <?php echo eduform_file('cover_image', 'Upload / Pilih Cover', ''); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

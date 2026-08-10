<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open_multipart('admin/partners/create'); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Mitra', 'handshake'); ?>
            <?php echo eduform_input('name', 'Nama Mitra', set_value('name'), 'text', array('required' => TRUE, 'placeholder' => 'Nama perusahaan/instansi mitra')); ?>
            <?php echo eduform_input('link', 'Tautan Website (Opsional)', set_value('link'), 'url', array('placeholder' => 'https://')); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Publikasi', 'paper-plane'); ?>
            <div class="mt-2">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan</button>
                <a href="<?php echo base_url('admin/partners'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
        <?php echo educard_start('Logo Mitra', 'image'); ?>
            <?php echo eduform_file('logo', 'Upload / Pilih Logo', '', array('help' => 'Gunakan logo dengan latar transparan (PNG) untuk hasil terbaik.')); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

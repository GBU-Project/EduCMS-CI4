<!-- Content Header -->
<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open_multipart('admin/staff/create'); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Data Staf', 'id-card'); ?>
            <?php echo eduform_input('name', 'Nama Lengkap', set_value('name'), 'text', array('required' => TRUE, 'placeholder' => 'Nama lengkap staf')); ?>
            <?php echo eduform_input('nik', 'NIK', set_value('nik'), 'text', array('placeholder' => 'Nomor Induk Kependudukan')); ?>
            <?php echo eduform_select('gender', 'Jenis Kelamin', array('L' => 'Laki-laki', 'P' => 'Perempuan'), set_value('gender', 'L'), array('required' => TRUE)); ?>
            <?php echo eduform_input('position', 'Jabatan', set_value('position'), 'text', array('required' => TRUE, 'placeholder' => 'Contoh: Tata Usaha, Staf Keuangan')); ?>
            <?php echo eduform_input('email', 'Email', set_value('email'), 'email', array('placeholder' => 'email@sekolah.sch.id')); ?>
            <?php echo eduform_input('phone', 'No. Telepon', set_value('phone'), 'text', array('placeholder' => '0812xxxxxxxx')); ?>
            <?php echo eduform_textarea('address', 'Alamat', set_value('address'), array('rows' => 3)); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Status', 'paper-plane'); ?>
            <?php echo eduform_select('status', 'Status', array('active' => 'Aktif', 'inactive' => 'Nonaktif'), set_value('status', 'active')); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan</button>
                <a href="<?php echo base_url('admin/staff'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
        <?php echo educard_start('Foto Staf', 'image'); ?>
            <?php echo eduform_file('photo', 'Upload Foto', '', array('help' => 'Format JPG/PNG. Rekomendasi: 300×300 px.')); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

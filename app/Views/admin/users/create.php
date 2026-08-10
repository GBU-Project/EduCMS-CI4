<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open('admin/users/create'); ?>
<div class="row">
    <div class="col-md-7">
        <?php echo educard_start('Detail Pengguna', 'user'); ?>
            <?php echo eduform_input('full_name', 'Nama Lengkap', set_value('full_name'), 'text', array('required' => TRUE)); ?>
            <?php echo eduform_input('username', 'Username', set_value('username'), 'text', array('placeholder' => 'Kosongkan untuk dibuat otomatis dari nama')); ?>
            <?php echo eduform_input('email', 'Email', set_value('email'), 'email', array('required' => TRUE)); ?>
            <?php echo eduform_input('password', 'Password', '', 'password', array('required' => TRUE, 'help' => 'Minimal 6 karakter.')); ?>
            <?php echo eduform_select('status', 'Status', array('active' => 'Aktif', 'inactive' => 'Nonaktif'), set_value('status', 'active')); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan</button>
                <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-5">
        <?php echo educard_start('Peran (Roles)', 'user-shield'); ?>
            <?php echo eduform_checkbox_group('role_ids', 'Pilih Role', $roles, array()); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

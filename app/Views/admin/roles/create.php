<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open('admin/roles/create'); ?>
<div class="row">
    <div class="col-md-5">
        <?php echo educard_start('Detail Role', 'user-shield'); ?>
            <?php echo eduform_input('name', 'Nama Role', set_value('name'), 'text', array('required' => TRUE, 'placeholder' => 'Contoh: Kepala Sekolah')); ?>
            <?php echo eduform_textarea('description', 'Deskripsi', set_value('description'), array('rows' => 3, 'placeholder' => 'Deskripsi singkat peran ini...')); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan Role</button>
                <a href="<?php echo base_url('admin/roles'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-7">
        <?php echo educard_start('Hak Akses (Permissions)', 'key'); ?>
            <?php echo eduform_checkbox_group('permission_ids', 'Pilih Permission', $permissions, array(), array('help' => 'Centang permission yang boleh diakses oleh role ini.')); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

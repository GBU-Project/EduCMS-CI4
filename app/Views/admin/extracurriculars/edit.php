<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open_multipart('admin/extracurriculars/edit/' . $row->id); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Ekstrakurikuler', 'volleyball'); ?>
            <?php echo eduform_input('name', 'Nama Ekstrakurikuler', set_value('name', $row->name ?? ''), 'text', array('required' => TRUE)); ?>
            <?php echo eduform_input('coach', 'Pembina', set_value('coach', $row->coach ?? ''), 'text'); ?>
            <?php echo eduform_input('schedule', 'Jadwal', set_value('schedule', $row->schedule ?? ''), 'text'); ?>
            <?php echo eduform_textarea('description', 'Deskripsi', set_value('description', $row->description ?? ''), array('rows' => 5, 'required' => TRUE)); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Simpan', 'paper-plane'); ?>
            <div class="mt-1">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Perbarui</button>
                <a href="<?php echo base_url('admin/extracurriculars'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
        <?php echo educard_start('Foto', 'image'); ?>
            <?php echo eduform_file('image', 'Ganti Foto', $row->image ?? '', array('help' => 'Kosongkan jika tidak ingin mengganti.')); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

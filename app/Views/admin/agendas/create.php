<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open('admin/agendas/create'); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Agenda', 'calendar-days'); ?>
            <?php echo eduform_input('title', 'Judul Agenda', set_value('title'), 'text', array('required' => TRUE, 'placeholder' => 'Nama kegiatan/agenda')); ?>
            <?php echo eduform_input('location', 'Lokasi', set_value('location'), 'text', array('placeholder' => 'Tempat pelaksanaan')); ?>
            <?php echo eduform_input('coordinator', 'Koordinator', set_value('coordinator'), 'text', array('placeholder' => 'Penanggung jawab kegiatan')); ?>
            <?php echo eduform_textarea('description', 'Deskripsi', set_value('description'), array('rows' => 5, 'required' => TRUE, 'placeholder' => 'Deskripsi agenda kegiatan...')); ?>
        <?php echo educard_end(); ?>

        <?php echo educard_start('Optimasi SEO', 'search'); ?>
            <?php echo eduform_input('meta_title', 'Meta Title SEO', set_value('meta_title'), 'text', array('placeholder' => 'Judul pencarian Google')); ?>
            <?php echo eduform_textarea('meta_description', 'Meta Description SEO', set_value('meta_description'), array('rows' => 3, 'placeholder' => 'Deskripsi ringkas pencarian Google')); ?>
            <?php echo eduform_input('meta_keywords', 'Meta Keywords', set_value('meta_keywords'), 'text', array('placeholder' => 'kata, kunci, penelusuran')); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Publikasi', 'paper-plane'); ?>
            <?php echo eduform_select('status', 'Status Publikasi', array('draft' => 'Draft', 'published' => 'Terbitkan'), set_value('status', 'draft')); ?>
        <?php echo educard_end(); ?>

        <?php echo educard_start('Jadwal', 'clock'); ?>
            <?php echo eduform_input('start_date', 'Tanggal Mulai', set_value('start_date'), 'datetime-local', array('required' => TRUE)); ?>
            <?php echo eduform_input('end_date', 'Tanggal Selesai', set_value('end_date'), 'datetime-local'); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan</button>
                <a href="<?php echo base_url('admin/agendas'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

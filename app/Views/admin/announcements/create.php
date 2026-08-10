<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open('admin/announcements/create'); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Pengumuman', 'bullhorn'); ?>
            <?php echo eduform_input('title', 'Judul Pengumuman', set_value('title'), 'text', array('required' => TRUE, 'placeholder' => 'Judul pengumuman')); ?>
            <?php echo eduform_input('slug', 'Slug URL', set_value('slug'), 'text', array('placeholder' => 'contoh: pengumuman-libur-semester')); ?>
            <?php echo eduform_textarea('content', 'Isi Pengumuman', set_value('content'), array('rows' => 8, 'placeholder' => 'Tulis isi pengumuman di sini...')); ?>
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
            <?php echo eduform_checkbox('is_pinned', 'Sematkan pengumuman ini (tampil di atas)', set_value('is_pinned') == '1'); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Simpan</button>
                <a href="<?php echo base_url('admin/announcements'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

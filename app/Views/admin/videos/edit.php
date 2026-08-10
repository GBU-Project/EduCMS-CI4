<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
<?php echo form_open_multipart('admin/videos/edit/' . $row->id); ?>
<div class="row">
    <div class="col-md-8">
        <?php echo educard_start('Detail Video', 'video'); ?>
            <?php echo eduform_input('title', 'Judul Video', set_value('title', $row->title ?? ''), 'text', array('required' => TRUE)); ?>
            <?php echo eduform_select('platform', 'Platform', array('youtube' => 'YouTube', 'vimeo' => 'Vimeo', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'other' => 'Lainnya'), set_value('platform', $row->platform ?? 'youtube')); ?>
            <?php echo eduform_input('video_url', 'URL / Embed Video', set_value('video_url', $row->video_url ?? ''), 'text', array('required' => TRUE, 'help' => 'Tempel link video dari platform eksternal. Tidak ada file video yang diunggah ke server.')); ?>
            <?php echo eduform_textarea('description', 'Deskripsi', set_value('description', $row->description ?? ''), array('rows' => 4)); ?>
        <?php echo educard_end(); ?>
    </div>
    <div class="col-md-4">
        <?php echo educard_start('Publikasi', 'paper-plane'); ?>
            <?php echo eduform_select('status', 'Status Publikasi', array('draft' => 'Draft', 'published' => 'Terbitkan'), set_value('status', $row->status ?? 'draft')); ?>
            <?php echo eduform_input('order_num', 'Urutan Tampil', set_value('order_num', $row->order_num ?? '0'), 'number'); ?>
            <?php echo eduform_checkbox('is_featured', 'Tampilkan sebagai video utama (Featured)', set_value('is_featured', !empty($row->is_featured) ? '1' : '0') == '1'); ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Perbarui</button>
                <a href="<?php echo base_url('admin/videos'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
            </div>
        <?php echo educard_end(); ?>
        <?php echo educard_start('Thumbnail (Opsional)', 'image'); ?>
            <p class="text-muted small">Kosongkan untuk YouTube — thumbnail akan diambil otomatis dari video.</p>
            <?php echo eduform_file('thumbnail', 'Ganti Thumbnail', $row->thumbnail ?? ''); ?>
        <?php echo educard_end(); ?>
    </div>
</div>
<?php echo form_close(); ?>
</div></section>

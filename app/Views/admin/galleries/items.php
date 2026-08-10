<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row">
        <div class="col-md-4">
            <?php echo educard_start('Tambah Item', 'plus'); ?>
            <?php if ($gallery->type === 'video'): ?>
                <?php echo form_open('admin/galleries/add_item/' . $gallery->id); ?>
                    <?php echo eduform_input('file_path', 'URL Video', '', 'text', array('required' => TRUE, 'placeholder' => 'https://www.youtube.com/watch?v=xxxx', 'help' => 'Tautan video eksternal (YouTube/Vimeo/dsb). Tidak ada file yang diunggah.')); ?>
                    <?php echo eduform_input('caption', 'Keterangan (Opsional)', '', 'text'); ?>
                    <button type="submit" class="btn btn-indigo btn-block mt-2"><?php echo render_icon('plus', 'mr-1'); ?> Tambahkan Video</button>
                <?php echo form_close(); ?>
            <?php else: ?>
                <?php echo form_open_multipart('admin/galleries/add_item/' . $gallery->id); ?>
                    <?php echo eduform_file('file_path', 'Upload Baru / Pilih dari Media Library', '', array('help' => 'Gunakan tombol upload untuk foto baru, atau "Pilih dari Media" untuk foto yang sudah ada di Media Library.')); ?>
                    <?php echo eduform_input('caption', 'Keterangan (Opsional)', '', 'text'); ?>
                    <button type="submit" class="btn btn-indigo btn-block mt-2"><?php echo render_icon('plus', 'mr-1'); ?> Tambahkan Foto</button>
                <?php echo form_close(); ?>
            <?php endif; ?>
            <?php echo educard_end(); ?>
            <a href="<?php echo base_url('admin/galleries/edit/' . $gallery->id); ?>" class="btn btn-outline-secondary btn-block mt-2"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali ke Album</a>
        </div>
        <div class="col-md-8">
            <?php echo educard_start('Item dalam Album: ' . esc_html($gallery->title), $gallery->type === 'video' ? 'video' : 'images'); ?>
            <?php if (empty($items)): ?>
                <?php echo eduempty_state('Belum Ada Item', 'Tambahkan foto atau video di sebelah kiri.', $gallery->type === 'video' ? 'video' : 'image'); ?>
            <?php else: ?>
                <div class="row">
                <?php foreach ($items as $it): ?>
                    <div class="col-6 col-md-4 mb-3">
                        <div class="card h-100">
                            <?php if ($it->file_type === 'video_url'): ?>
                                <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height:100px;">
                                    <?php echo render_icon('circle-play', 'fa-2x'); ?>
                                </div>
                            <?php else: ?>
                                <img src="<?php echo base_url($it->file_path); ?>" class="card-img-top" style="height:100px;object-fit:cover;" alt="">
                            <?php endif; ?>
                            <div class="card-body p-2">
                                <p class="small text-truncate mb-2" title="<?php echo esc_attr($it->caption ?? ''); ?>"><?php echo esc_html($it->caption ?: '-'); ?></p>
                                <button type="button" class="btn btn-xs btn-danger btn-block js-confirm-delete" data-delete-url="<?php echo base_url('admin/galleries/delete_item/' . $it->id); ?>"><?php echo render_icon('trash', 'mr-1'); ?> Hapus</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php echo educard_end(); ?>
        </div>
    </div>
</div></section>

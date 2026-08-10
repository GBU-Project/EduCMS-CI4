<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;">
                    <?php echo esc_html($title); ?>
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <?php echo generate_breadcrumb($breadcrumbs); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php echo form_open_multipart('admin/sliders/edit/' . $row->id); ?>
        <div class="row">
            <div class="col-md-8">
                <?php echo educard_start('Detail Slide', 'images'); ?>
                    <?php echo eduform_input('title', 'Judul Slide', set_value('title', $row->title ?? ''), 'text', array('required' => TRUE, 'placeholder' => 'Masukkan judul slide')); ?>
                    <?php echo eduform_input('subtitle', 'Sub-judul / Deskripsi', set_value('subtitle', $row->subtitle ?? ''), 'text', array('placeholder' => 'Teks kecil di bawah judul (opsional)')); ?>
                    <?php echo eduform_input('link_url', 'URL Tautan', set_value('link_url', $row->link_url ?? ''), 'url', array('placeholder' => 'https://...')); ?>
                    <?php echo eduform_input('link_text', 'Teks Tombol', set_value('link_text', $row->link_text ?? ''), 'text'); ?>
                    <?php echo eduform_input('order_num', 'Urutan Tampil', set_value('order_num', $row->order_num ?? 0), 'number', array('help' => 'Angka kecil tampil lebih dulu.')); ?>
                <?php echo educard_end(); ?>
            </div>
            <div class="col-md-4">
                <?php echo educard_start('Publikasi', 'paper-plane'); ?>
                    <?php echo eduform_select('status', 'Status', array('active' => 'Aktif', 'inactive' => 'Nonaktif'), set_value('status', $row->status ?? 'active')); ?>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-indigo btn-block"><?php echo render_icon('circle-check', 'mr-1'); ?> Perbarui Slide</button>
                        <a href="<?php echo base_url('admin/sliders'); ?>" class="btn btn-outline-secondary btn-block mt-2">Batal</a>
                    </div>
                <?php echo educard_end(); ?>
                <?php echo educard_start('Gambar Slide', 'image'); ?>
                    <?php echo eduform_file('image', 'Ganti Gambar', $row->image ?? '', array('help' => 'Kosongkan jika tidak ingin mengganti gambar.')); ?>
                <?php echo educard_end(); ?>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</section>

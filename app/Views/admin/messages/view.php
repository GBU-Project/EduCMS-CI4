<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <div class="row"><div class="col-md-8">
        <?php echo educard_start('Detail Pesan', 'envelope-open'); ?>
            <dl class="row mb-0">
                <dt class="col-sm-3">Nama</dt><dd class="col-sm-9"><?php echo esc_html($row->name ?? '-'); ?></dd>
                <dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?php echo esc_html($row->email ?? '-'); ?></dd>
                <dt class="col-sm-3">Telepon</dt><dd class="col-sm-9"><?php echo esc_html($row->phone ?? '-'); ?></dd>
                <dt class="col-sm-3">Subjek</dt><dd class="col-sm-9"><?php echo esc_html($row->subject ?? '-'); ?></dd>
                <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9"><?php echo esc_html($row->created_at ?? '-'); ?></dd>
            </dl>
            <hr>
            <p style="white-space:pre-line;"><?php echo esc_html($row->message ?? ''); ?></p>
        <?php echo educard_end(); ?>
        <a href="<?php echo base_url('admin/messages'); ?>" class="btn btn-outline-secondary"><?php echo render_icon('arrow-left', 'mr-1'); ?> Kembali ke Daftar Pesan</a>
    </div></div>
</div></section>

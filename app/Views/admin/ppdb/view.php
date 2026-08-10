<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <div class="row">
        <div class="col-md-8">
            <?php echo educard_start('Data Pendaftar', 'user-graduate'); ?>
                <dl class="row mb-0">
                    <dt class="col-sm-4">No. Registrasi</dt><dd class="col-sm-8"><?php echo esc_html($row->registration_number ?? '-'); ?></dd>
                    <dt class="col-sm-4">Nama Lengkap</dt><dd class="col-sm-8"><?php echo esc_html($row->full_name ?? '-'); ?></dd>
                    <dt class="col-sm-4">Jenis Kelamin</dt><dd class="col-sm-8"><?php echo (($row->gender ?? '') === 'P') ? 'Perempuan' : 'Laki-laki'; ?></dd>
                    <dt class="col-sm-4">NISN</dt><dd class="col-sm-8"><?php echo esc_html($row->nisn ?? '-'); ?></dd>
                    <dt class="col-sm-4">NIK</dt><dd class="col-sm-8"><?php echo esc_html($row->nik ?? '-'); ?></dd>
                    <dt class="col-sm-4">Tempat, Tanggal Lahir</dt><dd class="col-sm-8"><?php echo esc_html(($row->place_of_birth ?? '-') . ', ' . ($row->date_of_birth ?? '-')); ?></dd>
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8"><?php echo esc_html($row->address ?? '-'); ?></dd>
                    <dt class="col-sm-4">No. HP</dt><dd class="col-sm-8"><?php echo esc_html($row->phone ?? '-'); ?></dd>
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8"><?php echo esc_html($row->email ?? '-'); ?></dd>
                    <dt class="col-sm-4">Asal Sekolah</dt><dd class="col-sm-8"><?php echo esc_html($row->previous_school ?? '-'); ?></dd>
                    <dt class="col-sm-4">Nama Orang Tua</dt><dd class="col-sm-8"><?php echo esc_html($row->parent_name ?? '-'); ?></dd>
                    <dt class="col-sm-4">No. HP Orang Tua</dt><dd class="col-sm-8"><?php echo esc_html($row->parent_phone ?? '-'); ?></dd>
                    <dt class="col-sm-4">Tanggal Daftar</dt><dd class="col-sm-8"><?php echo esc_html($row->created_at ?? '-'); ?></dd>
                </dl>
            <?php echo educard_end(); ?>
        </div>
        <div class="col-md-4">
            <?php echo educard_start('Ubah Status', 'paper-plane'); ?>
                <?php echo form_open('admin/ppdb/update_status/' . $row->id); ?>
                <?php echo eduform_select('status', 'Status Verifikasi', array('pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'accepted' => 'Diterima', 'rejected' => 'Ditolak'), $row->status ?? 'pending'); ?>
                <button type="submit" class="btn btn-indigo btn-block mt-2"><?php echo render_icon('circle-check', 'mr-1'); ?> Perbarui Status</button>
                <?php echo form_close(); ?>
                <a href="<?php echo base_url('admin/ppdb'); ?>" class="btn btn-outline-secondary btn-block mt-2">Kembali</a>
            <?php echo educard_end(); ?>
        </div>
    </div>
</div></section>

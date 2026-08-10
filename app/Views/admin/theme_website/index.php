<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>

    <?php echo educard_start('Template Website Aktif', 'swatchbook'); ?>
        <p class="text-secondary small mb-4">
            Pilih template yang digunakan untuk tampilan depan website. Setiap template memiliki gaya visualnya sendiri
            — pengaturan warna dan font akan tersedia mengikuti template yang dipilih.
        </p>

        <?php echo form_open('admin/theme-website/save'); ?>
        <div class="row">
            <?php foreach ($themes as $key => $theme): ?>
                <?php
                    $is_active   = ($active_theme === $key);
                    $card_border = $is_active ? '2px solid #6366f1' : '1px solid #e5e7eb';
                ?>
                <div class="col-md-6 mb-3">
                    <label class="d-block h-100 mb-0" style="cursor:pointer;">
                        <div class="p-4 h-100" style="border:<?php echo $card_border; ?>;border-radius:12px;background:#ffffff;">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <input type="radio" name="active_theme" value="<?php echo esc_attr($key); ?>"
                                    <?php echo $is_active ? 'checked' : ''; ?>>
                                <?php if ($is_active): ?>
                                    <span class="badge badge-indigo" style="background:#6366f1;color:#fff;padding:6px 12px;font-size:0.8rem;border-radius:6px;">Aktif</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="font-weight-bold text-dark mb-2"><?php echo esc_html($theme['name']); ?></h5>
                            <p class="text-secondary small mb-0"><?php echo esc_html($theme['description']); ?></p>
                        </div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-indigo mt-2">
            <?php echo render_icon('floppy-disk', 'mr-1'); ?> Simpan Theme Website
        </button>
        <?php echo form_close(); ?>
    <?php echo educard_end(); ?>

</div></section>

<?php $groups = array(
    'general'    => array('label' => 'Umum', 'icon' => 'gear'),
    'school'     => array('label' => 'Data Sekolah', 'icon' => 'school'),
    'social'     => array('label' => 'Media Sosial', 'icon' => 'share-nodes'),
    'smtp'       => array('label' => 'Email (SMTP)', 'icon' => 'envelope'),
    'seo'        => array('label' => 'SEO', 'icon' => 'magnifying-glass'),
    'homepage'   => array('label' => 'Homepage', 'icon' => 'house'),
);
$i = 0;
?>
<div class="content-header"><div class="container-fluid"><div class="row mb-2 align-items-center">
    <div class="col-sm-6"><h1 class="m-0 font-weight-bold text-dark" style="letter-spacing:-0.8px;font-size:1.7rem;"><?php echo esc_html($title); ?></h1></div>
    <div class="col-sm-6"><div class="float-sm-right"><?php echo generate_breadcrumb($breadcrumbs); ?></div></div>
</div></div></div>
<section class="content"><div class="container-fluid">
    <?php echo render_flash_messages(); ?>
    <?php echo form_open('admin/settings/save'); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="card card-premium">
                <div class="list-group list-group-flush" id="settings-tabs" role="tablist">
                    <?php foreach ($groups as $key => $g): ?>
                        <a class="list-group-item list-group-item-action <?php echo $i === 0 ? 'active' : ''; ?>" id="tab-<?php echo $key; ?>-btn" data-toggle="list" href="#tab-<?php echo $key; ?>" role="tab">
                            <?php echo render_icon($g['icon'], 'mr-2'); ?> <?php echo esc_html($g['label']); ?>
                        </a>
                    <?php $i++; endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <?php $i = 0; foreach ($groups as $key => $g):
                    $fields = $all_settings[$key] ?? array();
                ?>
                <div class="tab-pane fade <?php echo $i === 0 ? 'show active' : ''; ?>" id="tab-<?php echo $key; ?>" role="tabpanel">
                    <?php echo educard_start($g['label'], $g['icon']); ?>
                        <?php if ($key === 'homepage'):
                            // ====================================================
                            // RC07-001: Homepage Section Manager v2
                            // ====================================================
                            // Instead of rendering raw key-value pairs, show a
                            // structured section manager table. This groups the
                            // per-section .enabled, .order, .title, .subtitle keys
                            // into a single visual row per section.
                            $hp_section_registry = array(
                                'hero'          => array('label' => 'Hero / Banner', 'icon' => 'image'),
                                'welcome'       => array('label' => 'Sambutan Kepala Sekolah', 'icon' => 'hand-wave'),
                                'vision'        => array('label' => 'Visi & Misi', 'icon' => 'eye'),
                                'stats'         => array('label' => 'Statistik Ringkas', 'icon' => 'chart-bar'),
                                'programs'      => array('label' => 'Ekstrakurikuler', 'icon' => 'medal'),
                                'news'          => array('label' => 'Berita Sekolah', 'icon' => 'newspaper'),
                                'announcements' => array('label' => 'Pengumuman', 'icon' => 'bullhorn'),
                                'agenda'        => array('label' => 'Agenda Mendatang', 'icon' => 'calendar'),
                                'videos'        => array('label' => 'Video', 'icon' => 'video'),
                                'gallery'       => array('label' => 'Galeri Kegiatan', 'icon' => 'images'),
                                'partners'      => array('label' => 'Mitra Sekolah', 'icon' => 'handshake'),
                                'testimonials'  => array('label' => 'Testimoni', 'icon' => 'quote-left'),
                                'ppdb'          => array('label' => 'PPDB Online', 'icon' => 'user-plus'),
                                'cta'           => array('label' => 'Call to Action', 'icon' => 'phone'),
                            );

                            // Build sorted list from current DB values
                            $hp_rows = array();
                            foreach ($hp_section_registry as $sec_key => $sec_meta) {
                                $enabled_key = $sec_key . '.enabled';
                                $order_key   = $sec_key . '.order';
                                $title_key   = $sec_key . '.title';
                                $sub_key     = $sec_key . '.subtitle';

                                $is_enabled = isset($fields[$enabled_key]) ? ($fields[$enabled_key]->value == '1') : TRUE;
                                $order_val  = isset($fields[$order_key])   ? (int) $fields[$order_key]->value   : 0;
                                $title_val  = isset($fields[$title_key])   ? $fields[$title_key]->value         : '';
                                $sub_val    = isset($fields[$sub_key])     ? $fields[$sub_key]->value           : '';

                                $hp_rows[] = array(
                                    'key'       => $sec_key,
                                    'label'     => $sec_meta['label'],
                                    'icon'      => $sec_meta['icon'],
                                    'enabled'   => $is_enabled,
                                    'order'     => $order_val,
                                    'title'     => $title_val,
                                    'subtitle'  => $sub_val,
                                );
                            }

                            // Sort by order ASC
                            usort($hp_rows, function ($a, $b) { return $a['order'] - $b['order']; });
                        ?>
                            <style>
                            #homepage-section-table tr.js-sortable-row {
                                transition: background-color 0.15s ease, border-color 0.15s ease;
                            }
                            #homepage-section-table tr.js-sortable-row.dragging {
                                opacity: 0.5;
                                background-color: #e0e7ff !important;
                                border: 2px dashed #6366f1 !important;
                            }
                            #homepage-section-table tr.js-sortable-row.drag-over {
                                border-top: 3px solid #6366f1 !important;
                                background-color: #f1f5f9 !important;
                            }
                            .js-drag-handle {
                                cursor: grab !important;
                                user-select: none;
                            }
                            .js-drag-handle:active {
                                cursor: grabbing !important;
                            }
                            </style>
                            <p class="text-muted small mb-3">Atur urutan dan visibilitas section yang tampil pada halaman depan website. Drag baris atau gunakan tombol panah untuk mengubah urutan.</p>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0" id="homepage-section-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:40px;" class="text-center"></th>
                                            <th style="width:50px;" class="text-center">#</th>
                                            <th>Section</th>
                                            <th style="width:110px;" class="text-center">Urutan</th>
                                            <th style="width:100px;" class="text-center">Posisi</th>
                                            <th style="width:100px;" class="text-center">Tampilkan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $row_num = 1; foreach ($hp_rows as $sec): ?>
                                        <tr class="js-sortable-row" draggable="true" data-key="<?php echo $sec['key']; ?>">
                                            <td class="text-center align-middle js-drag-handle" style="cursor:grab;" title="Drag untuk mengubah urutan" aria-label="Drag untuk mengubah urutan">
                                                <i class="fas fa-grip-vertical text-muted"></i>
                                            </td>
                                            <td class="text-center text-muted align-middle js-row-num"><?php echo $row_num++; ?></td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <?php echo function_exists('render_icon') ? render_icon($sec['icon'], 'mr-2 text-indigo') : ''; ?>
                                                    <strong><?php echo esc_html($sec['label']); ?></strong>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="number"
                                                       name="settings[homepage][<?php echo str_replace('.', ':', $sec['key']); ?>:order]"
                                                       class="form-control form-control-sm text-center js-order-input"
                                                       value="<?php echo (int) $sec['order']; ?>"
                                                       min="0" step="10"
                                                       style="width:80px;margin:0 auto;"
                                                       readonly>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Urutan Section">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm js-move-up" title="Pindah ke atas" aria-label="Pindah ke atas">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm js-move-down" title="Pindah ke bawah" aria-label="Pindah ke bawah">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="hidden" name="settings[homepage][<?php echo str_replace('.', ':', $sec['key']); ?>:enabled]" value="0">
                                                <div class="custom-control custom-switch d-flex justify-content-center">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="hp-toggle-<?php echo $sec['key']; ?>"
                                                           name="settings[homepage][<?php echo str_replace('.', ':', $sec['key']); ?>:enabled]"
                                                           value="1"
                                                           <?php echo $sec['enabled'] ? 'checked' : ''; ?>>
                                                    <label class="custom-control-label" for="hp-toggle-<?php echo $sec['key']; ?>"></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Collapsible Title/Subtitle overrides per section -->
                            <div class="mt-4">
                                <h6 class="font-weight-bold text-dark mb-3"><?php echo function_exists('render_icon') ? render_icon('pen-to-square', 'mr-1') : ''; ?> Kustomisasi Judul Section (Opsional)</h6>
                                <p class="text-muted small mb-3">Kosongkan jika ingin menggunakan judul bawaan tema.</p>
                                <?php foreach ($hp_rows as $sec): ?>
                                    <?php
                                        // Skip sections that don't have title/subtitle fields
                                        $has_title = isset($fields[$sec['key'] . '.title']);
                                        $has_sub   = isset($fields[$sec['key'] . '.subtitle']);
                                        if (!$has_title && !$has_sub) continue;
                                    ?>
                                    <div class="card card-outline card-secondary mb-2">
                                        <div class="card-header py-2 px-3" data-toggle="collapse" data-target="#hp-title-<?php echo $sec['key']; ?>" style="cursor:pointer;">
                                            <span class="font-weight-semibold small"><?php echo function_exists('render_icon') ? render_icon($sec['icon'], 'mr-1') : ''; ?> <?php echo esc_html($sec['label']); ?></span>
                                            <span class="float-right text-muted small"><?php echo render_icon('chevron-down'); ?></span>
                                        </div>
                                        <div class="collapse" id="hp-title-<?php echo $sec['key']; ?>">
                                            <div class="card-body py-2 px-3">
                                                <?php if ($has_title): ?>
                                                <div class="form-group mb-2">
                                                    <label class="small font-weight-semibold">Judul</label>
                                                    <input type="text" name="settings[homepage][<?php echo str_replace('.', ':', $sec['key']); ?>:title]" class="form-control form-control-sm" value="<?php echo esc_attr($sec['title']); ?>" placeholder="Judul bawaan tema">
                                                </div>
                                                <?php endif; ?>
                                                <?php if ($has_sub): ?>
                                                <div class="form-group mb-1">
                                                    <label class="small font-weight-semibold">Subtitle</label>
                                                    <input type="text" name="settings[homepage][<?php echo str_replace('.', ':', $sec['key']); ?>:subtitle]" class="form-control form-control-sm" value="<?php echo esc_attr($sec['subtitle']); ?>" placeholder="Subtitle bawaan tema">
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php elseif (empty($fields)): ?>
                            <p class="text-muted small mb-0">Belum ada pengaturan pada grup ini.</p>
                        <?php else: ?>
                            <?php foreach ($fields as $field_key => $row): ?>

                                <?php
                                    // CI3's Input::_clean_input_keys() rejects any POST key that
                                    // isn't [a-z0-9:_/|-] — a literal '.' fails that check and
                                    // silently collapses to array key FALSE=>0. Encode dot as ':'.
                                    $field_name  = 'settings[' . $key . '][' . str_replace('.', ':', $field_key) . ']';
                                    $is_textarea = in_array($field_key, array('site_description', 'address', 'site_keywords', 'maps_embed', 'wa_default_message', 'welcome_speech', 'vision', 'mission'));

                                    // Generic suffix-based rendering
                                    $is_enabled_toggle = (substr($field_key, -8) === '.enabled');
                                    $is_order_field    = (substr($field_key, -6) === '.order');

                                    // RC06-005: Unified Media Picker for ALL Image Fields
                                    $is_image_picker = in_array($field_key, array('site_logo', 'site_favicon', 'og_image', 'site_banner', 'hero_image', 'principal_photo')) ||
                                                       (substr($field_key, -6) === '.image') ||
                                                       (substr($field_key, -9) === '.bg_image');

                                    // Label mapping
                                    $rc5_label_prefix_map = array(
                                        'news'          => 'Berita',
                                        'announcements' => 'Pengumuman',
                                        'partners'      => 'Mitra',
                                        'testimonials'  => 'Testimoni',
                                        'videos'        => 'Video',
                                        'programs'      => 'Ekstrakurikuler',
                                    );
                                    $key_parts = explode('.', $field_key, 2);
                                    if (isset($rc5_label_prefix_map[$key_parts[0]])) {
                                        $field_label = trim($rc5_label_prefix_map[$key_parts[0]] . ' ' . (isset($key_parts[1]) ? ucwords(str_replace('_', ' ', $key_parts[1])) : ''));
                                    } else {
                                        $field_label = ucwords(str_replace(array('.', '_'), ' ', $field_key));
                                    }

                                    // TASK 1 & TASK 5: Helper Text Mapping per Field Key
                                    $helper_texts = array(
                                        'site_name'          => 'Nama resmi instansi/sekolah yang akan ditampilkan pada header dan footer website.',
                                        'site_tagline'       => 'Slogan atau motto singkat sekolah.',
                                        'site_description'   => 'Deskripsi ringkas sekolah untuk pengindeksan mesin pencari (SEO).',
                                        'site_keywords'      => 'Kata kunci pencarian dipisahkan dengan tanda koma.',
                                        'site_email'         => 'Alamat email resmi sekolah untuk korespondensi publik.',
                                        'email'              => 'Alamat email resmi sekolah.',
                                        'site_phone'         => 'Nomor telepon resmi atau WhatsApp sekolah.',
                                        'phone'              => 'Nomor telepon resmi sekolah.',
                                        'wa_default_message' => 'Pesan salam otomatis yang akan terisi di kotak obrolan saat pengunjung mengklik tombol WhatsApp.',
                                        'address'            => 'Alamat lokasi fisik sekolah lengkap.',
                                        'facebook'           => 'Tautan halaman profil Facebook sekolah (contoh: https://facebook.com/sekolah).',
                                        'instagram'          => 'Tautan akun Instagram resmi sekolah (contoh: https://instagram.com/sekolah).',
                                        'youtube'            => 'Tautan channel YouTube resmi sekolah.',
                                        'tiktok'             => 'Tautan akun TikTok resmi sekolah.',
                                        'maps_embed'         => 'Tag <iframe> atau URL lokasi dari Google Maps untuk pratinjau dan halaman kontak.',
                                        'site_logo'          => 'Logo utama sekolah (format PNG/JPG, disarankan latar transparan).',
                                        'site_favicon'       => 'Favicon tab browser (format PNG/ICO 32x32 pixel).',
                                        'og_image'           => 'Gambar pratinjau yang tampil saat link website dibagikan ke WhatsApp atau media sosial.',
                                        'welcome_speech'     => 'Teks pesan sambutan resmi dari Kepala Sekolah yang akan ditampilkan pada homepage.',
                                        'principal_photo'    => 'Foto resmi Kepala Sekolah untuk kartu sambutan (format JPG/PNG).',
                                        'vision'             => 'Teks Visi Sekolah yang menjadi landasan pendidikan.',
                                        'mission'            => 'Teks Misi Sekolah (dapat berupa beberapa poin kalimat).',
                                    );
                                    $current_helper = $helper_texts[$field_key] ?? '';
                                ?>
                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-semibold text-dark mb-1"><?php echo esc_html($field_label); ?></label>
                                    
                                    <?php if ($is_image_picker):
                                        $picker_safe_id = preg_replace('/[^a-zA-Z0-9_-]/', '-', $key . '-' . $field_key);
                                        $preview_id     = 'rc5-picker-' . $picker_safe_id . '-preview';
                                        $hidden_id      = 'rc5-picker-' . $picker_safe_id . '-value';
                                        $current_val    = $row->value ?? '';
                                        $img_src        = !empty($current_val) ? base_url($current_val) : '';
                                    ?>
                                        <div class="mb-2 p-2 bg-light border rounded align-items-center" id="<?php echo esc_attr($preview_id); ?>-wrap" style="<?php echo empty($current_val) ? 'display:none;' : 'display:inline-block;'; ?>">
                                            <img src="<?php echo esc_attr($img_src); ?>" id="<?php echo esc_attr($preview_id); ?>" alt="<?php echo esc_attr($field_label); ?>" class="img-thumbnail" style="max-height:120px;max-width:100%;object-fit:contain;">
                                        </div>
                                        <input type="hidden" name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($hidden_id); ?>" value="<?php echo esc_attr($current_val); ?>">
                                        <div class="mt-1 btn-group-sm">
                                            <button type="button" class="btn btn-sm btn-outline-indigo js-image-picker-trigger" data-target-hidden="<?php echo esc_attr($hidden_id); ?>" data-target-preview="<?php echo esc_attr($preview_id); ?>">
                                                <?php echo render_icon('images', 'mr-1'); ?> Ganti Gambar
                                            </button>
                                            <a href="<?php echo esc_attr($img_src); ?>" id="<?php echo esc_attr($preview_id); ?>-link" target="_blank" class="btn btn-sm btn-outline-info js-rc5-image-preview-btn" style="<?php echo empty($current_val) ? 'display:none;' : ''; ?>">
                                                <?php echo render_icon('eye', 'mr-1'); ?> Pratinjau
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger js-rc5-image-clear" data-target-hidden="<?php echo esc_attr($hidden_id); ?>" data-target-preview-wrap="<?php echo esc_attr($preview_id); ?>-wrap" data-target-preview-link="<?php echo esc_attr($preview_id); ?>-link">
                                                <?php echo render_icon('trash', 'mr-1'); ?> Hapus
                                            </button>
                                        </div>

                                        <?php if (!empty($current_helper)): ?>
                                            <small class="form-text text-muted mt-1"><?php echo esc_html($current_helper); ?></small>
                                        <?php endif; ?>

                                    <?php elseif ($field_key === 'maps_embed'): ?>
                                        <!-- TASK 2: Google Maps Embed & Live Preview -->
                                        <textarea name="<?php echo esc_attr($field_name); ?>" id="field-maps-embed" class="form-control mb-2" rows="3" placeholder="Contoh: <iframe src=&quot;https://www.google.com/maps/embed?...&quot; ...></iframe>"><?php echo esc_html($row->value ?? ''); ?></textarea>
                                        <small class="form-text text-muted mb-2"><?php echo esc_html($current_helper); ?></small>
                                        <div id="maps-embed-preview-wrapper" class="mt-2 p-2 border rounded bg-light" style="<?php echo empty($row->value) ? 'display:none;' : ''; ?>">
                                            <label class="small font-weight-bold text-secondary mb-1">Pratinjau Lokasi Google Maps:</label>
                                            <div id="maps-embed-preview-box" style="overflow:hidden;border-radius:6px;max-height:260px;">
                                                <?php echo $row->value ?? ''; ?>
                                            </div>
                                        </div>

                                    <?php elseif ($is_enabled_toggle): ?>
                                        <input type="hidden" name="<?php echo esc_attr($field_name); ?>" value="0">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   id="field-<?php echo esc_attr($key . '-' . $field_key); ?>"
                                                   name="<?php echo esc_attr($field_name); ?>"
                                                   value="1"
                                                   <?php echo !empty($row->value) ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="field-<?php echo esc_attr($key . '-' . $field_key); ?>">Tampilkan section ini</label>
                                        </div>

                                    <?php elseif ($is_order_field): ?>
                                        <input type="number" name="<?php echo esc_attr($field_name); ?>" class="form-control" value="<?php echo esc_attr($row->value ?? '0'); ?>">

                                    <?php elseif ($is_textarea): ?>
                                        <textarea name="<?php echo esc_attr($field_name); ?>" class="form-control" rows="3"><?php echo esc_html($row->value ?? ''); ?></textarea>
                                        <?php if (!empty($current_helper)): ?>
                                            <small class="form-text text-muted mt-1"><?php echo esc_html($current_helper); ?></small>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <input type="text" name="<?php echo esc_attr($field_name); ?>" class="form-control" value="<?php echo esc_attr($row->value ?? ''); ?>">
                                        <?php if (!empty($current_helper)): ?>
                                            <small class="form-text text-muted mt-1"><?php echo esc_html($current_helper); ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php echo educard_end(); ?>
                </div>
                <?php $i++; endforeach; ?>
                <div class="mt-3 pb-4">
                    <button type="submit" class="btn btn-indigo btn-lg px-4 shadow-sm"><?php echo render_icon('circle-check', 'mr-2'); ?> Simpan Semua Setelan</button>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div></section>

<script>
(function () {
    function initHomepageSettings() {
        // ---------------------------------------------------------------------
        // 1. Image Clear & Google Maps Preview (using jQuery when available)
        // ---------------------------------------------------------------------
        if (typeof $ !== 'undefined') {
            $('.js-rc5-image-clear').off('click').on('click', function () {
                var $btn = $(this);
                var hiddenId = $btn.data('target-hidden');
                var wrapId   = $btn.data('target-preview-wrap');
                var linkId   = $btn.data('target-preview-link');

                $('#' + hiddenId).val('');
                $('#' + wrapId).hide();
                $('#' + linkId).hide();
            });

            $('#field-maps-embed').off('input change').on('input change', function () {
                var val = $(this).val().trim();
                var $wrap = $('#maps-embed-preview-wrapper');
                var $box  = $('#maps-embed-preview-box');

                if (val !== '') {
                    $box.html(val);
                    $wrap.show();
                } else {
                    $box.empty();
                    $wrap.hide();
                }
            });

            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                        var $img = $(mutation.target);
                        var src  = $img.attr('src');
                        var imgId = $img.attr('id');
                        if (src && imgId) {
                            $('#' + imgId + '-link').attr('href', src).show();
                            $('#' + imgId + '-wrap').show();
                        }
                    }
                });
            });

            $('img[id^="rc5-picker-"]').each(function() {
                observer.observe(this, { attributes: true });
            });
        }

        // ---------------------------------------------------------------------
        // 2. RC07-002: Homepage Drag & Drop & Posisi Move Up/Down (Vanilla JS)
        // ---------------------------------------------------------------------
        var tableBody = document.querySelector('#homepage-section-table tbody');
        if (!tableBody || tableBody.dataset.sortableInit === 'true') return;
        tableBody.dataset.sortableInit = 'true';

        var dragSrcEl = null;

        function updateOrderNumbers() {
            var rows = tableBody.querySelectorAll('tr.js-sortable-row');
            rows.forEach(function (row, index) {
                var newOrder = (index + 1) * 10;
                var rowNumEl = row.querySelector('.js-row-num');
                var orderInputEl = row.querySelector('.js-order-input');
                if (rowNumEl) rowNumEl.textContent = index + 1;
                if (orderInputEl) orderInputEl.value = newOrder;
            });
        }

        // Run initial re-numbering
        updateOrderNumbers();

        // Drag & Drop Handlers
        tableBody.addEventListener('dragstart', function (e) {
            var row = e.target.closest('tr.js-sortable-row');
            if (!row) return;
            dragSrcEl = row;
            row.classList.add('dragging');
            if (e.dataTransfer) {
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', '');
            }
        });

        tableBody.addEventListener('dragover', function (e) {
            e.preventDefault();
            var row = e.target.closest('tr.js-sortable-row');
            if (!row || !dragSrcEl || dragSrcEl === row) return;

            if (e.dataTransfer) {
                e.dataTransfer.dropEffect = 'move';
            }

            var bounding = row.getBoundingClientRect();
            var offset = e.clientY - bounding.top;
            if (offset > bounding.height / 2) {
                row.after(dragSrcEl);
            } else {
                row.before(dragSrcEl);
            }
        });

        tableBody.addEventListener('dragenter', function (e) {
            var row = e.target.closest('tr.js-sortable-row');
            if (row && row !== dragSrcEl) {
                row.classList.add('drag-over');
            }
        });

        tableBody.addEventListener('dragleave', function (e) {
            var row = e.target.closest('tr.js-sortable-row');
            if (row) {
                row.classList.remove('drag-over');
            }
        });

        tableBody.addEventListener('dragend', function (e) {
            var rows = tableBody.querySelectorAll('tr.js-sortable-row');
            rows.forEach(function (r) {
                r.classList.remove('dragging', 'drag-over');
            });
            updateOrderNumbers();
        });

        // Click Handlers for Posisi (Move Up / Move Down)
        tableBody.addEventListener('click', function (e) {
            var btnUp = e.target.closest('.js-move-up');
            var btnDown = e.target.closest('.js-move-down');

            if (btnUp) {
                e.preventDefault();
                e.stopPropagation();
                var row = btnUp.closest('tr.js-sortable-row');
                var prev = row ? row.previousElementSibling : null;
                if (row && prev && prev.classList.contains('js-sortable-row')) {
                    row.parentNode.insertBefore(row, prev);
                    updateOrderNumbers();
                }
            } else if (btnDown) {
                e.preventDefault();
                e.stopPropagation();
                var row = btnDown.closest('tr.js-sortable-row');
                var next = row ? row.nextElementSibling : null;
                if (row && next && next.classList.contains('js-sortable-row')) {
                    row.parentNode.insertBefore(next, row);
                    updateOrderNumbers();
                }
            }
        });
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(initHomepageSettings, 1);
    } else {
        document.addEventListener('DOMContentLoaded', initHomepageSettings);
    }
    window.addEventListener('load', initHomepageSettings);
})();
</script>



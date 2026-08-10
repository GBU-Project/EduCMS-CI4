<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark text-lg" style="letter-spacing:-0.8px;font-size:1.7rem;">
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

<section class="content">
    <div class="container-fluid">
        <?php echo render_flash_messages(); ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card card-premium shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title font-weight-bold mb-0 text-dark">
                            <?php echo render_icon('plus', 'text-indigo mr-2'); ?>
                            Form Tambah Item Navigasi
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        <form action="<?php echo base_url('admin/menus/create?group_id=' . $group_id); ?>" method="post" id="menu-form">
                            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                            <input type="hidden" name="internal_url" id="internal_url" value="">

                            <!-- Title -->
                            <?php echo eduform_input('title', 'Judul Menu', set_value('title'), 'text', array('required' => TRUE, 'placeholder' => 'Contoh: Profil Sekolah')); ?>

                            <!-- Parent -->
                            <div class="form-group mb-3">
                                <label for="parent_id" class="form-label font-weight-semibold">Parent Menu (Induk)</label>
                                <select name="parent_id" id="parent_id" class="form-control custom-select">
                                    <option value="">[Tanpa Parent / Menu Utama]</option>
                                    <?php foreach ($parents as $p): ?>
                                        <option value="<?php echo $p->id; ?>" <?php echo set_select('parent_id', $p->id); ?>>
                                            <?php echo esc_html($p->indented_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-muted text-xs d-block mt-1">Pilih menu induk jika item ini merupakan sub-menu (child).</span>
                            </div>

                            <!-- Icon & Description (Optional) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <?php echo eduform_input('icon', 'Icon (FontAwesome Class)', set_value('icon'), 'text', array('placeholder' => 'Contoh: house, user, link', 'help' => 'Nama icon FontAwesome v6 (tanpa fa- prefix).')); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo eduform_input('description', 'Deskripsi Singkat', set_value('description'), 'text', array('placeholder' => 'Keterangan menu', 'help' => 'Opsional: Penjelasan singkat fungsi menu.')); ?>
                                </div>
                            </div>

                            <!-- Link Type Selection -->
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-semibold d-block">Tipe Link Tujuan</label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="link_type_internal" name="link_type" class="custom-control-input" value="internal" checked>
                                    <label class="custom-control-label font-weight-normal" for="link_type_internal">Link Internal (Konten Portal)</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="link_type_external" name="link_type" class="custom-control-input" value="external">
                                    <label class="custom-control-label font-weight-normal" for="link_type_external">Link External (URL Manual)</label>
                                </div>
                            </div>

                            <!-- Internal Link Options Container -->
                            <div id="internal_link_container" class="card bg-light border-0 p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label for="internal_type" class="form-label text-xs font-weight-bold uppercase text-secondary">Tipe Modul / Fitur</label>
                                            <select id="internal_type" class="form-control form-control-sm custom-select">
                                                <option value="">-- Pilih Fitur Portal --</option>
                                                <option value="pages">Halaman Statis (Detail)</option>
                                                <option value="posts">Berita / Artikel (Detail)</option>
                                                <option value="categories">Kategori Berita (Filter)</option>
                                                <option value="tags">Tag Berita (Filter)</option>
                                                <option value="achievements">Prestasi Sekolah (Detail)</option>
                                                <option value="agendas">Agenda Kegiatan (Detail)</option>
                                                <option value="extracurriculars">Ekstrakurikuler (Detail)</option>
                                                <option value="news_index">Portal Berita (Daftar)</option>
                                                <option value="ppdb">PPDB Online (Pendaftaran)</option>
                                                <option value="contact">Hubungi Kami (Kontak)</option>
                                                <option value="gallery_photo">Galeri Foto</option>
                                                <option value="gallery_video">Galeri Video</option>
                                                <option value="download">Unduhan File</option>
                                                <option value="guru">Direktori Guru</option>
                                                <option value="staff">Direktori Staf</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="internal_value_wrapper" style="display:none;">
                                        <div class="form-group mb-2">
                                            <label for="internal_value" class="form-label text-xs font-weight-bold uppercase text-secondary">Pilih Item Konten</label>
                                            <select id="internal_value" class="form-control form-control-sm custom-select">
                                                <!-- Dynamic Options by JS -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- URL Input (Read-only for internal, editable for external) -->
                            <?php echo eduform_input('url', 'URL Tujuan', set_value('url'), 'text', array('required' => TRUE, 'placeholder' => 'Contoh: /page/profil atau https://google.com', 'readonly' => TRUE, 'help' => 'URL navigasi link menu.')); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Target -->
                                    <?php echo eduform_select('target', 'Buka Link Di', array('_self' => 'Tab yang Sama (Default)', '_blank' => 'Tab Baru (Open in New Tab)'), set_value('target', '_self')); ?>
                                </div>
                                <div class="col-md-6">
                                    <!-- Status -->
                                    <?php echo eduform_select('status', 'Status Publikasi', array('published' => 'Aktif (Diterbitkan)', 'draft' => 'Draft (Diarsipkan)'), set_value('status', 'published')); ?>
                                </div>
                            </div>

                            <!-- Sort Order -->
                            <?php echo eduform_input('order_num', 'Sort Order (No Urutan)', set_value('order_num', '1'), 'number', array('required' => TRUE, 'placeholder' => '1', 'help' => 'Angka urutan tampilan menu.')); ?>

                            <!-- Form Actions -->
                            <div class="mt-4 pt-3 border-t">
                                <button type="submit" class="btn btn-sm btn-indigo mr-2">
                                    <?php echo render_icon('save', 'mr-1'); ?> Buat Item Menu
                                </button>
                                <a href="<?php echo base_url('admin/menus?group_id=' . $group_id); ?>" class="btn btn-sm btn-outline-secondary">
                                    Batal
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-premium shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title font-weight-bold mb-0 text-dark">
                            <?php echo render_icon('circle-info', 'text-indigo mr-2'); ?>
                            Petunjuk Pengisian
                        </h5>
                    </div>
                    <div class="card-body small text-secondary">
                        <p class="mb-2"><strong>Judul Menu:</strong> Teks yang akan tampil pada menu navigasi website.</p>
                        <p class="mb-2"><strong>Link Internal:</strong> Disarankan menggunakan link internal agar format URL selalu cocok dengan sistem routing EduCMS, serta URL terupdate otomatis bila slug konten berubah.</p>
                        <p class="mb-2"><strong>Link External:</strong> Gunakan link external bila ingin mengarahkan navigasi ke website luar (misal: `https://kemdikbud.go.id`). Wajib menyertakan `http://` atau `https://`.</p>
                        <p class="mb-0"><strong>Circular Loop Guard:</strong> Sistem mencegah pengisian induk menu (parent) yang memutar secara melingkar untuk mencegah error fatal di frontend.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pass PHP variables to Javascript -->
<script>
    const internalData = <?php echo json_encode($internal_data); ?>;
    
    $(document).ready(function() {
        // Toggle Link Type Handler
        $('input[name="link_type"]').on('change', function() {
            const type = $(this).val();
            if (type === 'external') {
                $('#internal_link_container').slideUp(200);
                $('#url').prop('readonly', false).val('').focus();
                $('#internal_url').val('');
                $('#internal_type').val('');
                $('#internal_value_wrapper').hide();
            } else {
                $('#internal_link_container').slideDown(200);
                $('#url').prop('readonly', true).val('');
                $('#internal_url').val('');
            }
        });

        // Internal Module Select Handler
        $('#internal_type').on('change', function() {
            const modType = $(this).val();
            const valueWrapper = $('#internal_value_wrapper');
            const valSelect = $('#internal_value');
            
            valSelect.html(''); // Clear previous options
            
            if (!modType) {
                valueWrapper.hide();
                $('#url').val('');
                $('#internal_url').val('');
                return;
            }

            // Fixed URLs list
            const fixedUrls = {
                'news_index': '/posts',
                'ppdb': '/ppdb',
                'contact': '/contact',
                'gallery_photo': '/galeri-foto',
                'gallery_video': '/galeri-video',
                'download': '/download',
                'guru': '/guru',
                'staff': '/staff'
            };

            if (fixedUrls.hasOwnProperty(modType)) {
                valueWrapper.hide();
                const targetUrl = fixedUrls[modType];
                $('#url').val(targetUrl);
                $('#internal_url').val(targetUrl);
            } else {
                // Dynamic loaded list
                const items = internalData[modType] || [];
                if (items.length === 0) {
                    valSelect.html('<option value="">-- Tidak ada data --</option>');
                } else {
                    valSelect.html('<option value="">-- Pilih Item Konten --</option>');
                    items.forEach(function(item) {
                        const optionTitle = item.title || item.name;
                        valSelect.append('<option value="' + item.slug + '">' + optionTitle + '</option>');
                    });
                }
                valueWrapper.show();
                $('#url').val('');
                $('#internal_url').val('');
            }
        });

        // Dynamic Item Selection Handler
        $('#internal_value').on('change', function() {
            const slug = $(this).val();
            const modType = $('#internal_type').val();
            
            if (!slug) {
                $('#url').val('');
                $('#internal_url').val('');
                return;
            }

            let targetUrl = '';
            if (modType === 'pages') {
                targetUrl = '/page/' + slug;
            } else if (modType === 'posts') {
                targetUrl = '/berita/' + slug;
            } else if (modType === 'categories') {
                targetUrl = '/berita?category=' + slug;
            } else if (modType === 'tags') {
                targetUrl = '/berita?tag=' + slug;
            } else if (modType === 'achievements') {
                targetUrl = '/prestasi/' + slug;
            } else if (modType === 'agendas') {
                targetUrl = '/agenda/' + slug;
            } else if (modType === 'extracurriculars') {
                targetUrl = '/ekstrakurikuler/' + slug;
            }

            $('#url').val(targetUrl);
            $('#internal_url').val(targetUrl);
        });
    });
</script>

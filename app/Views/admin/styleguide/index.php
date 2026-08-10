<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark text-lg" style="letter-spacing: -0.8px; font-size: 1.7rem;">
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

        <!-- 1. Theme, Toast, & Alerts (Using EduCard and EduAlert wrappers) -->
        <div class="row mb-4">
            <div class="col-md-6">
                <?php echo educard_start('Theme Manager & Alerts (EduAlert)', 'palette'); ?>
                    <p class="text-muted text-sm mb-4">Ganti skema warna admin panel secara dinamis dan trigger SweetAlert2 dialogs menggunakan wrapper <code>EduAlert</code>.</p>
                    
                    <div class="mb-3">
                        <button onclick="cycleTheme()" class="btn btn-indigo mr-2">
                            <?php echo render_icon('circle-half-stroke', 'mr-1'); ?> Switch Theme
                        </button>
                        <button onclick="EduAlert.toast('success', 'Berhasil! Data Anda telah diperbarui.')" class="btn btn-success">
                            <?php echo render_icon('circle-check', 'mr-1'); ?> Trigger Toast
                        </button>
                    </div>
                    <div>
                        <button onclick="EduAlert.success('Informasi Sistem', 'Ini adalah contoh alert sukses dari wrapper EduAlert.')" class="btn btn-info mr-2">
                            <?php echo render_icon('circle-info', 'mr-1'); ?> Alert Success
                        </button>
                        <button onclick="triggerConfirmDemo()" class="btn btn-danger">
                            <?php echo render_icon('trash', 'mr-1'); ?> Confirm Delete
                        </button>
                    </div>
                <?php echo educard_end(); ?>
            </div>

            <!-- 2. Loading Overlay & Reusable Empty State -->
            <div class="col-md-6">
                <div id="loading-card-demo" class="card card-premium shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title font-weight-bold mb-0 text-dark"><?php echo render_icon('spinner', 'text-indigo mr-2'); ?>Loading & Empty States</h5>
                        <button onclick="triggerLoadingDemo()" class="btn btn-xs btn-outline-indigo">Demo Loading</button>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <!-- Reusable Empty State Helper -->
                        <?php echo eduempty_state(
                            'Data Kosong',
                            'Belum ada lampiran file yang diunggah ke media library untuk direktori sekolah.',
                            'folder-open',
                            'Tambah Berkas',
                            'admin/styleguide'
                        ); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Form Layouts & Validations (Using EduForm Helper) -->
        <div class="row mb-4">
            <div class="col-md-12">
                <?php echo educard_start('Standard Form Layout & Validations (EduForm)', 'keyboard'); ?>
                    <form onsubmit="return false;">
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo eduform_input(
                                    'field_success', 
                                    'Field Valid (Success)', 
                                    'Input yang benar', 
                                    'text', 
                                    array('help' => 'Data tersedia dan siap diproses!')
                                ); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo eduform_input(
                                    'field_invalid', 
                                    'Field Invalid (Error)', 
                                    'invalid-email@', 
                                    'email', 
                                    array('help' => 'Harap masukkan format email yang benar.')
                                ); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo eduform_textarea(
                                    'field_disabled', 
                                    'Disabled Textarea', 
                                    'Isi konten textarea yang terkunci...', 
                                    array('disabled' => TRUE, 'rows' => 2, 'help' => 'Help text: Input dinonaktifkan sistem.')
                                ); ?>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <?php echo eduform_select(
                                    'status_select', 
                                    'Pilih Status (Dropdown Select)', 
                                    array('active' => 'Aktif', 'inactive' => 'Nonaktif'), 
                                    'active'
                                ); ?>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center pt-3">
                                <?php echo eduform_checkbox('check_demo', 'Saya menyetujui syarat & ketentuan sistem', TRUE); ?>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center pt-3">
                                <?php echo eduform_radio('radio_demo', 'radio_1', 'Pilihan Radio Opsi 1', TRUE); ?>
                                <?php echo eduform_radio('radio_demo', 'radio_2', 'Pilihan Radio Opsi 2'); ?>
                            </div>
                        </div>
                    </form>
                <?php echo educard_end(); ?>
            </div>
        </div>

        <!-- 4. DataTable Wrapper & Tables (Using EduTable wrapper) -->
        <div class="row mb-4">
            <div class="col-md-12">
                <?php echo educard_start('DataTable Wrapper (EduTable) & Badges', 'table'); ?>
                    <div class="table-responsive">
                        <table id="styleguide-table" class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Peran (Role)</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Super Administrator</td>
                                    <td>admin</td>
                                    <td>admin@educms.local</td>
                                    <td><span class="badge badge-indigo">Super Admin</span></td>
                                    <td><span class="badge badge-success">Aktif</span></td>
                                    <td>
                                        <button class="btn btn-xs btn-indigo"><?php echo render_icon('edit'); ?></button>
                                        <button onclick="triggerConfirmDemo()" class="btn btn-xs btn-danger"><?php echo render_icon('trash'); ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Budiono Siregar</td>
                                    <td>budiono</td>
                                    <td>budiono@sekolah.sch.id</td>
                                    <td><span class="badge badge-primary">Administrator</span></td>
                                    <td><span class="badge badge-success">Aktif</span></td>
                                    <td>
                                        <button class="btn btn-xs btn-indigo"><?php echo render_icon('edit'); ?></button>
                                        <button onclick="triggerConfirmDemo()" class="btn btn-xs btn-danger"><?php echo render_icon('trash'); ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Siti Aminah, S.Pd.</td>
                                    <td>siti_editor</td>
                                    <td>siti@sekolah.sch.id</td>
                                    <td><span class="badge badge-warning text-dark">Editor</span></td>
                                    <td><span class="badge badge-success">Aktif</span></td>
                                    <td>
                                        <button class="btn btn-xs btn-indigo"><?php echo render_icon('edit'); ?></button>
                                        <button onclick="triggerConfirmDemo()" class="btn btn-xs btn-danger"><?php echo render_icon('trash'); ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hendrik Pratama</td>
                                    <td>hendrik</td>
                                    <td>hendrik@sekolah.sch.id</td>
                                    <td><span class="badge badge-secondary">PPDB Admin</span></td>
                                    <td><span class="badge badge-danger">Nonaktif</span></td>
                                    <td>
                                        <button class="btn btn-xs btn-indigo"><?php echo render_icon('edit'); ?></button>
                                        <button onclick="triggerConfirmDemo()" class="btn btn-xs btn-danger"><?php echo render_icon('trash'); ?></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php echo educard_end(); ?>
            </div>
        </div>

    </div>
</section>

<!-- Page script integrations -->
<script>
    $(document).ready(function() {
        // Initialize DataTable using the compliant EduTable Wrapper
        EduTable.init('#styleguide-table');

        // Apply error indicator styling to invalid demo field on load
        $('#field_invalid').addClass('is-invalid');
    });

    function triggerLoadingDemo() {
        showLoading('#loading-card-demo');
        setTimeout(function() {
            hideLoading('#loading-card-demo');
            EduAlert.toast('success', 'Proses loading simulasi selesai!');
        }, 2000);
    }

    function triggerConfirmDemo() {
        EduAlert.confirm('Hapus Data?', 'Data yang terhapus tidak dapat dikembalikan kembali.', function() {
            EduAlert.toast('success', 'Data berhasil dihapus.');
        });
    }
</script>

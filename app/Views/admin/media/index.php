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

<section class="content">
    <div class="container-fluid">
        <?php echo render_flash_messages(); ?>
        <div class="row">
            <div class="col-12">
                <?php echo educard_start('Media Library', 'photo-film'); ?>
                    <!-- Drag & Drop Upload Area -->
                    <div class="mb-4">
                        <div id="media-dropzone" class="text-center p-4 mb-3" style="border:2px dashed #ced4da;border-radius:.5rem;transition:border-color .15s ease,background-color .15s ease;">
                            <?php echo render_icon('cloud-arrow-up', 'fa-2x text-muted mb-2'); ?>
                            <p class="mb-2">Drag &amp; Drop File di sini</p>
                            <p class="text-muted small mb-2">atau</p>
                            <button type="button" class="btn btn-outline-indigo btn-sm" id="media-dropzone-browse">Pilih File</button>
                            <input type="file" id="media-dropzone-input" class="d-none" multiple accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx">
                        </div>
                        <div id="media-upload-progress-list"></div>
                    </div>

                    <!-- Bulk Selection & Action Toolbar (RC06-004B) -->
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" id="media-bulk-toolbar">
                        <div class="custom-control custom-checkbox align-self-center">
                            <input type="checkbox" class="custom-control-input" id="media-select-all">
                            <label class="custom-control-label font-weight-semibold text-secondary" for="media-select-all" style="cursor:pointer;user-select:none;">
                                Pilih Semua (Halaman Ini)
                            </label>
                        </div>
                        <div>
                            <button type="button" class="btn btn-danger btn-sm" id="media-bulk-delete-btn" disabled>
                                <?php echo render_icon('trash', 'mr-1'); ?> <span id="media-bulk-delete-label">Delete Selected</span>
                            </button>
                        </div>
                    </div>

                    <div id="media-empty-state" <?php echo empty($list) ? '' : 'class="d-none"'; ?>>
                        <?php echo eduempty_state('Belum Ada Media', 'Unggah file pertama Anda menggunakan area drag & drop di atas.', 'photo-film'); ?>
                    </div>
                    <div class="row" id="media-grid" <?php echo empty($list) ? 'style="display:none;"' : ''; ?>>
                        <?php if (!empty($list)): ?>
                            <?php foreach ($list as $item): ?>
                                <?php
                                    $is_image = isset($item->mime_type) && strpos($item->mime_type, 'image') !== FALSE;
                                    $file_url = !empty($item->directory) && !empty($item->disk_name) ? $item->directory . '/' . $item->disk_name : '';
                                ?>
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3 media-item">
                                    <div class="card border shadow-sm h-100 position-relative media-card-wrapper">
                                        <div class="position-absolute" style="top:6px;left:6px;z-index:10;">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input js-media-select-item" id="media-item-<?php echo $item->id; ?>" value="<?php echo $item->id; ?>">
                                                <label class="custom-control-label" for="media-item-<?php echo $item->id; ?>" style="cursor:pointer;"></label>
                                            </div>
                                        </div>
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:100px;overflow:hidden;">
                                            <?php if ($is_image && !empty($file_url)): ?>
                                                <img src="<?php echo base_url($file_url); ?>" alt="<?php echo esc_attr($item->filename ?? ''); ?>" style="max-height:100px;max-width:100%;object-fit:cover;">
                                            <?php else: ?>
                                                <?php echo render_icon('file', 'fa-3x text-secondary'); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body p-2">
                                            <p class="small text-truncate mb-1" title="<?php echo esc_attr($item->filename ?? ''); ?>"><?php echo esc_html($item->filename ?? '-'); ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <?php if (!empty($file_url)): ?>
                                                    <a href="<?php echo base_url($file_url); ?>" target="_blank" class="btn btn-xs btn-info" title="Lihat"><?php echo render_icon('eye'); ?></a>
                                                <?php else: ?>
                                                    <span></span>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-xs btn-danger js-confirm-force-delete"
                                                    data-force-delete-url="<?php echo base_url('admin/media/delete/' . $item->id); ?>"
                                                    title="Hapus"><?php echo render_icon('trash'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php echo educard_end(); ?>
            </div>
        </div>
    </div>
</section>

<script>
    // NOTE: this view is rendered before footer.php, which is where
    // jQuery/SweetAlert2/educms-admin.js actually get <script> tags onto
    // the page (Template::load_admin renders header -> sidebar -> this
    // view -> footer). An inline `$(document).ready(...)` here would run
    // immediately as the browser parses this tag — before jQuery exists —
    // and throw, silently killing every handler below. Deferring to
    // DOMContentLoaded guarantees the footer's scripts have already
    // executed by the time this callback runs.
    document.addEventListener('DOMContentLoaded', function() {
        // =========================================================
        // Media Library Drag & Drop Upload (RC5-011)
        // Interface only — uploads still go through the existing
        // upload_media() service via admin/media/drop_upload, the same
        // service used by the editor and (previously) the plain form.
        // =========================================================
        var $dropzone      = $('#media-dropzone');
        var $browseBtn     = $('#media-dropzone-browse');
        var $fileInput     = $('#media-dropzone-input');
        var $progressList  = $('#media-upload-progress-list');
        var $grid          = $('#media-grid');
        var $emptyState    = $('#media-empty-state');
        var uploadUrl       = '<?php echo base_url('admin/media/drop_upload'); ?>';
        var csrfName        = $('meta[name="csrf-token-name"]').attr('content');
        var csrfValue       = $('meta[name="csrf-token-value"]').attr('content');

        // =========================================================
        // Media Library Bulk Selection & Delete (RC06-004B)
        // =========================================================
        var $selectAll     = $('#media-select-all');
        var $bulkDeleteBtn = $('#media-bulk-delete-btn');
        var $bulkDeleteLabel = $('#media-bulk-delete-label');

        function updateBulkState() {
            var $checkedItems = $('.js-media-select-item:checked');
            var count = $checkedItems.length;
            var totalItems = $('.js-media-select-item').length;

            if (count > 0) {
                $bulkDeleteBtn.prop('disabled', false);
                $bulkDeleteLabel.text('Delete Selected (' + count + ')');
            } else {
                $bulkDeleteBtn.prop('disabled', true);
                $bulkDeleteLabel.text('Delete Selected');
            }

            if (totalItems > 0 && count === totalItems) {
                $selectAll.prop('checked', true);
                $selectAll.prop('indeterminate', false);
            } else if (count > 0) {
                $selectAll.prop('checked', false);
                $selectAll.prop('indeterminate', true);
            } else {
                $selectAll.prop('checked', false);
                $selectAll.prop('indeterminate', false);
            }
        }

        $(document).on('change', '.js-media-select-item', function() {
            updateBulkState();
        });

        $selectAll.on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.js-media-select-item').prop('checked', isChecked);
            updateBulkState();
        });

        $bulkDeleteBtn.on('click', function() {
            var $checkedItems = $('.js-media-select-item:checked');
            var count = $checkedItems.length;
            if (count === 0) return;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus ' + count + ' File Media?',
                    text: 'Anda akan menghapus ' + count + ' file. Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Hapus (' + count + ')',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        submitBulkDelete($checkedItems);
                    }
                });
            } else {
                if (confirm('Anda akan menghapus ' + count + ' file. Tindakan ini tidak dapat dibatalkan.')) {
                    submitBulkDelete($checkedItems);
                }
            }
        });

        function submitBulkDelete($checkedItems) {
            var bulkUrl = '<?php echo base_url("admin/media/bulk_delete"); ?>';
            var cName   = $('meta[name="csrf-token-name"]').attr('content') || '<?php echo csrf_token(); ?>';
            var cVal    = $('meta[name="csrf-token-value"]').attr('content') || '<?php echo csrf_hash(); ?>';

            var ids = [];
            $checkedItems.each(function() {
                ids.push($(this).val());
            });

            var postData = { media_ids: ids };
            if (cName && cVal) {
                postData[cName] = cVal;
            }

            $.ajax({
                url: bulkUrl,
                type: 'POST',
                data: postData,
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    if (res && res.status) {
                        EduAlert.success('Berhasil', res.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 600);
                    } else {
                        EduAlert.error('Gagal', (res && res.error) ? res.error : (res && res.message ? res.message : 'Gagal menghapus file.'));
                    }
                },
                error: function(xhr) {
                    // NOTE: this used to fall back to submitting a plain
                    // HTML <form> POST when the AJAX call failed. That
                    // fallback is what actually crashed the page: on a
                    // CSRF failure, CI4's CSRF filter tries to build a
                    // "redirect back" response, which throws
                    // (RedirectException: no redirect address) for a
                    // request with no real browser navigation history —
                    // exactly what a JS-built <form> is. Just show the
                    // real error instead of retrying with a request that's
                    // guaranteed to crash the same way.
                    var msg = 'Gagal menghapus file.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    } else if (xhr && xhr.status === 403) {
                        msg = 'Sesi keamanan (CSRF) sudah tidak valid. Silakan muat ulang halaman ini lalu coba lagi.';
                    } else if (xhr && xhr.status) {
                        msg = 'HTTP ' + xhr.status + ': Gagal menghapus file.';
                    }
                    EduAlert.error('Gagal', msg);
                }
            });
        }



        $browseBtn.on('click', function() {
            $fileInput.trigger('click');
        });

        $fileInput.on('change', function() {
            uploadFiles(this.files);
            $fileInput.val('');
        });

        // Highlight on drag over, restore on leave/drop
        $dropzone.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $dropzone.css({
                'border-color': '#3b82f6',
                'background-color': '#EEF4FF'
            });
        });

        $dropzone.on('dragleave dragend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $dropzone.css({
                'border-color': '#ced4da',
                'background-color': ''
            });
        });

        $dropzone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $dropzone.css({
                'border-color': '#ced4da',
                'background-color': ''
            });
            var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : [];
            if (files && files.length) {
                uploadFiles(files);
            }
        });

        // Prevent the browser from navigating to the dropped file if the
        // user's cursor slips outside the dropzone itself.
        $(document).on('dragover drop', function(e) {
            e.preventDefault();
        });

        function uploadFiles(fileList) {
            $.each(fileList, function(i, file) {
                uploadSingleFile(file);
            });
        }

        function uploadSingleFile(file) {
            var rowId = 'upload-row-' + Date.now() + '-' + Math.floor(Math.random() * 10000);

            var $row = $(
                '<div class="mb-2" id="' + rowId + '">' +
                    '<div class="d-flex justify-content-between small mb-1">' +
                        '<span class="text-truncate mr-2">' + escapeHtml(file.name) + '</span>' +
                        '<span class="upload-percent text-muted">0%</span>' +
                    '</div>' +
                    '<div class="progress" style="height:6px;">' +
                        '<div class="progress-bar bg-indigo" role="progressbar" style="width:0%"></div>' +
                    '</div>' +
                '</div>'
            );
            $progressList.append($row);

            var formData = new FormData();
            formData.append('file', file);
            if (csrfName && csrfValue) {
                formData.append(csrfName, csrfValue);
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', uploadUrl, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    var pct = Math.round((e.loaded / e.total) * 100);
                    $row.find('.progress-bar').css('width', pct + '%');
                    $row.find('.upload-percent').text(pct + '%');
                }
            };

            xhr.onload = function() {
                if (window.EduCMS_CSRF) { window.EduCMS_CSRF.updateFromXhr(xhr); }
                var json = null;
                try {
                    json = JSON.parse(xhr.responseText);
                } catch (err) {
                    json = null;
                }

                if (xhr.status >= 200 && xhr.status < 300 && json && json.status) {
                    $row.find('.progress-bar').css('width', '100%').removeClass('bg-indigo').addClass('bg-success');
                    $row.find('.upload-percent').text('100%');
                    addMediaCard(json);
                    setTimeout(function() {
                        $row.fadeOut(200, function() { $(this).remove(); });
                    }, 800);
                } else {
                    var message = (json && json.error) ? json.error : 'Gagal mengunggah file.';
                    $row.find('.progress-bar').removeClass('bg-indigo').addClass('bg-danger');
                    EduAlert.error('Upload Gagal', escapeHtml(file.name) + ': ' + message);
                    setTimeout(function() {
                        $row.fadeOut(200, function() { $(this).remove(); });
                    }, 1200);
                }
            };

            xhr.onerror = function() {
                $row.find('.progress-bar').removeClass('bg-indigo').addClass('bg-danger');
                EduAlert.error('Upload Gagal', 'Terjadi kesalahan jaringan saat mengunggah ' + escapeHtml(file.name) + '.');
                setTimeout(function() {
                    $row.fadeOut(200, function() { $(this).remove(); });
                }, 1200);
            };

            xhr.send(formData);
        }

        function addMediaCard(item) {
            $emptyState.addClass('d-none');
            $grid.show();

            var mediaBody = item.is_image
                ? '<img src="' + item.url + '" alt="' + escapeHtml(item.filename) + '" style="max-height:100px;max-width:100%;object-fit:cover;">'
                : '<i class="fa-solid fa-file fa-3x text-secondary"></i>';

            var checkboxHtml = item.id ? 
                '<div class="position-absolute" style="top:6px;left:6px;z-index:10;">' +
                    '<div class="custom-control custom-checkbox">' +
                        '<input type="checkbox" class="custom-control-input js-media-select-item" id="media-item-' + item.id + '" value="' + item.id + '">' +
                        '<label class="custom-control-label" for="media-item-' + item.id + '" style="cursor:pointer;"></label>' +
                    '</div>' +
                '</div>' : '';

            var $card = $(
                '<div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3 media-item">' +
                    '<div class="card border shadow-sm h-100 position-relative media-card-wrapper">' +
                        checkboxHtml +
                        '<div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:100px;overflow:hidden;">' +
                            mediaBody +
                        '</div>' +
                        '<div class="card-body p-2">' +
                            '<p class="small text-truncate mb-1" title="' + escapeHtml(item.filename) + '">' + escapeHtml(item.filename) + '</p>' +
                            '<div class="d-flex justify-content-between align-items-center">' +
                                '<a href="' + item.url + '" target="_blank" class="btn btn-xs btn-info" title="Lihat"><i class="fa-solid fa-eye"></i></a>' +
                                '<button type="button" class="btn btn-xs btn-danger js-confirm-force-delete" data-force-delete-url="' + item.delete_url + '" title="Hapus"><i class="fa-solid fa-trash"></i></button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
            $grid.prepend($card);
            updateBulkState();
        }

        function escapeHtml(str) {
            return $('<div>').text(str == null ? '' : str).html();
        }
    });
</script>


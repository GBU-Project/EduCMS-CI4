<!--
    Shared Image Picker modal — RC06-004C Unified Media Picker.

    Loaded ONCE globally from admin/layouts/footer.php (not per-view), so
    every image field across all modules (Posts, Pages, Sliders, Achievements,
    Extracurriculars, Staff, Teachers, Admin Settings) reuses this single modal
    with 2 seamless actions:
    1. Pilih dari Media Library (Browse central media assets)
    2. Upload Baru (Upload new file direct into Media Library & auto-select)
-->
<div class="modal fade" id="imagePickerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0 bg-light">
                <ul class="nav nav-tabs border-bottom-0" id="mediaPickerTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold text-dark" id="picker-browse-tab" data-toggle="tab" href="#picker-browse-pane" role="tab">
                            <?php echo function_exists('render_icon') ? render_icon('images', 'mr-1 text-indigo') : ''; ?> Pilih dari Media Library
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold text-dark" id="picker-upload-tab" data-toggle="tab" href="#picker-upload-pane" role="tab">
                            <?php echo function_exists('render_icon') ? render_icon('cloud-arrow-up', 'mr-1 text-indigo') : ''; ?> Upload Baru
                        </a>
                    </li>
                </ul>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <div class="tab-content">
                    <!-- Tab 1: Browse Media Library -->
                    <div class="tab-pane fade show active" id="picker-browse-pane" role="tabpanel">
                        <input type="text" id="imagePickerSearch" class="form-control mb-3" placeholder="Cari nama file media...">
                        <div id="imagePickerStatus" class="text-center text-muted small py-4">Memuat gambar...</div>
                        <div class="row" id="imagePickerGrid" style="max-height:380px;overflow-y:auto;"></div>
                    </div>
                    <!-- Tab 2: Upload New -->
                    <div class="tab-pane fade" id="picker-upload-pane" role="tabpanel">
                        <div id="picker-dropzone" class="text-center p-5 mb-3" style="border:2px dashed #ced4da;border-radius:.5rem;cursor:pointer;background-color:#f8fafc;transition:all 0.2s ease;">
                            <?php echo function_exists('render_icon') ? render_icon('cloud-arrow-up', 'fa-3x text-indigo mb-3') : ''; ?>
                            <h6 class="font-weight-bold mb-1">Drag &amp; Drop File di sini untuk Mengunggah</h6>
                            <p class="text-muted small mb-3">File akan otomatis disimpan ke Media Library terpusat</p>
                            <button type="button" class="btn btn-indigo btn-sm" id="picker-dropzone-browse">
                                <?php echo function_exists('render_icon') ? render_icon('folder-open', 'mr-1') : ''; ?> Pilih File dari Komputer
                            </button>
                            <input type="file" id="picker-dropzone-input" class="d-none" accept="image/*">
                        </div>
                        <div id="picker-upload-progress" class="d-none mt-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span id="picker-upload-filename" class="font-weight-semibold text-dark">Mengunggah...</span>
                                <span id="picker-upload-percent" class="text-muted">0%</span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div id="picker-upload-bar" class="progress-bar bg-indigo progress-bar-striped progress-bar-animated" role="progressbar" style="width:0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a href="<?php echo base_url('admin/media'); ?>" target="_blank" class="text-muted small mr-auto">
                    <?php echo function_exists('render_icon') ? render_icon('external-link', 'mr-1') : ''; ?> Kelola Media Library &rarr;
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
(function ($) {
    'use strict';

    if (typeof $ === 'undefined') {
        return;
    }

    var pickerState = { targetHidden: null, targetPreview: null, items: null };
    var pickerUrl   = '<?php echo base_url('admin/media/picker'); ?>';
    var uploadUrl   = '<?php echo base_url('admin/media/drop_upload'); ?>';
    var csrfName    = $('meta[name="csrf-token-name"]').attr('content');
    var csrfValue   = $('meta[name="csrf-token-value"]').attr('content');

    function renderStatus(message) {
        $('#imagePickerStatus').text(message).show();
        $('#imagePickerGrid').empty();
    }

    function renderGrid(items) {
        var $grid = $('#imagePickerGrid');
        $grid.empty();

        if (!items || !items.length) {
            renderStatus('Tidak ada gambar ditemukan. Unggah lewat tab "Upload Baru" atau halaman Media Library.');
            return;
        }

        $('#imagePickerStatus').hide();

        items.forEach(function (item) {
            var $col = $('<div class="col-6 col-sm-4 col-md-3 mb-3"></div>');
            var $card = $('<div class="card border h-100 shadow-sm" style="cursor:pointer;transition:transform 0.15s ease;" role="button" tabindex="0"></div>');
            var $imgWrap = $('<div class="d-flex align-items-center justify-content-center bg-light" style="height:90px;overflow:hidden;"></div>');
            var $img = $('<img class="img-fluid" style="max-height:90px;object-fit:cover;">').attr('src', item.url).attr('alt', item.filename || '');
            var $body = $('<div class="card-body p-2"></div>');
            var $name = $('<p class="small text-truncate mb-0" title=""></p>').text(item.filename || '').attr('title', item.filename || '');

            $imgWrap.append($img);
            $body.append($name);
            $card.append($imgWrap).append($body);
            $col.append($card);

            $card.on('mouseover', function() { $(this).css('transform', 'scale(1.03)'); });
            $card.on('mouseout', function() { $(this).css('transform', 'scale(1)'); });

            $card.on('click keypress', function (e) {
                if (e.type === 'keypress' && e.which !== 13) return;
                selectImage(item);
            });

            $grid.append($col);
        });
    }

    function selectImage(item) {
        if (pickerState.targetHidden) {
            $('#' + pickerState.targetHidden).val(item.path);
        }
        if (pickerState.targetPreview) {
            var $preview = $('#' + pickerState.targetPreview);
            $preview.attr('src', item.url);
            $preview.closest('div.mb-2').show();
            // Handle preview wrap container if available
            $('#' + pickerState.targetPreview + '-wrap').show();
        }
        $('#imagePickerModal').modal('hide');
    }

    function loadImages(search) {
        renderStatus('Memuat gambar...');
        $.ajax({
            url: pickerUrl,
            method: 'GET',
            dataType: 'json',
            data: search ? { q: search } : {},
            success: function (response) {
                if (!response || response.status !== true) {
                    renderStatus('Gagal memuat Media Library.');
                    return;
                }
                pickerState.items = response.items || [];
                renderGrid(pickerState.items);
            },
            error: function (xhr) {
                if (xhr.status === 403) {
                    renderStatus('Anda tidak memiliki akses ke Media Library.');
                } else {
                    renderStatus('Gagal memuat Media Library. Coba lagi.');
                }
            }
        });
    }

    // Modal Trigger
    $(document).on('click', '.js-image-picker-trigger', function () {
        pickerState.targetHidden = $(this).data('target-hidden');
        pickerState.targetPreview = $(this).data('target-preview');
        // Reset to first tab
        $('#mediaPickerTabs a:first').tab('show');
        $('#imagePickerModal').modal('show');
        loadImages($('#imagePickerSearch').val());
    });

    // Search filter
    var searchTimer = null;
    $(document).on('input', '#imagePickerSearch', function () {
        var value = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () { loadImages(value); }, 350);
    });

    // =========================================================
    // Tab 2: Direct Upload to Media Library (RC06-004C)
    // Single Source of Truth — automatically saves file into media
    // library and selects it instantly for the calling form field.
    // =========================================================
    var $dropzone    = $('#picker-dropzone');
    var $browseBtn   = $('#picker-dropzone-browse');
    var $fileInput   = $('#picker-dropzone-input');
    var $progress    = $('#picker-upload-progress');
    var $progressBar = $('#picker-upload-bar');
    var $filename    = $('#picker-upload-filename');
    var $percent     = $('#picker-upload-percent');

    $browseBtn.on('click', function(e) {
        e.stopPropagation();
        $fileInput.trigger('click');
    });

    $dropzone.on('click', function() {
        $fileInput.trigger('click');
    });

    $fileInput.on('change', function() {
        if (this.files && this.files.length) {
            uploadPickerFile(this.files[0]);
            $fileInput.val('');
        }
    });

    $dropzone.on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $dropzone.css({ 'border-color': '#3b82f6', 'background-color': '#eef4ff' });
    });

    $dropzone.on('dragleave dragend drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $dropzone.css({ 'border-color': '#ced4da', 'background-color': '#f8fafc' });
    });

    $dropzone.on('drop', function(e) {
        var files = e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : [];
        if (files && files.length) {
            uploadPickerFile(files[0]);
        }
    });

    function uploadPickerFile(file) {
        $filename.text(file.name);
        $percent.text('0%');
        $progressBar.css('width', '0%').removeClass('bg-danger bg-success').addClass('bg-indigo');
        $progress.removeClass('d-none');

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
                $progressBar.css('width', pct + '%');
                $percent.text(pct + '%');
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
                $progressBar.css('width', '100%').removeClass('bg-indigo').addClass('bg-success');
                $percent.text('100%');

                setTimeout(function() {
                    $progress.addClass('d-none');
                    // Auto select newly uploaded asset
                    selectImage({
                        id: json.id,
                        path: json.path || ('uploads/media/' + json.filename),
                        url: json.url,
                        filename: json.filename
                    });
                    if (typeof EduAlert !== 'undefined') {
                        EduAlert.success('Berhasil', 'Gambar berhasil diunggah dan disimpan ke Media Library.');
                    }
                }, 400);
            } else {
                var errorMsg = (json && json.error) ? json.error : 'Gagal mengunggah file.';
                $progressBar.removeClass('bg-indigo').addClass('bg-danger');
                $filename.text('Upload Gagal: ' + errorMsg);
            }
        };

        xhr.onerror = function() {
            $progressBar.removeClass('bg-indigo').addClass('bg-danger');
            $filename.text('Upload Gagal: Kesalahan jaringan.');
        };

        xhr.send(formData);
    }

})(window.jQuery);
</script>

/**
 * EduCMS Admin UI Foundation Core JS
 * Handles Theme Manager, DataTable Wrappers, SweetAlert2 Integrations, and Loaders
 */

$(document).ready(function() {
    // Initialize Theme Manager on load
    initThemeManager();
    // Initialize TinyMCE Rich Text Editor
    initRichTextEditor();
});

// =========================================================
// CSRF token refresh (audit finding #6 — csrf_regenerate)
// -----------------------------------------------------------------------
// The server regenerates the CSRF token on every request once
// csrf_regenerate is enabled. Since the CSRF cookie is HttpOnly (cannot
// be read from JS), the server instead echoes the current token back via
// the X-CSRF-Token-Name / X-CSRF-Token-Value response headers (see
// MY_Controller). Every AJAX call — jQuery or raw XHR — should refresh
// the <meta> tags from those headers so the *next* submission in the
// same page uses a valid token.
// =========================================================
window.EduCMS_CSRF = {
    update: function (name, value) {
        if (name && value) {
            $('meta[name="csrf-token-name"]').attr('content', name);
            $('meta[name="csrf-token-value"]').attr('content', value);
        }
    },
    updateFromXhr: function (xhr) {
        try {
            var name = xhr.getResponseHeader('X-CSRF-Token-Name');
            var value = xhr.getResponseHeader('X-CSRF-Token-Value');
            EduCMS_CSRF.update(name, value);
        } catch (e) { /* older browsers / cross-origin: ignore */ }
    }
};

$(document).ajaxComplete(function (event, xhr) {
    EduCMS_CSRF.updateFromXhr(xhr);
});

// =========================================================
// Rich Text Editor Initialization (TinyMCE)
// =========================================================
function initRichTextEditor() {
    if (typeof tinymce === 'undefined') {
        return;
    }

    const baseUrl = (window.EduCMS_Config && window.EduCMS_Config.baseUrl) ? window.EduCMS_Config.baseUrl : '/';

    // Check if the current theme is dark
    const isDark = $('body').hasClass('dark-mode');

    tinymce.init({
        selector: 'textarea[name="content"], textarea.js-rich-editor',
        height: 500,
        branding: false,
        promotion: false,
        menubar: 'edit insert view format table tools help',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'codesample', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote removeformat | link image media table hr | codesample code fullscreen preview',
        
        // AJAX Image Upload integration (reusing upload_media helper)
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', baseUrl + 'admin/media/editor_upload');

                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = function() {
                    var json;

                    EduCMS_CSRF.updateFromXhr(xhr);

                    if (xhr.status === 403) {
                        reject('HTTP Error: ' + xhr.status + ' (Forbidden)', { remove: true });
                        return;
                    }

                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }

                    try {
                        json = JSON.parse(xhr.responseText);
                    } catch (err) {
                        reject('Respon server tidak valid');
                        return;
                    }

                    if (!json || typeof json.location != 'string') {
                        reject('Path lokasi gambar tidak ditemukan');
                        return;
                    }

                    resolve(json.location);
                };

                xhr.onerror = function () {
                    reject('Gagal mengunggah gambar karena kesalahan jaringan. Kode: ' + xhr.status);
                };

                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                var csrfValue = $('meta[name="csrf-token-value"]').attr('content');
                if (csrfName && csrfValue) {
                    formData.append(csrfName, csrfValue);
                }

                xhr.send(formData);
            });
        },
        
        // Paste from Word/Google Docs cleanup filter
        paste_as_text: false,
        paste_data_images: true,
        paste_enable_default_filters: true,
        paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,h5,h6,p,ul,ol,li,table,thead,tbody,tr,th,td,a[href]",
        invalid_styles: 'color font-family font-size line-height background background-color border margin padding',
        
        // Dark Mode editor support (skins/content)
        skin: isDark ? 'oxide-dark' : 'oxide',
        content_css: isDark ? 'dark' : 'default',
        
        // Responsive CSS for editor iframe content
        content_style: 'body { font-family: Outfit, sans-serif; font-size: 14px; } img { max-width: 100%; height: auto; } iframe { max-width: 100%; } table { width: 100%; border-collapse: collapse; }'
    });
}

// =========================================================
// 1. Theme Manager (Light, Dark, Auto)
// =========================================================
function initThemeManager() {
    const dbTheme = (window.EduCMS_Config && window.EduCMS_Config.defaultTheme) ? window.EduCMS_Config.defaultTheme : 'auto';
    const savedTheme = localStorage.getItem('educms_admin_theme') || dbTheme || 'auto';
    applyTheme(savedTheme);

    // Listen to changes in system theme preferences
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (localStorage.getItem('educms_admin_theme') === 'auto') {
            applyTheme('auto');
        }
    });
}

function applyTheme(theme) {
    localStorage.setItem('educms_admin_theme', theme);
    
    let isDark = theme === 'dark';
    if (theme === 'auto') {
        isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    const body = $('body');
    const navbar = $('.main-header');

    if (isDark) {
        body.addClass('dark-mode');
        navbar.removeClass('navbar-white navbar-light').addClass('navbar-dark bg-dark');
        $('.theme-icon').removeClass('fa-sun fa-desktop').addClass('fa-moon');
    } else {
        body.removeClass('dark-mode');
        navbar.removeClass('navbar-dark bg-dark').addClass('navbar-white navbar-light');
        $('.theme-icon').removeClass('fa-moon fa-desktop').addClass('fa-sun');
    }
}

// Global theme toggle trigger
function cycleTheme() {
    const themes = ['light', 'dark', 'auto'];
    const currentTheme = localStorage.getItem('educms_admin_theme') || 'auto';
    let nextIndex = (themes.indexOf(currentTheme) + 1) % themes.length;
    const nextTheme = themes[nextIndex];

    applyTheme(nextTheme);
    
    // Display short toast notify of change
    let themeNames = { 'light': 'Light Mode', 'dark': 'Dark Mode', 'auto': 'System Preference (Auto)' };
    showToast('info', 'Tema diganti ke: ' + themeNames[nextTheme]);
}

// =========================================================
// 2. Reusable DataTable Wrapper
// =========================================================
function initDataTable(selector, options = {}) {
    const defaultOptions = {
        responsive: true,
        autoWidth: false,
        order: [], // Default no ordering on init
        language: {
            "processing":   "Sedang memproses...",
            "lengthMenu":   "Tampilkan _MENU_ entri",
            "zeroRecords":  "Tidak ditemukan data yang sesuai",
            "info":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
            "search":       "Cari:",
            "paginate": {
                "first":    "Pertama",
                "previous": "<i class='fa-solid fa-chevron-left'></i>",
                "next":     "<i class='fa-solid fa-chevron-right'></i>",
                "last":     "Terakhir"
            }
        }
    };

    const finalOptions = $.extend(true, {}, defaultOptions, options);
    return $(selector).DataTable(finalOptions);
}

// =========================================================
// 3. SweetAlert2 & Toast Wrappers
// =========================================================
// SweetAlert2 Toast configuration
const SwalToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

function showToast(icon, message) {
    SwalToast.fire({
        icon: icon, // 'success', 'error', 'warning', 'info', 'question'
        title: message
    });
}

function showAlert(type, title, message) {
    Swal.fire({
        icon: type,
        title: title,
        html: message,
        confirmButtonColor: '#6366f1'
    });
}

function showConfirm(title, text, confirmCallback) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            confirmCallback();
        }
    });
}

// =========================================================
// 4. Loading Overlay Helpers
// =========================================================
function showLoading(containerSelector) {
    const container = $(containerSelector);
    if (container.find('.loading-overlay').length > 0) return;

    container.addClass('loading-overlay-wrapper');
    const overlayHtml = `
        <div class="loading-overlay">
            <div class="loading-spinner"></div>
            <span class="text-muted text-sm font-weight-medium">Memuat data...</span>
        </div>
    `;
    container.append(overlayHtml);
}

function hideLoading(containerSelector) {
    const container = $(containerSelector);
    container.find('.loading-overlay').fadeOut(300, function() {
        $(this).remove();
        container.removeClass('loading-overlay-wrapper');
    });
}

// =========================================================
// 5. EduTable & EduAlert Wrappers (Audit Compliance)
// =========================================================
const EduTable = {
    init: function(selector, options = {}) {
        return initDataTable(selector, options);
    }
};

const EduAlert = {
    success: function(title, message) {
        showAlert('success', title, message);
    },
    error: function(title, message) {
        showAlert('error', title, message);
    },
    confirm: function(title, text, confirmCallback) {
        showConfirm(title, text, confirmCallback);
    },
    toast: function(icon, message) {
        showToast(icon, message);
    }
};

// =========================================================
// 6. CRUD Foundation Delete/Restore Confirmation (Global)
// =========================================================
// Single reusable handler for every module's list view (Pages, Categories,
// Tags, Posts, and any future CRUD module). Buttons carry the target URL
// via data attributes (see crud_helper.php::render_action_buttons), so no
// per-module confirmDelete()/confirmForceDelete() JS needs to be redeclared.
$(document).on('click', '.js-confirm-delete', function(e) {
    e.preventDefault();
    const url = $(this).data('delete-url');
    EduAlert.confirm('Pindahkan ke Sampah?', 'Data ini akan dipindahkan ke tempat sampah sementara.', function() {
        window.location.href = url;
    });
});

$(document).on('click', '.js-confirm-force-delete', function(e) {
    e.preventDefault();
    const url = $(this).data('force-delete-url');
    EduAlert.confirm('Hapus Permanen?', 'Data ini akan dihapus secara permanen dari database.', function() {
        window.location.href = url;
    });
});

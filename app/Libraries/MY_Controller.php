<?php

/**
 * EduCMS legacy controller base classes (port from application/core/MY_Controller.php).
 * Defined in the global namespace so the namespaced App\Controllers\* classes can
 * extend them via `use \Admin_Controller;` etc. The compat layer (CI_Controller,
 * CI_Loader, CI_DB, CI_Session, ...) provides the CI3 API these rely on.
 */
#[AllowDynamicProperties]
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load core session and template libraries if not autoloaded
        if (!isset($this->session)) {
            $this->load->library('session');
        }
        if (!isset($this->template)) {
            $this->load->library('template');
        }

        // SECURITY (audit finding #6): with csrf_regenerate enabled the
        // token changes on every POST, which would otherwise break repeat
        // AJAX calls in the same page (e.g. multiple TinyMCE image
        // uploads). CSRF verification already ran and regenerated the
        // hash by the time this constructor executes, so exposing the
        // *current* token here via response headers lets client-side JS
        // refresh its copy after every request instead of relying on the
        // token rendered at initial page load.
        if (config_item('csrf_protection')) {
            $this->output->set_header('X-CSRF-Token-Name: ' . $this->security->get_csrf_token_name());
            $this->output->set_header('X-CSRF-Token-Value: ' . $this->security->get_csrf_hash());
        }
    }
}

#[AllowDynamicProperties]
class Portal_Controller extends MY_Controller {

    public $site_settings = array();
    public $header_menu = array();
    public $footer_menu = array();

    public function __construct() {
        parent::__construct();

        // Ensure Setting_model is loaded
        $this->load->model('setting_model');
        $this->load->model('menu_model');
        $this->load->helper('menu');

        // Fetch settings configured for autoloading
        $this->site_settings = $this->setting_model->get_autoloaded();

        // Build the site-wide navigation menus from the Menu Builder (database),
        // so no controller/view ever needs to hardcode a nav link.
        if ($this->db->table_exists('menus') && $this->db->table_exists('menu_groups')) {
            $this->header_menu = $this->menu_model->get_tree_by_group_slug('header');
            $this->footer_menu = $this->menu_model->get_tree_by_group_slug('footer');
        }

        // Expose settings and menus to views globally
        $this->load->vars(array(
            'site_settings' => $this->site_settings,
            'header_menu'   => $this->header_menu,
            'footer_menu'   => $this->footer_menu,
            'current_path'  => '/' . trim($this->uri->uri_string(), '/')
        ));
    }
    /**
     * Load SEO metadata for a content record and shape it for the
     * portal header partial (title/description/keywords/OG/canonical).
     * $page_name matches the key Admin_CRUD_Controller saves under:
     * "{perm_prefix}_{id}" (e.g. "posts_12", "achievements_4").
     * Always returns an array (possibly all-empty) so the header partial
     * never has to guard against a missing variable.
     */
    protected function load_seo($page_name, $fallback_description = '') {
        $this->load->model('seo_model');
        $seo = $this->seo_model->get_seo($page_name);

        return array(
            'meta_title'       => $seo->meta_title ?? '',
            'meta_description' => $seo->meta_description ?? $fallback_description,
            'keywords'         => $seo->keywords ?? '',
            'og_title'         => $seo->og_title ?? '',
            'og_description'   => $seo->og_description ?? '',
            'og_image'         => $seo->og_image ?? '',
            'canonical_url'    => $seo->canonical_url ?? ''
        );
    }
}

#[AllowDynamicProperties]
class Admin_Controller extends MY_Controller {

    protected $user_id = NULL;

    public function __construct() {
        parent::__construct();

        // Load authentication library
        $this->load->library('auth_lib', NULL, 'auth');

        // 1. Session Authentication Check
        if (!$this->auth->is_logged_in()) {
            // Save the URL they were trying to access to redirect them back after login
            if ($this->input->method() === 'get' && !$this->input->is_ajax_request()) {
                $this->session->set_userdata('redirect_to', current_url());
            }
            redirect('admin/login');
        }

        $this->user_id = $this->session->userdata('user_id');

        // Load authorization library
        $this->load->library('rbac');

        // Load settings model for system-wide configuration
        $this->load->model('setting_model');
        $site_settings = $this->setting_model->get_autoloaded();

        // Detect pending database migrations automatically on every admin
        // request (Catatan 3: "Tanpa intervensi user") so an outdated
        // database is surfaced immediately instead of silently causing
        // errors elsewhere in the panel.
        $this->load->library('db_upgrade');
        $pending_migrations_count = 0;
        try {
            $pending_migrations_count = count($this->db_upgrade->get_pending_migrations());
        } catch (Exception $e) {
            // Never let a migration-detection failure block the admin panel itself.
            $pending_migrations_count = 0;
        }

        // Expose current authenticated user details to all admin views
        $this->load->vars(array(
            'current_user' => $this->auth->get_user($this->user_id),
            'user_permissions' => $this->rbac->get_user_permissions($this->user_id),
            'site_settings' => $site_settings,
            'pending_migrations_count' => $pending_migrations_count
        ));
    }

    /**
     * RBAC Permission Enforcement Middleware
     */
    protected function check_permission($permission) {
        if (!$this->rbac->has_permission($this->user_id, $permission)) {
            // If ajax, return json, otherwise show standard forbidden page
            if ($this->input->is_ajax_request()) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_status_header(403)
                     ->set_output(json_encode(array('error' => 'Forbidden access.')));
                $this->output->_display();
                exit();
            } else {
                show_error('Anda tidak memiliki hak akses untuk halaman ini.', 403, 'Akses Ditolak (Forbidden)');
            }
        }
    }

    /**
     * Unified Activity Logging Helper
     */
    protected function log_activity($module, $action, $old_value = NULL, $new_value = NULL) {
        $this->load->library('logger');
        $this->logger->log(
            $this->user_id,
            $module,
            $action,
            $old_value,
            $new_value
        );
    }
}

#[AllowDynamicProperties]
class Admin_CRUD_Controller extends Admin_Controller {

    protected $model_name = '';
    protected $view_dir = '';
    protected $route_prefix = '';
    protected $title_singular = '';
    protected $title_plural = '';
    protected $perm_prefix = '';

    // Engine configurations
    protected $validation_rules = array();
    protected $upload_fields = array(); // array('field_name' => 'subfolder')
    protected $has_seo = FALSE;
    protected $has_slug = FALSE;
    protected $slug_source_field = 'title';

    public function __construct() {
        parent::__construct();
        if (!empty($this->model_name)) {
            $this->load->model($this->model_name);
        }
        $this->load->helper('crud');
        $this->load->model('seo_model');
    }

    /**
     * Reusable list renderer with active/trash toggle support.
     *
     * Search, sorting, and pagination for these lists are handled entirely
     * client-side by EduTable (DataTables) — see educms-admin.js. The
     * Foundation intentionally does not run a parallel server-side
     * pagination layer here; that previously existed alongside EduTable
     * and was never wired into any view, which meant rows beyond the
     * server's page size were unreachable. Standardized on a single
     * source of truth per the v1.x decision (see CHANGELOG).
     */
    public function index() {
        $this->check_permission($this->perm_prefix . '.view');
        $show_trash = (bool) $this->input->get('trash');

        $where = array();
        if ($show_trash) {
            $where[$this->{$this->model_name}->table . '.deleted_at !='] = NULL;
        }

        // Fetch all matching rows; EduTable applies search, sort, and paging client-side.
        $data['list'] = $this->{$this->model_name}->get_paged($where, '', array(), NULL, NULL, NULL);

        $data['show_trash'] = $show_trash;
        $data['title'] = $this->title_plural . ($show_trash ? ' (Tempat Sampah)' : '');
        $data['breadcrumbs'] = array($this->title_plural => $this->route_prefix, ($show_trash ? 'Sampah' : 'Daftar') => '');

        $this->template->load_admin($this->view_dir . '/index', $data);
    }

    /**
     * Create record
     */
    public function create() {
        $this->check_permission($this->perm_prefix . '.manage');

        $this->load->library('form_validation');
        if (!empty($this->validation_rules)) {
            $this->form_validation->set_rules($this->validation_rules);
        }

        if ($this->form_validation->run() === TRUE) {
            $data = $this->_get_post_data();

            // Handle Slugs
            if ($this->has_slug) {
                $slug_val = $this->input->post('slug');
                if (empty($slug_val)) {
                    $slug_val = $this->input->post($this->slug_source_field);
                }
                $data['slug'] = generate_unique_slug($slug_val, $this->model_name);
            }

            // Handle Uploads
            $this->load->helper('upload');
            foreach ($this->upload_fields as $field => $subfolder) {
                if (isset($_FILES[$field]) && !empty($_FILES[$field]['name'])) {
                    $upload = upload_media($field, $subfolder);
                    if ($upload['status']) {
                        $data[$field] = $upload['file_path'];
                    } else {
                        $this->session->set_flashdata('error', 'Gagal upload ' . $field . ': ' . $upload['error']);
                        $this->_render_create_view();
                        return;
                    }
                }
            }

            // Additional system fields
            if ($this->db->field_exists('author_id', $this->{$this->model_name}->table)) {
                $data['author_id'] = $this->user_id;
            }
            if ($this->db->field_exists('created_at', $this->{$this->model_name}->table)) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            // Hook before insert
            $data = $this->_before_insert($data);

            $insert_id = $this->{$this->model_name}->insert($data);

            // Save SEO
            if ($this->has_seo) {
                $seo_data = array(
                    'meta_title' => $this->input->post('meta_title'),
                    'meta_description' => $this->input->post('meta_description'),
                    'keywords' => $this->input->post('meta_keywords')
                );
                $this->seo_model->save_seo($this->perm_prefix . '_' . $insert_id, $seo_data);
            }

            // Hook after insert
            $this->_after_insert($insert_id, $data);

            $this->log_activity($this->title_singular, 'create', NULL, json_encode(array('id' => $insert_id) + $data));
            $this->session->set_flashdata('success', $this->title_singular . ' berhasil dibuat.');
            redirect($this->route_prefix);
        }

        $this->_render_create_view();
    }

    /**
     * Edit record
     */
    public function edit($id) {
        $this->check_permission($this->perm_prefix . '.manage');

        $row = $this->{$this->model_name}->get_by('id', $id);
        if (!$row) {
            show_404();
        }

        $this->load->library('form_validation');
        if (!empty($this->validation_rules)) {
            $this->form_validation->set_rules($this->validation_rules);
        }

        if ($this->form_validation->run() === TRUE) {
            $data = $this->_get_post_data();

            // Handle Slugs
            if ($this->has_slug) {
                $slug_val = $this->input->post('slug');
                if (empty($slug_val)) {
                    $slug_val = $this->input->post($this->slug_source_field);
                }
                $data['slug'] = generate_unique_slug($slug_val, $this->model_name, $id);
            }

            // Handle Uploads
            $this->load->helper('upload');
            foreach ($this->upload_fields as $field => $subfolder) {
                if (isset($_FILES[$field]) && !empty($_FILES[$field]['name'])) {
                    $upload = upload_media($field, $subfolder);
                    if ($upload['status']) {
                        $data[$field] = $upload['file_path'];
                    } else {
                        $this->session->set_flashdata('error', 'Gagal upload ' . $field . ': ' . $upload['error']);
                        $this->_render_edit_view($id, $row);
                        return;
                    }
                }
            }

            if ($this->db->field_exists('updated_at', $this->{$this->model_name}->table)) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }

            // Hook before update
            $data = $this->_before_update($id, $data);

            $this->{$this->model_name}->update($id, $data);

            // Save SEO
            if ($this->has_seo) {
                $seo_data = array(
                    'meta_title' => $this->input->post('meta_title'),
                    'meta_description' => $this->input->post('meta_description'),
                    'keywords' => $this->input->post('meta_keywords')
                );
                $this->seo_model->save_seo($this->perm_prefix . '_' . $id, $seo_data);
            }

            // Hook after update
            $this->_after_update($id, $data);

            $this->log_activity($this->title_singular, 'update', json_encode($row), json_encode($data));
            $this->session->set_flashdata('success', $this->title_singular . ' berhasil diperbarui.');
            redirect($this->route_prefix);
        }

        $this->_render_edit_view($id, $row);
    }

    /**
     * Soft delete a record
     */
    public function delete($id) {
        $this->check_permission($this->perm_prefix . '.manage');
        $row = $this->{$this->model_name}->get_by('id', $id);
        if (!$row) {
            show_404();
        }

        $this->{$this->model_name}->delete($id, $this->user_id);
        $this->log_activity($this->title_singular, 'delete', json_encode($row), NULL);

        if ($this->input->is_ajax_request()) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => TRUE)));
        } else {
            $this->session->set_flashdata('success', $this->title_singular . ' berhasil dipindahkan ke tempat sampah.');
            redirect($this->route_prefix);
        }
    }

    /**
     * Restore a soft-deleted record
     */
    public function restore($id) {
        $this->check_permission($this->perm_prefix . '.manage');
        $rows = $this->{$this->model_name}->get_with_trashed(array($this->{$this->model_name}->table . '.id' => $id));
        if (empty($rows)) {
            show_404();
        }
        $row = $rows[0];

        $this->{$this->model_name}->restore($id);
        $this->log_activity($this->title_singular, 'restore', NULL, json_encode($row));

        $this->session->set_flashdata('success', $this->title_singular . ' berhasil dipulihkan.');
        redirect($this->route_prefix . '?trash=1');
    }

    /**
     * Force delete a record permanently
     */
    public function force_delete($id) {
        $this->check_permission($this->perm_prefix . '.manage');

        // Permanent, irreversible destruction is Super-Admin-only regardless
        // of who holds {module}.manage (e.g. the Editor role) — see
        // CONTENT_MODULE_STANDARD.md §5.
        $this->load->library('rbac');
        if (!$this->rbac->is_super_admin($this->user_id)) {
            if ($this->input->is_ajax_request()) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_status_header(403)
                     ->set_output(json_encode(array('error' => 'Hanya Super Admin yang dapat menghapus data secara permanen.')));
                $this->output->_display();
                exit();
            }
            show_error('Hanya Super Admin yang dapat menghapus data secara permanen.', 403, 'Akses Ditolak (Forbidden)');
        }

        $rows = $this->{$this->model_name}->get_with_trashed(array($this->{$this->model_name}->table . '.id' => $id));
        if (empty($rows)) {
            show_404();
        }
        $row = $rows[0];

        // Permanent delete SEO if applicable
        if ($this->has_seo) {
            $seo = $this->seo_model->get_seo($this->perm_prefix . '_' . $id);
            if ($seo) {
                $this->seo_model->force_delete($seo->id);
            }
        }

        $this->{$this->model_name}->force_delete($id);
        $this->log_activity($this->title_singular, 'force_delete', json_encode($row), NULL);

        $this->session->set_flashdata('success', $this->title_singular . ' berhasil dihapus secara permanen.');
        redirect($this->route_prefix . '?trash=1');
    }

    // Helper functions / hooks
    protected function _get_post_data() {
        $fields = $this->db->list_fields($this->{$this->model_name}->table);
        $data = array();
        $exclude = array('id', 'created_at', 'updated_at', 'deleted_at', 'deleted_by');
        // SECURITY (audit finding #3): these fields hold raw WYSIWYG HTML
        // that is echoed unescaped on the public site, so it must be
        // stripped of any script/event-handler content before it is ever
        // persisted, regardless of which CRUD controller (Posts, Pages,
        // Announcements, ...) is saving it.
        $wysiwyg_fields = array('content');
        foreach ($fields as $field) {
            if (in_array($field, $exclude)) continue;
            if ($this->input->post($field) !== NULL) {
                $value = $this->input->post($field, FALSE); // raw; sanitized below if applicable
                if (in_array($field, $wysiwyg_fields, TRUE)) {
                    $this->load->helper('sanitize');
                    $value = sanitize_html($value);
                }
                $data[$field] = $value;
            }
        }
        return $data;
    }

    protected function _before_insert($data) {
        return $data;
    }

    protected function _after_insert($id, $data) {
    }

    protected function _before_update($id, $data) {
        return $data;
    }

    protected function _after_update($id, $data) {
    }

    protected function _render_create_view() {
        $data['title'] = 'Buat ' . $this->title_singular . ' Baru';
        $data['breadcrumbs'] = array($this->title_plural => $this->route_prefix, 'Buat' => '');
        if (isset($this->templates)) {
            $data['templates'] = $this->templates;
        }
        $this->template->load_admin($this->view_dir . '/create', $data);
    }

    protected function _render_edit_view($id, $row) {
        $data['row'] = $row;
        $data['page'] = $row; // backward compatibility
        $data['category'] = $row;
        $data['tag'] = $row;
        $data['post'] = $row;

        $data['title'] = 'Edit ' . $this->title_singular;
        $data['breadcrumbs'] = array($this->title_plural => $this->route_prefix, 'Edit' => '');
        if (isset($this->templates)) {
            $data['templates'] = $this->templates;
        }
        if ($this->has_seo) {
            $data['seo'] = $this->seo_model->get_seo($this->perm_prefix . '_' . $id);
        }
        $this->template->load_admin($this->view_dir . '/edit', $data);
    }
}

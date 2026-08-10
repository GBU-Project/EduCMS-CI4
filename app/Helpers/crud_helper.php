<?php
/**
 * EduCMS CRUD Helper
 * Centralizes slug generation, reusable list actions, and alert/toast integrations.
 */

if (!function_exists('generate_unique_slug')) {
    /**
     * Generate unique slug for any database table
     */
    function generate_unique_slug($title, $model_name, $exclude_id = NULL, $field_name = 'slug') {
        $CI = get_instance();
        $CI->load->helper('educms_helper');
        $slug = slugify($title);
        
        $original_slug = $slug;
        $i = 1;
        while (TRUE) {
            $CI->db->where($field_name, $slug);
            if ($exclude_id !== NULL) {
                $CI->db->where('id !=', $exclude_id);
            }
            $count = $CI->db->count_all_results($CI->$model_name->table);
            if ($count === 0) {
                break;
            }
            $slug = $original_slug . '-' . $i;
            $i++;
        }
        return $slug;
    }
}

if (!function_exists('render_action_buttons')) {
    /**
     * Standardized Action Buttons renderer for lists (reusable action buttons)
     */
    function render_action_buttons($id, $slug, $route_prefix, $show_trash = FALSE, $preview_url = '') {
        $output = '';
        if ($show_trash) {
            $restore_url = base_url($route_prefix . '/restore/' . $id);
            $force_delete_url = base_url($route_prefix . '/force_delete/' . $id);
            $output .= '<a href="' . $restore_url . '" class="btn btn-xs btn-success mr-1" title="Pulihkan">' . render_icon('trash-arrow-up') . '</a>';
            $output .= '<button type="button" class="btn btn-xs btn-danger js-confirm-force-delete" data-force-delete-url="' . $force_delete_url . '" title="Hapus Permanen">' . render_icon('ban') . '</button>';
        } else {
            if (!empty($preview_url)) {
                $output .= '<a href="' . base_url($preview_url) . '" target="_blank" class="btn btn-xs btn-info mr-1" title="Pratinjau">' . render_icon('eye') . '</a>';
            }
            $edit_url = base_url($route_prefix . '/edit/' . $id);
            $delete_url = base_url($route_prefix . '/delete/' . $id);
            $output .= '<a href="' . $edit_url . '" class="btn btn-xs btn-indigo mr-1" title="Edit">' . render_icon('edit') . '</a>';
            $output .= '<button type="button" class="btn btn-xs btn-danger js-confirm-delete" data-delete-url="' . $delete_url . '" title="Hapus">' . render_icon('trash') . '</button>';
        }
        return $output;
    }
}

if (!function_exists('render_flash_messages')) {
    /**
     * Unified Flash Messages script renderer using HTML Alert Banner + SweetAlert2 toasts
     */
    function render_flash_messages() {
        $CI = get_instance();
        $output = '';
        $icon_map = array('success' => 'circle-check', 'error' => 'circle-xmark', 'warning' => 'triangle-exclamation', 'info' => 'circle-info');
        
        foreach (array('success', 'error', 'info', 'warning') as $type) {
            $msg = $CI->session->flashdata($type);
            if ($msg) {
                $bs_class = ($type === 'error') ? 'danger' : $type;
                $icon_name = isset($icon_map[$type]) ? $icon_map[$type] : 'circle-info';
                
                // 1. Visible HTML Alert Banner
                $output .= '
                <div class="alert alert-' . $bs_class . ' alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius:12px;font-weight:500;">
                    <i class="fa-solid fa-' . $icon_name . ' mr-2 fa-lg"></i>
                    <span>' . nl2br(esc_html($msg)) . '</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';

                // 2. SweetAlert2 Toast via Vanilla JS DOMContentLoaded Listener
                $msg_js = json_encode($msg);
                $output .= "
                <script>
                    (function() {
                        function fireToast() {
                            if (typeof EduAlert !== 'undefined' && typeof EduAlert.toast === 'function') {
                                EduAlert.toast('{$type}', {$msg_js});
                            }
                        }
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', fireToast);
                        } else {
                            window.addEventListener('load', fireToast);
                        }
                    })();
                </script>";
            }
        }
        return $output;
    }
}

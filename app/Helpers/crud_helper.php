<?php
/**
 * EduCMS CRUD Helper (Native CI4)
 * Centralizes slug generation, reusable list actions, and alert/toast integrations.
 */

if (! function_exists('generate_unique_slug')) {
    /**
     * Generate unique slug for any database table
     */
    function generate_unique_slug($title, $tableName, $excludeId = null, $fieldName = 'slug')
    {
        helper('educms_helper');
        $slug = slugify($title);

        $db           = \Config\Database::connect();
        $originalSlug = $slug;
        $i            = 1;

        while (true) {
            $builder = $db->table($tableName)->where($fieldName, $slug);
            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }
            $count = $builder->countAllResults();
            if ($count === 0) {
                break;
            }
            $slug = $originalSlug . '-' . $i;
            $i++;
        }
        return $slug;
    }
}

if (! function_exists('render_action_buttons')) {
    /**
     * Standardized Action Buttons renderer for lists
     */
    function render_action_buttons($id, $slug, $routePrefix, $showTrash = false, $previewUrl = '')
    {
        $output = '';
        if ($showTrash) {
            $restoreUrl    = base_url($routePrefix . '/restore/' . $id);
            $forceDeleteUrl = base_url($routePrefix . '/force_delete/' . $id);
            $output        .= '<a href="' . $restoreUrl . '" class="btn btn-xs btn-success mr-1" title="Pulihkan">' . render_icon('trash-arrow-up') . '</a>';
            $output        .= '<button type="button" class="btn btn-xs btn-danger js-confirm-force-delete" data-force-delete-url="' . $forceDeleteUrl . '" title="Hapus Permanen">' . render_icon('ban') . '</button>';
        } else {
            if (! empty($previewUrl)) {
                $output .= '<a href="' . base_url($previewUrl) . '" target="_blank" class="btn btn-xs btn-info mr-1" title="Pratinjau">' . render_icon('eye') . '</a>';
            }
            $editUrl   = base_url($routePrefix . '/edit/' . $id);
            $deleteUrl = base_url($routePrefix . '/delete/' . $id);
            $output   .= '<a href="' . $editUrl . '" class="btn btn-xs btn-indigo mr-1" title="Edit">' . render_icon('edit') . '</a>';
            $output   .= '<button type="button" class="btn btn-xs btn-danger js-confirm-delete" data-delete-url="' . $deleteUrl . '" title="Hapus">' . render_icon('trash') . '</button>';
        }
        return $output;
    }
}

if (! function_exists('render_flash_messages')) {
    /**
     * Unified Flash Messages script renderer using HTML Alert Banner + SweetAlert2 toasts (Native CI4)
     */
    function render_flash_messages()
    {
        $output  = '';
        $iconMap = ['success' => 'circle-check', 'error' => 'circle-xmark', 'warning' => 'triangle-exclamation', 'info' => 'circle-info'];

        foreach (['success', 'error', 'info', 'warning'] as $type) {
            $msg = session()->getFlashdata($type);
            if ($msg) {
                $bsClass  = ($type === 'error') ? 'danger' : $type;
                $iconName = isset($iconMap[$type]) ? $iconMap[$type] : 'circle-info';

                $output .= '
                <div class="alert alert-' . $bsClass . ' alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius:12px;font-weight:500;">
                    <i class="fa-solid fa-' . $iconName . ' mr-2 fa-lg"></i>
                    <span>' . nl2br(esc_html((string) $msg)) . '</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';

                $msgJs   = json_encode($msg);
                $output .= "
                <script>
                    (function() {
                        function fireToast() {
                            if (typeof EduAlert !== 'undefined' && typeof EduAlert.toast === 'function') {
                                EduAlert.toast('{$type}', {$msgJs});
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

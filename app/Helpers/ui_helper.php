<?php
/**
 * EduCMS Admin UI Foundation Reusable Components Helper
 * Implements centralized rendering for cards, empty states, and form inputs
 */

// =========================================================
// 1. Card Component (EduCard)
// =========================================================
if (!function_exists('educard_start')) {
    /**
     * Start a standard premium card wrapper
     */
    function educard_start($title, $icon = '', $type = 'premium') {
        $icon_html = !empty($icon) ? render_icon($icon, 'text-indigo mr-2') : '';
        $card_class = ($type === 'premium') ? 'card-premium shadow-sm' : 'card';
        return '
        <div class="card ' . $card_class . '">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">' . $icon_html . esc_html($title) . '</h5>
            </div>
            <div class="card-body">';
    }
}

if (!function_exists('educard_end')) {
    /**
     * End a card wrapper
     */
    function educard_end() {
        return '
            </div>
        </div>';
    }
}

// =========================================================
// 2. Empty State Component
// =========================================================
if (!function_exists('eduempty_state')) {
    /**
     * Render a standardized empty state alert with an optional action button
     */
    function eduempty_state($title, $description, $icon = 'folder-open', $button_text = '', $button_url = '', $extra_classes = '') {
        $button_html = '';
        if (!empty($button_text) && !empty($button_url)) {
            $button_html = '<a href="' . base_url($button_url) . '" class="btn btn-sm btn-indigo"><i class="fa-solid fa-plus mr-1"></i>' . esc_html($button_text) . '</a>';
        }
        
        return '
        <div class="empty-state ' . esc_attr($extra_classes) . '">
            <div class="empty-state-icon">
                ' . render_icon($icon, 'fa-2x') . '
            </div>
            <h6 class="empty-state-title">' . esc_html($title) . '</h6>
            <p class="empty-state-description">' . esc_html($description) . '</p>
            ' . $button_html . '
        </div>';
    }
}

// =========================================================
// 3. Form Components (EduForm)
// =========================================================
if (!function_exists('eduform_input')) {
    /**
     * Render a text/email/password standard form control
     */
    function eduform_input($name, $label, $value = '', $type = 'text', $options = array()) {
        $readonly = isset($options['readonly']) && $options['readonly'] ? ' readonly' : '';
        $disabled = isset($options['disabled']) && $options['disabled'] ? ' disabled' : '';
        $placeholder = isset($options['placeholder']) ? esc_attr($options['placeholder']) : '';
        $required = isset($options['required']) && $options['required'] ? ' required' : '';
        $help_text = isset($options['help']) ? '<span class="text-muted text-xs d-block mt-1">' . esc_html($options['help']) . '</span>' : '';
        
        $error_class = '';
        $error_feedback = '';
        
        return '
        <div class="form-group mb-3">
            <label for="' . esc_attr($name) . '" class="form-label font-weight-semibold">' . esc_html($label) . '</label>
            <input type="' . esc_attr($type) . '" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" class="form-control' . $error_class . '" value="' . esc_attr($value) . '" placeholder="' . $placeholder . '"' . $readonly . $disabled . $required . '>
            ' . $error_feedback . '
            ' . $help_text . '
        </div>';
    }
}

if (!function_exists('eduform_textarea')) {
    /**
     * Render a standard multiline form control
     */
    function eduform_textarea($name, $label, $value = '', $options = array()) {
        $readonly = isset($options['readonly']) && $options['readonly'] ? ' readonly' : '';
        $disabled = isset($options['disabled']) && $options['disabled'] ? ' disabled' : '';
        $placeholder = isset($options['placeholder']) ? esc_attr($options['placeholder']) : '';
        $rows = isset($options['rows']) ? (int)$options['rows'] : 4;
        $required = isset($options['required']) && $options['required'] ? ' required' : '';
        $help_text = isset($options['help']) ? '<span class="text-muted text-xs d-block mt-1">' . esc_html($options['help']) . '</span>' : '';
        
        $error_class = '';
        $error_feedback = '';
        
        return '
        <div class="form-group mb-3">
            <label for="' . esc_attr($name) . '" class="form-label font-weight-semibold">' . esc_html($label) . '</label>
            <textarea name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" rows="' . $rows . '" class="form-control' . $error_class . '" placeholder="' . $placeholder . '"' . $readonly . $disabled . $required . '>' . esc_html($value) . '</textarea>
            ' . $error_feedback . '
            ' . $help_text . '
        </div>';
    }
}

if (!function_exists('eduform_select')) {
    /**
     * Render a standard dropdown select control
     */
    function eduform_select($name, $label, $options_list = array(), $selected_value = '', $options = array()) {
        $disabled = isset($options['disabled']) && $options['disabled'] ? ' disabled' : '';
        $required = isset($options['required']) && $options['required'] ? ' required' : '';
        $help_text = isset($options['help']) ? '<span class="text-muted text-xs d-block mt-1">' . esc_html($options['help']) . '</span>' : '';
        
        $error_class = '';
        $error_feedback = '';
        
        $options_html = '';
        foreach ($options_list as $val => $text) {
            $sel = ($val == $selected_value) ? ' selected' : '';
            $options_html .= '<option value="' . esc_attr($val) . '"' . $sel . '>' . esc_html($text) . '</option>';
        }
        
        return '
        <div class="form-group mb-3">
            <label for="' . esc_attr($name) . '" class="form-label font-weight-semibold">' . esc_html($label) . '</label>
            <select name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" class="form-control custom-select' . $error_class . '"' . $disabled . $required . '>
                ' . $options_html . '
            </select>
            ' . $error_feedback . '
            ' . $help_text . '
        </div>';
    }
}

if (!function_exists('eduform_checkbox')) {
    /**
     * Render a standard checkbox switch control
     */
    function eduform_checkbox($name, $label, $checked = FALSE, $options = array()) {
        $disabled = isset($options['disabled']) && $options['disabled'] ? ' disabled' : '';
        $check_attr = $checked ? ' checked' : '';
        $value = isset($options['value']) ? esc_attr($options['value']) : '1';
        
        return '
        <div class="custom-control custom-checkbox mb-2">
            <input type="checkbox" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" class="custom-control-input" value="' . $value . '"' . $check_attr . $disabled . '>
            <label class="custom-control-label text-secondary small" for="' . esc_attr($name) . '">' . esc_html($label) . '</label>
        </div>';
    }
}

if (!function_exists('eduform_file')) {
    /**
     * Render a standard file/image upload control with optional current-file
     * preview, plus (RC4 Blueprint v1.2, TASK 15) a "Pilih dari Media" button
     * that opens the shared Image Picker modal (application/views/admin/
     * components/image_picker.php, loaded once from admin/layouts/footer.php)
     * for image-type fields.
     *
     * Backward compatibility, by design rather than by special-casing:
     * the hidden input added below shares the SAME `name` attribute as the
     * <input type="file">. A browser submitting multipart/form-data sends
     * file inputs to $_FILES and every other input (including a hidden one)
     * to $_POST — never both from the same name — so:
     *   - Choosing a NEW file via the classic file input still populates
     *     $_FILES[$name] exactly as before; Admin_CRUD_Controller's upload
     *     handling (which only ever reads $_FILES, never this hidden input)
     *     is completely untouched and takes priority, same as pre-RC4.
     *   - Picking an image from the library instead sets the hidden input's
     *     value via JS; $_FILES[$name] stays empty, so _get_post_data()
     *     (which reads $_POST for every real table column, including $name)
     *     picks up the chosen path automatically. No controller changes
     *     were needed in any of the 7 modules using this helper.
     *   - Touching neither control on Edit leaves the hidden input at its
     *     pre-filled $current_path value, so the existing image is kept —
     *     identical to today's actual (accidental) behavior where an
     *     untouched file input just leaves the column alone.
     */
    function eduform_file($name, $label, $current_path = '', $options = array()) {
        $required = isset($options['required']) && $options['required'] ? ' required' : '';
        $accept = isset($options['accept']) ? ' accept="' . esc_attr($options['accept']) . '"' : ' accept="image/*"';
        $help_text = isset($options['help']) ? '<span class="text-muted text-xs d-block mt-1">' . esc_html($options['help']) . '</span>' : '';

        $error_class = '';
        $error_feedback = '';

        // Picker only makes sense for image fields — Media_model::get_images()
        // only ever returns image/* rows, so a non-image field (accept
        // explicitly overridden to something else) gets no picker button.
        $is_image_field = (strpos($accept, 'image') !== FALSE);

        $safe_id = preg_replace('/[^a-zA-Z0-9_-]/', '-', $name);
        $preview_id = $safe_id . '__picker-preview';
        $hidden_id = $safe_id . '__picker-value';

        $preview_html = '';
        if (!empty($current_path)) {
            $preview_html = '
            <div class="mb-2">
                <img src="' . base_url($current_path) . '" alt="' . esc_attr($label) . '" id="' . esc_attr($preview_id) . '" class="img-thumbnail" style="max-height:120px;">
            </div>';
        } elseif ($is_image_field) {
            // Present-but-hidden placeholder so the picker's JS always has an
            // <img> to reveal + point at, even when there is no current file
            // yet (e.g. Create forms) — avoids building a whole <div> in JS.
            $preview_html = '
            <div class="mb-2" id="' . esc_attr($preview_id) . '-wrap" style="display:none;">
                <img src="" alt="' . esc_attr($label) . '" id="' . esc_attr($preview_id) . '" class="img-thumbnail" style="max-height:120px;">
            </div>';
        }

        $picker_button = '';
        $hidden_input = '';
        if ($is_image_field) {
            $hidden_input = '<input type="hidden" name="' . esc_attr($name) . '" id="' . esc_attr($hidden_id) . '" value="' . esc_attr($current_path) . '">';
            $picker_button = '
            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 js-image-picker-trigger"
                data-target-hidden="' . esc_attr($hidden_id) . '"
                data-target-preview="' . esc_attr($preview_id) . '">' .
                (function_exists('render_icon') ? render_icon('images', 'mr-1') : '') . 'Pilih dari Media
            </button>';
        }

        return '
        <div class="form-group mb-3">
            <label for="' . esc_attr($name) . '" class="form-label font-weight-semibold">' . esc_html($label) . '</label>
            ' . $preview_html . '
            ' . $hidden_input . '
            <input type="file" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" class="form-control-file' . $error_class . '"' . $accept . $required . '>
            ' . $picker_button . '
            ' . $error_feedback . '
            ' . $help_text . '
        </div>';
    }
}

if (!function_exists('eduform_checkbox_group')) {
    /**
     * Render a standardized multi-select checkbox pill group (used for Category/Tag selectors)
     */
    function eduform_checkbox_group($name, $label, $items, $selected_ids = array(), $options = array()) {
        $help_text = isset($options['help']) ? '<span class="text-muted text-xs d-block mt-1">' . esc_html($options['help']) . '</span>' : '';
        $selected_ids = array_map('strval', (array) $selected_ids);

        $items_html = '';
        if (empty($items)) {
            $items_html = '<p class="text-muted small mb-0">Belum ada data tersedia.</p>';
        } else {
            foreach ($items as $item) {
                $checked = in_array((string) $item->id, $selected_ids, TRUE) ? ' checked' : '';
                $items_html .= '
                <div class="custom-control custom-checkbox custom-control-inline mb-2">
                    <input type="checkbox" name="' . esc_attr($name) . '[]" id="' . esc_attr($name . '_' . $item->id) . '" class="custom-control-input" value="' . (int) $item->id . '"' . $checked . '>
                    <label class="custom-control-label small" for="' . esc_attr($name . '_' . $item->id) . '">' . esc_html($item->name) . '</label>
                </div>';
            }
        }

        return '
        <div class="form-group mb-3">
            <label class="form-label font-weight-semibold">' . esc_html($label) . '</label>
            <div class="border rounded p-2">' . $items_html . '</div>
            ' . $help_text . '
        </div>';
    }
}

if (!function_exists('eduform_radio')) {
    /**
     * Render a standard radio button control
     */
    function eduform_radio($name, $id, $label, $checked = FALSE, $options = array()) {
        $disabled = isset($options['disabled']) && $options['disabled'] ? ' disabled' : '';
        $check_attr = $checked ? ' checked' : '';
        $value = isset($options['value']) ? esc_attr($options['value']) : $id;
        
        return '
        <div class="custom-control custom-radio mb-2">
            <input type="radio" name="' . esc_attr($name) . '" id="' . esc_attr($id) . '" class="custom-control-input" value="' . $value . '"' . $check_attr . $disabled . '>
            <label class="custom-control-label text-secondary small" for="' . esc_attr($id) . '">' . esc_html($label) . '</label>
        </div>';
    }
}

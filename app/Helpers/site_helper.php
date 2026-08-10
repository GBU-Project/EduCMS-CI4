<?php
/**
 * Site branding helper — RC4 Blueprint v1.2, TASK 7.
 *
 * Single source of truth for site identity (name, logo, favicon,
 * tagline, contact info) so views never read $site_settings arrays
 * directly for branding. Every function here goes through the existing
 * get_setting() helper (settings_helper.php, already autoloaded), which
 * itself falls back to a direct DB query when $CI->site_settings isn't
 * populated yet (e.g. the login screen — Auth extends MY_Controller
 * directly, not Portal_Controller/Admin_Controller, so it never
 * pre-loads site_settings). That fallback already existed in the
 * Settings library, so these helpers work everywhere with no changes
 * needed to Auth.php or MY_Controller (no architecture change).
 *
 * Every function has a safe fallback so a fresh/empty settings table
 * never produces an empty page title, a broken <img>, or a PHP notice.
 */

if (!function_exists('site_name')) {
    /**
     * The site's display name. Source of truth: general.site_name
     * (per Blueprint v1.2 TASK 6 — not school.name, and NOT the
     * school.school_name key some older views mistakenly checked,
     * which was never actually seeded and always silently fell
     * through to a hardcoded string).
     */
    function site_name($default = 'EduCMS') {
        $value = get_setting('general', 'site_name', $default);
        return ($value !== NULL && trim((string) $value) !== '') ? $value : $default;
    }
}

if (!function_exists('site_tagline')) {
    function site_tagline($default = 'Portal Resmi Sekolah') {
        $value = get_setting('general', 'site_tagline', $default);
        return ($value !== NULL && trim((string) $value) !== '') ? $value : $default;
    }
}

if (!function_exists('site_logo')) {
    /**
     * Absolute URL to the site logo, or NULL if not configured (or the
     * configured file doesn't actually exist on disk — a "safe
     * fallback" also has to protect against a broken <img> when a site
     * has the setting row but never uploaded the file). Callers should
     * fall back to the existing icon-based brand mark when this
     * returns NULL, exactly as they already do today.
     */
    function site_logo() {
        $value = get_setting('general', 'site_logo', '');
        if (empty($value)) {
            return NULL;
        }
        $relative = ltrim($value, '/');
        if (!file_exists(FCPATH . $relative)) {
            return NULL;
        }
        return base_url($relative);
    }
}

if (!function_exists('site_favicon')) {
    /**
     * Absolute URL to the favicon, or NULL if not configured / the file
     * is missing on disk (same safe-fallback reasoning as site_logo()).
     */
    function site_favicon() {
        $value = get_setting('general', 'site_favicon', '');
        if (empty($value)) {
            return NULL;
        }
        $relative = ltrim($value, '/');
        if (!file_exists(FCPATH . $relative)) {
            return NULL;
        }
        return base_url($relative);
    }
}

if (!function_exists('site_address')) {
    function site_address($default = '') {
        $value = get_setting('school', 'address', $default);
        return ($value !== NULL && trim((string) $value) !== '') ? $value : $default;
    }
}

if (!function_exists('site_phone')) {
    function site_phone($default = '') {
        $value = get_setting('school', 'phone', $default);
        return ($value !== NULL && trim((string) $value) !== '') ? $value : $default;
    }
}

if (!function_exists('site_email')) {
    function site_email($default = '') {
        $value = get_setting('school', 'email', $default);
        return ($value !== NULL && trim((string) $value) !== '') ? $value : $default;
    }
}

if (!function_exists('site_wa_url')) {
    /**
     * Get normalized WhatsApp URL from Website Settings (school phone) with pre-filled message text.
     * Returns NULL if phone is empty, landline, or invalid mobile number.
     * If message is empty, returns clean wa.me URL without ?text= parameter.
     *
     * @param string|null $phone_input    Optional raw phone string (defaults to site_phone('') if empty)
     * @param string|null $custom_message Optional custom message text (defaults to school.wa_default_message)
     * @return string|null Normalized wa.me URL with pre-filled text parameter if message exists
     */
    function site_wa_url($phone_input = NULL, $custom_message = NULL) {
        $raw_phone = ($phone_input !== NULL && trim((string)$phone_input) !== '') ? $phone_input : site_phone('');
        if ($raw_phone === NULL || trim((string)$raw_phone) === '') {
            return NULL;
        }

        $raw_phone = trim((string)$raw_phone);

        // If stored string is already a wa.me or whatsapp URL, extract digits
        if (preg_match('#wa\.me/([0-9]+)#i', $raw_phone, $matches)) {
            $digits = $matches[1];
        } else {
            // Clean non-digits
            $digits = preg_replace('/[^0-9]/', '', $raw_phone);
        }

        if (empty($digits)) {
            return NULL;
        }

        // Convert leading 08... to 628...
        if (substr($digits, 0, 2) === '08') {
            $digits = '628' . substr($digits, 2);
        } elseif (substr($digits, 0, 1) === '8' && strlen($digits) >= 9 && strlen($digits) <= 12) {
            // Convert leading 8... (e.g. 8123456789) to 628...
            $digits = '62' . $digits;
        } elseif (substr($digits, 0, 1) === '0') {
            // Reject any landline starting with 0 that is not 08 (e.g. 021, 022, 031, 061)
            return NULL;
        }

        // Length validation: valid mobile numbers are between 10 and 15 digits
        $len = strlen($digits);
        if ($len < 10 || $len > 15) {
            return NULL;
        }

        // If Indonesian number (starts with 62), must start with 628 (mobile operator), excluding landlines (6221, 6222, etc.)
        if (substr($digits, 0, 2) === '62' && substr($digits, 0, 3) !== '628') {
            return NULL;
        }

        $url = 'https://wa.me/' . $digits;

        // Resolve pre-filled message parameter
        $message = $custom_message;
        if ($message === NULL) {
            $message = get_setting('school', 'wa_default_message', 'Halo Admin Sekolah, saya ingin bertanya informasi seputar sekolah.');
        }

        $clean_msg = trim((string)$message);
        if ($clean_msg !== '') {
            // Prevent double-encoding by decoding any existing percent-encodings first
            if (strpos($clean_msg, '%') !== false) {
                $clean_msg = rawurldecode($clean_msg);
            }
            // Normalize CRLF to LF for clean WhatsApp multiline rendering
            $clean_msg = str_replace("\r\n", "\n", $clean_msg);
            $url .= '?text=' . rawurlencode($clean_msg);
        }

        return $url;
    }
}





if (!function_exists('site_footer')) {
    /**
     * Ready-to-echo footer copyright line. Text kept identical to the
     * RC3 wording (only the source of the name changed) so this isn't
     * treated as a UI copy change beyond what Blueprint v1.2 asked for.
     */
    function site_footer() {
        return '&copy; ' . date('Y') . ' ' . esc_html(site_name()) . '. All rights reserved.';
    }
}

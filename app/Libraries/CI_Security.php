<?php

/**
 * CI3-compatible Security wrapper (CSRF token accessors + helpers).
 */
#[AllowDynamicProperties]
class CI_Security
{
    public function get_csrf_token_name()
    {
        return config('Security')->tokenName;
    }

    public function get_csrf_hash()
    {
        return csrf_hash();
    }

    public function xss_clean($str)
    {
        if (is_array($str)) {
            return array_map([$this, 'xss_clean'], $str);
        }

        return htmlspecialchars(strip_tags((string) $str), ENT_QUOTES, 'UTF-8');
    }

    public function sanitize_filename($str, $relative_path = false)
    {
        return str_replace(['../', './', '..\\'], '', (string) $str);
    }

    public function csrf_set_cookie()
    {
        return true;
    }
}

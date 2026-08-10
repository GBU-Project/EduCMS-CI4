<?php
if (!function_exists('get_setting')) {
    /**
     * Retrieve setting keys in a single function call.
     *
     * Bugfix: this used to load a library literally named "Settings" —
     * but application/controllers/admin/Settings.php ALSO declares
     * `class Settings`. CI3 has no namespaces, so the two collided: on
     * any request that touched the Settings admin controller, CI's
     * loader saw a class named "Settings" already existed (the
     * controller) and mis-wired $CI->settings to it instead of the real
     * library, cascading into unrelated-looking errors (e.g. a bogus
     * "Session.php" load failure) specifically on /admin/settings.
     * Renamed the library to Site_settings so the class name is unique.
     */
    function get_setting($group, $key, $default = NULL) {
        $CI = get_instance();
        
        // Ensure Settings library is loaded
        if (!isset($CI->site_settings_lib)) {
            $CI->load->library('site_settings', NULL, 'site_settings_lib');
        }
        
        return $CI->site_settings_lib->get($group, $key, $default);
    }
}

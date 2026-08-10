<?php
#[AllowDynamicProperties]
class Site_settings {

    protected $CI;

    public function __construct() {
        $this->CI = get_instance();
    }

    /**
     * Get a setting value by group name and key, with optional default fallback
     */
    public function get($group, $key, $default = NULL) {
        // Try to read from the memory-cached registry in active controller
        $CI = get_instance();
        if (isset($CI->site_settings) && isset($CI->site_settings[$group][$key])) {
            return $CI->site_settings[$group][$key];
        }

        // Fall back to direct database query if not in cache
        if ($this->CI->db->table_exists('settings')) {
            $row = $this->CI->db->select('value')
                                ->from('settings')
                                ->where('group_name', $group)
                                ->where('key', $key)
                                ->where('deleted_at', NULL)
                                ->get()
                                ->row();
            if ($row !== NULL) {
                return $row->value;
            }
        }
        return $default;
    }
}

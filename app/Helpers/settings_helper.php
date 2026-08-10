<?php

if (! function_exists('get_setting')) {
    /**
     * Retrieve setting keys in a single function call (Native CI4).
     */
    function get_setting($group, $key, $default = null)
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('settings')) {
            return $default;
        }

        $row = $db->table('settings')
            ->where('group_name', $group)
            ->where('key', $key)
            ->get()
            ->getRow();

        return $row ? $row->value : $default;
    }
}

if (! function_exists('site_name')) {
    function site_name(): string
    {
        return (string) get_setting('general', 'site_name', 'EduCMS');
    }
}

if (! function_exists('site_favicon')) {
    function site_favicon(): string
    {
        return (string) get_setting('general', 'site_favicon', '');
    }
}

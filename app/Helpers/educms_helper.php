<?php
if (!function_exists('esc_html')) {
    /**
     * timing-attack safe HTML output escaping
     */
    function esc_html($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    /**
     * HTML attribute output escaping
     */
    function esc_attr($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('slugify')) {
    /**
     * Translate string to URL-safe slug
     */
    function slugify($str) {
        $str = preg_replace('~[^\pL\d]+~u', '-', $str);
        $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);
        $str = preg_replace('~[^-\w]+~', '', $str);
        $str = trim($str, '-');
        $str = preg_replace('~-+~', '-', $str);
        $str = strtolower($str);
        if (empty($str)) {
            return 'n-a';
        }
        return $str;
    }
}

if (!function_exists('format_date_id')) {
    /**
     * Translate DateTime string to Indonesian Date Format
     */
    function format_date_id($datetime, $include_time = FALSE) {
        if (empty($datetime)) {
            return '-';
        }
        
        $timestamp = strtotime($datetime);
        $days = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $months = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );

        $day = $days[date('w', $timestamp)];
        $date = date('j', $timestamp);
        $month = $months[(int)date('n', $timestamp)];
        $year = date('Y', $timestamp);

        $output = "$date $month $year";
        if ($include_time) {
            $output .= ' ' . date('H:i', $timestamp);
        }
        return $output;
    }
}

if (!function_exists('render_icon')) {
    /**
     * Reusable FontAwesome Icon Wrapper Helper
     * Wraps icon names using the standard fa-solid set
     */
    function render_icon($icon_name, $extra_classes = '', $title = '') {
        $title_attr = !empty($title) ? ' title="' . esc_attr($title) . '"' : '';
        return '<i class="fa-solid fa-' . esc_attr($icon_name) . ' ' . esc_attr($extra_classes) . '"' . $title_attr . '></i>';
    }
}

<?php
if (!function_exists('resolve_menu_url')) {
    /**
     * Resolve a menu item's stored URL into a final href.
     * - Absolute/external URLs (http://, https://, mailto:, tel:, #) pass through untouched.
     * - Anything else is treated as an internal path and run through base_url().
     */
    function resolve_menu_url($url) {
        $url = trim((string) $url);
        if ($url === '' || $url === '#') {
            return '#';
        }
        if (preg_match('#^(https?:)?//#i', $url) || preg_match('#^(mailto:|tel:)#i', $url)) {
            return $url;
        }
        return base_url(ltrim($url, '/'));
    }
}

if (!function_exists('is_external_menu_url')) {
    function is_external_menu_url($url) {
        $url = trim((string) $url);
        return (bool) preg_match('#^(https?:)?//#i', $url);
    }
}

if (!function_exists('render_menu_desktop')) {
    /**
     * Render a nested menu tree as a horizontal desktop nav with dropdowns for
     * items that have children. $active_url is the current request path, used
     * to highlight the active link.
     */
    function render_menu_desktop($tree, $active_url = '') {
        if (empty($tree)) {
            return '';
        }

        $html = '';
        foreach ($tree as $item) {
            $href = resolve_menu_url($item->url);
            $target = ($item->target === '_blank') ? ' target="_blank" rel="noopener"' : '';
            $has_children = !empty($item->children);
            $is_active = (!empty($active_url) && rtrim($item->url, '/') === rtrim($active_url, '/'));

            $link_classes = 'text-sm font-medium transition px-1 py-2 ' .
                ($is_active ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600');

            if ($has_children) {
                $is_placeholder_parent = (trim((string) $item->url) === '' || trim((string) $item->url) === '#');
                $html .= '<div class="relative group">';
                if ($is_placeholder_parent) {
                    // Parent exists only to hold a dropdown — never a real destination,
                    // so it must not render as a clickable href="#" link.
                    $html .= '<span class="' . $link_classes . ' inline-flex items-center gap-1 cursor-default select-none">';
                    $html .= esc_html($item->title);
                    $html .= '<i data-lucide="chevron-down" class="w-3.5 h-3.5"></i></span>';
                } else {
                    $html .= '<a href="' . esc_attr($href) . '"' . $target . ' class="' . $link_classes . ' inline-flex items-center gap-1">';
                    $html .= esc_html($item->title);
                    $html .= '<i data-lucide="chevron-down" class="w-3.5 h-3.5"></i></a>';
                }
                $html .= '<div class="absolute left-0 top-full mt-1 w-56 bg-white rounded-xl border border-slate-100 shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-40">';
                foreach ($item->children as $child) {
                    $child_href = resolve_menu_url($child->url);
                    $child_target = ($child->target === '_blank') ? ' target="_blank" rel="noopener"' : '';
                    $html .= '<a href="' . esc_attr($child_href) . '"' . $child_target . ' class="block px-4 py-2 text-sm text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 transition">' . esc_html($child->title) . '</a>';
                }
                $html .= '</div></div>';
            } else {
                $html .= '<a href="' . esc_attr($href) . '"' . $target . ' class="' . $link_classes . '">' . esc_html($item->title) . '</a>';
            }
        }
        return $html;
    }
}

if (!function_exists('render_menu_mobile')) {
    /**
     * Render a nested menu tree as a stacked mobile nav (simple accordion-free list;
     * child items are shown indented directly beneath their parent).
     */
    function render_menu_mobile($tree) {
        if (empty($tree)) {
            return '';
        }
        $html = '';
        foreach ($tree as $item) {
            $has_children = !empty($item->children);
            $is_placeholder_parent = $has_children && (trim((string) $item->url) === '' || trim((string) $item->url) === '#');

            if ($is_placeholder_parent) {
                $html .= '<div class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-400">' . esc_html($item->title) . '</div>';
            } else {
                $href = resolve_menu_url($item->url);
                $target = ($item->target === '_blank') ? ' target="_blank" rel="noopener"' : '';
                $html .= '<a href="' . esc_attr($href) . '"' . $target . ' class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">' . esc_html($item->title) . '</a>';
            }

            if ($has_children) {
                foreach ($item->children as $child) {
                    $child_href = resolve_menu_url($child->url);
                    $child_target = ($child->target === '_blank') ? ' target="_blank" rel="noopener"' : '';
                    $html .= '<a href="' . esc_attr($child_href) . '"' . $child_target . ' class="block pl-8 pr-4 py-2 text-sm text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition">' . esc_html($child->title) . '</a>';
                }
            }
        }
        return $html;
    }
}

if (!function_exists('render_menu_footer')) {
    /**
     * Render a flat (non-dropdown) list of links for the footer "Tautan Cepat" column.
     */
    function render_menu_footer($tree) {
        if (empty($tree)) {
            return '';
        }
        $html = '';
        foreach ($tree as $item) {
            $href = resolve_menu_url($item->url);
            $target = ($item->target === '_blank') ? ' target="_blank" rel="noopener"' : '';
            $html .= '<li><a href="' . esc_attr($href) . '"' . $target . ' class="hover:text-white transition">' . esc_html($item->title) . '</a></li>';
        }
        return $html;
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($str) {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
    }
}

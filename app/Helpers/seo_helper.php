<?php
if (!function_exists('generate_seo_tags')) {
    /**
     * Compile page HTML meta tags dynamically
     */
    function generate_seo_tags($meta = array()) {
        $CI = get_instance();
        
        // RC4 Blueprint v1.2, TASK 17 fix: this used to read the
        // `school.school_name` setting key, which is never actually
        // seeded anywhere in the app — exactly the stale key TASK 6/7's
        // site_helper.php already documented as a mistake other code
        // should not repeat. site_name() (general.site_name) is the
        // single source of truth for site identity; this function was
        // simply never updated when that became true. (Currently dead
        // code — nothing in the app calls generate_seo_tags() since RC3
        // moved SEO tag rendering directly into partials/header.php —
        // fixed anyway so it isn't a landmine for future code that does.)
        $school_name = site_name('EduCMS Sekolah');
        $default_title = get_setting('seo', 'meta_title', $school_name);
        $default_desc = get_setting('seo', 'meta_description', 'Website Portal Resmi Sekolah');
        $default_keys = get_setting('seo', 'keywords', 'sekolah, portal, cms, educms');
        
        $title = isset($meta['title']) ? $meta['title'] . ' | ' . $school_name : $default_title;
        $desc = isset($meta['description']) ? $meta['description'] : $default_desc;
        $keywords = isset($meta['keywords']) ? $meta['keywords'] : $default_keys;
        $canonical = isset($meta['canonical']) ? $meta['canonical'] : current_url();
        $og_image = isset($meta['og_image']) ? $meta['og_image'] : base_url('assets/shared/default_og.jpg');

        $html  = "\t<title>" . esc_html($title) . "</title>\n";
        $html .= "\t<meta name=\"description\" content=\"" . esc_attr($desc) . "\" />\n";
        $html .= "\t<meta name=\"keywords\" content=\"" . esc_attr($keywords) . "\" />\n";
        $html .= "\t<link rel=\"canonical\" href=\"" . esc_attr($canonical) . "\" />\n\n";

        // Open Graph Meta Tags
        $html .= "\t<meta property=\"og:type\" content=\"website\" />\n";
        $html .= "\t<meta property=\"og:title\" content=\"" . esc_attr($title) . "\" />\n";
        $html .= "\t<meta property=\"og:description\" content=\"" . esc_attr($desc) . "\" />\n";
        $html .= "\t<meta property=\"og:url\" content=\"" . esc_attr($canonical) . "\" />\n";
        $html .= "\t<meta property=\"og:site_name\" content=\"" . esc_attr($school_name) . "\" />\n";
        $html .= "\t<meta property=\"og:image\" content=\"" . esc_attr($og_image) . "\" />\n\n";

        // Twitter Card Meta Tags
        $html .= "\t<meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
        $html .= "\t<meta name=\"twitter:title\" content=\"" . esc_attr($title) . "\" />\n";
        $html .= "\t<meta name=\"twitter:description\" content=\"" . esc_attr($desc) . "\" />\n";
        $html .= "\t<meta name=\"twitter:image\" content=\"" . esc_attr($og_image) . "\" />\n";

        return $html;
    }
}

if (!function_exists('generate_breadcrumb')) {
    /**
     * Render Bootstrap 5 breadcrumbs list
     */
    function generate_breadcrumb($segments = array()) {
        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">';
        $html .= '<li class="breadcrumb-item"><a href="' . base_url() . '"><i class="fas fa-home"></i> Beranda</a></li>';
        
        $count = count($segments);
        $i = 1;
        foreach ($segments as $name => $link) {
            if ($i === $count || empty($link)) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html($name) . '</li>';
            } else {
                $html .= '<li class="breadcrumb-item"><a href="' . base_url($link) . '">' . esc_html($name) . '</a></li>';
            }
            $i++;
        }
        
        $html .= '</ol></nav>';
        return $html;
    }
}

<?php

if (! function_exists('generate_seo_tags')) {
    /**
     * Compile page HTML meta tags dynamically (Native CI4).
     */
    function generate_seo_tags($meta = [])
    {
        $schoolName  = site_name();
        $defaultTitle = get_setting('seo', 'meta_title', $schoolName);
        $defaultDesc  = get_setting('seo', 'meta_description', 'Website Portal Resmi Sekolah');
        $defaultKeys  = get_setting('seo', 'keywords', 'sekolah, portal, cms, educms');

        $title     = isset($meta['title']) ? $meta['title'] . ' | ' . $schoolName : $defaultTitle;
        $desc      = isset($meta['description']) ? $meta['description'] : $defaultDesc;
        $keywords  = isset($meta['keywords']) ? $meta['keywords'] : $defaultKeys;
        $canonical = isset($meta['canonical']) ? $meta['canonical'] : current_url();
        $ogImage   = isset($meta['og_image']) ? $meta['og_image'] : base_url('assets/shared/default_og.jpg');

        $html  = "\t<title>" . esc_html($title) . "</title>\n";
        $html .= "\t<meta name=\"description\" content=\"" . esc_attr($desc) . "\" />\n";
        $html .= "\t<meta name=\"keywords\" content=\"" . esc_attr($keywords) . "\" />\n";
        $html .= "\t<link rel=\"canonical\" href=\"" . esc_attr($canonical) . "\" />\n\n";

        // Open Graph Meta Tags
        $html .= "\t<meta property=\"og:type\" content=\"website\" />\n";
        $html .= "\t<meta property=\"og:title\" content=\"" . esc_attr($title) . "\" />\n";
        $html .= "\t<meta property=\"og:description\" content=\"" . esc_attr($desc) . "\" />\n";
        $html .= "\t<meta property=\"og:url\" content=\"" . esc_attr($canonical) . "\" />\n";
        $html .= "\t<meta property=\"og:site_name\" content=\"" . esc_attr($schoolName) . "\" />\n";
        $html .= "\t<meta property=\"og:image\" content=\"" . esc_attr($ogImage) . "\" />\n\n";

        // Twitter Card Meta Tags
        $html .= "\t<meta name=\"twitter:card\" content=\"summary_large_image\" />\n";
        $html .= "\t<meta name=\"twitter:title\" content=\"" . esc_attr($title) . "\" />\n";
        $html .= "\t<meta name=\"twitter:description\" content=\"" . esc_attr($desc) . "\" />\n";
        $html .= "\t<meta name=\"twitter:image\" content=\"" . esc_attr($ogImage) . "\" />\n";

        return $html;
    }
}

if (! function_exists('generate_breadcrumb')) {
    /**
     * Render Bootstrap 5 breadcrumbs list
     */
    function generate_breadcrumb($segments = [])
    {
        $html  = '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">';
        $html .= '<li class="breadcrumb-item"><a href="' . base_url() . '"><i class="fas fa-home"></i> Beranda</a></li>';

        $count = count($segments);
        $i     = 1;
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

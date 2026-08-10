<?php
#[AllowDynamicProperties]
class Template {

    protected $CI;

    public function __construct() {
        $this->CI = get_instance();
    }

    /**
     * Render administrative page wrapped in AdminLTE layouts
     */
    public function load_admin($view, $data = array()) {
        $this->CI->load->view('admin/layouts/header', $data);
        $this->CI->load->view('admin/layouts/sidebar', $data);
        $this->CI->load->view($view, $data);
        $this->CI->load->view('admin/layouts/footer', $data);
    }

    /**
     * Render public school pages wrapped in Portal or Active Theme layouts
     */
    public function load_portal($view, $data = array()) {
        $active_theme = get_setting('theme', 'active_theme', 'default');
        
        // Strip 'portal/' prefix if passed
        $view_name = ltrim(preg_replace('/^portal\//', '', $view), '/');
        $theme_base = FCPATH . 'themes/' . $active_theme . '/views/';

        // 1. Check if view exists in Active Theme
        if ($active_theme !== 'default' && file_exists($theme_base . $view_name . '.php')) {
            $resolved_page = '../../themes/' . $active_theme . '/views/' . $view_name;
            $this->CI->load->view($resolved_page, $data);
            return;
        }

        // 2. Fallback to Core Portal view, but resolve active theme partials if available
        $header_view = 'portal/partials/header';
        $footer_view = 'portal/partials/footer';
        if ($active_theme !== 'default' && file_exists($theme_base . 'partials/header.php')) {
            $header_view = '../../themes/' . $active_theme . '/views/partials/header';
        }
        if ($active_theme !== 'default' && file_exists($theme_base . 'partials/footer.php')) {
            $footer_view = '../../themes/' . $active_theme . '/views/partials/footer';
        }

        // Read view content and substitute standard portal header/footer inclusions
        $content = $this->CI->load->view('portal/' . $view_name, $data, TRUE);
        
        // Check if content includes portal header/footer lines and replace with active theme partials
        $has_portal_header = strpos($content, "view('portal/partials/header'") !== FALSE || strpos($content, 'view("portal/partials/header"') !== FALSE;
        $has_portal_footer = strpos($content, "view('portal/partials/footer'") !== FALSE || strpos($content, 'view("portal/partials/footer"') !== FALSE;

        if ($has_portal_header || $has_portal_footer) {
            // Render header first if the view included portal header
            if ($has_portal_header) {
                $this->CI->load->view($header_view, $data);
                // Strip the internal header include line output from view content
                $content = preg_replace('/<\?php\s+\$this->load->view\([\'"]portal\/partials\/header[\'"]\);\s*\?>/i', '', $content);
            }
            if ($has_portal_footer) {
                // Strip the internal footer include line output from view content
                $content = preg_replace('/<\?php\s+\$this->load->view\([\'"]portal\/partials\/footer[\'"]\);\s*\?>/i', '', $content);
            }
            
            // Output view body
            echo $content;

            if ($has_portal_footer) {
                $this->CI->load->view($footer_view, $data);
            }
        } else {
            // Direct output if no header/footer tags present
            echo $content;
        }
    }



}


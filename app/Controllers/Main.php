<?php

namespace App\Controllers;

use \CI_Controller;
/**
 * Main Controller
 * Handles global framework-level routes such as the custom 404 error page.
 * Referenced by $route['404_override'] in application/config/routes.php.
 */
class Main extends CI_Controller {

    public function error_404() {
        $this->output->set_status_header(404);
        $this->load->view('errors/html/error_custom_404');
    }
}

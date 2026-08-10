<?php

use CodeIgniter\Controller;

/**
 * CI3-compatible controller base. Builds the full compat object graph in
 * the constructor (request/response are pulled from the shared services,
 * so it works even before CI4 calls initController()).
 */
#[AllowDynamicProperties]
class CI_Controller extends Controller
{
    // CI4's Controller declares these as protected; legacy CI3 code assigns
    // them dynamically via the loader (e.g. $this->logger = new Logger()).
    // Widen visibility so those assignments don't hit
    // "Cannot access protected property".
    public $helpers = [];
    public $request;
    public $response;
    public $logger;
    public $validator;

    public function __construct()
    {
        Compat::set_instance($this);

        $this->load = new CI_Loader($this);
        $this->config = new CI_Config();
        $this->db = new CI_DB();
        $this->input = new CI_Input();
        $this->session = new CI_Session();
        $this->output = new CI_Output();
        $this->security = new CI_Security();
        $this->uri = new CI_URI();

        // CI3 autoload behaviors (see application/config/autoload.php).
        $this->load->library('template');
        $this->load->helper(['url', 'form', 'security', 'educms', 'settings', 'ui', 'seo', 'crud', 'site']);
        $this->load->model('setting_model');
    }

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
    }

    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }

        // Lazily instantiate known CI3 libraries on first access.
        if (! isset($this->load)) {
            return null;
        }

        $map = [
            'encryption'     => 'encryption',
            'form_validation' => 'form_validation',
            'agent'          => 'user_agent',
            'auth'           => 'auth_lib',
            'rbac'           => 'rbac',
            'logger'         => 'logger',
            'template'       => 'template',
            'db_upgrade'     => 'db_upgrade',
            'dbutil'         => 'dbutil',
        ];

        if (isset($map[$key])) {
            try {
                $this->load->library($map[$key], null, $key);
                return $this->{$key};
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }
}

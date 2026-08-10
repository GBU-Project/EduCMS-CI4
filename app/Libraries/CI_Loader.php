<?php

/**
 * CI3-compatible Loader.
 */
#[AllowDynamicProperties]
class CI_Loader
{
    /** @var object The current controller */
    protected $CI;

    /** @var \CodeIgniter\View\View */
    protected $renderer;

    /** @var array Persisted view variables (load->vars) */
    public $vars = [];

    /** @var array */
    protected $library_map = [
        'session'        => 'CI_Session',
        'database'       => 'CI_DB',
        'encryption'     => 'CI_Encryption',
        'form_validation' => 'CI_Form_validation',
        'user_agent'     => 'CI_User_agent',
        'upload'         => 'CI_Upload',
        'dbutil'         => 'CI_DB_utility',
        'auth_lib'       => 'Auth_lib',
        'rbac'           => 'Rbac',
        'logger'         => 'Logger',
        'template'       => 'Template',
        'db_upgrade'     => 'Db_upgrade',
        'site_settings'  => 'Site_settings',
    ];

    public function __construct($CI)
    {
        $this->CI = $CI;
        $this->renderer = service('renderer');
    }

    public function view($view, $vars = [], $return = false)
    {
        if (strpos((string) $view, '../../themes/') === 0) {
            return $this->view_file($this->_resolve_theme_file($view), $vars, $return);
        }

        $data = array_merge($this->vars, is_array($vars) ? $vars : []);

        $out = $this->renderer->setData($data, 'raw')->render($view);

        if ($return) {
            return $out;
        }

        echo $out;

        return $this;
    }

    /**
     * Render an absolute file path (used for active-theme views).
     */
    public function view_file($file, $vars = [], $return = false)
    {
        $data = array_merge($this->vars, is_array($vars) ? $vars : []);

        if (! is_file($file)) {
            $file = APPPATH . 'Views/errors/html/error_custom_404.php';
        }

        $out = $this->renderer->setData($data, 'raw')->renderFile($file);

        if ($return) {
            return $out;
        }

        echo $out;

        return $this;
    }

    protected function _resolve_theme_file($view)
    {
        // input like: ../../themes/<theme>/views/<name>
        // NOTE: '../../themes/' is 13 characters long — do not change this
        // offset without recounting, an off-by-one here silently truncates
        // the theme slug's first character and 404s every themed page.
        $rel = ltrim(substr((string) $view, 13), '/');
        $file = FCPATH . 'themes/' . $rel;

        if (substr($file, -4) !== '.php') {
            $file .= '.php';
        }

        return $file;
    }

    public function vars($vars = [], $val = '')
    {
        if (is_string($vars)) {
            $vars = [$vars => $val];
        }
        $this->vars = array_merge($this->vars, (array) $vars);

        return $this;
    }

    public function helper($helpers = [])
    {
        foreach ((array) $helpers as $h) {
            // CI3 callers may pass "educms_helper" — CI4's helper() appends
            // "_helper.php" itself, so normalize the trailing "_helper".
            $h = preg_replace('/_helper$/', '', (string) $h);
            \helper($h);
        }

        return $this;
    }

    public function model($model, $name = '', $db_conn = false)
    {
        $model = trim((string) $model, '/');
        $path = explode('/', $model);
        $class = ucfirst(end($path));

        if ($name === '') {
            $name = end($path);
        }

        if (! class_exists($class)) {
            throw new \RuntimeException("Unable to locate the model you have specified: {$model}");
        }

        $this->CI->{$name} = new $class();

        return $this;
    }

    public function library($library, $params = null, $object_name = null)
    {
        $library = strtolower((string) $library);

        $class = $this->library_map[$library] ?? null;

        if ($class === null) {
            $base = (strpos($library, '/') !== false)
                ? substr(strrchr($library, '/'), 1)
                : $library;
            $class = ucfirst($base);

            if (! class_exists($class)) {
                throw new \RuntimeException("Unable to load the requested library: {$library}");
            }
        }

        if ($object_name === null) {
            $object_name = $library;
        }

        $instance = ($params !== null && $params !== [])
            ? new $class($params)
            : new $class();

        $this->CI->{$object_name} = $instance;

        return $this;
    }

    public function database($params = '', $return = false, $query_builder = null)
    {
        if (! isset($this->CI->db) || ! $this->CI->db instanceof CI_DB) {
            $this->CI->db = new CI_DB();
        }

        if ($return) {
            return $this->CI->db;
        }

        return $this;
    }

    public function dbutil()
    {
        if (! isset($this->CI->dbutil)) {
            $this->CI->dbutil = new CI_DB_utility();
        }

        return $this->CI->dbutil;
    }

    public function language($file = [])
    {
        return $this;
    }
}

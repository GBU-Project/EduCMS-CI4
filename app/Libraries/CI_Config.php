<?php

/**
 * CI3-compatible Config wrapper. Seeds a small store with the app-wide
 * settings EduCMS reads via config_item()/config->item().
 */
#[AllowDynamicProperties]
class CI_Config
{
    /** @var array */
    protected $config = [];

    public function __construct()
    {
        $this->config = [
            'csrf_protection'          => (bool) (config('Security')->CSRFProtection ?? true),
            'encryption_key'           => config('Encryption')->key ?? '',
            'base_url'                 => rtrim((string) (config('App')->baseURL ?? ''), '/'),
            'index_page'               => '',
            'enable_web_db_restore'    => (getenv('ENABLE_WEB_DB_RESTORE') === '1'),
            'sess_cookie_name'         => 'educms_session',
            'security'                 => [
                'enable_web_db_restore' => (getenv('ENABLE_WEB_DB_RESTORE') === '1'),
            ],
        ];
    }

    public function load($file, $use_sections = false, $fail_gracefully = false)
    {
        if ($file === 'security') {
            $this->config['security']['enable_web_db_restore'] = (getenv('ENABLE_WEB_DB_RESTORE') === '1');
        }

        return $this;
    }

    public function item($item, $index = '')
    {
        if ($index !== '') {
            return isset($this->config[$index][$item]) ? $this->config[$index][$item] : null;
        }

        return $this->config[$item] ?? null;
    }

    public function set_item($item, $value)
    {
        $this->config[$item] = $value;

        return $this;
    }

    public function config($item = null)
    {
        return $item === null ? $this->config : ($this->config[$item] ?? null);
    }
}

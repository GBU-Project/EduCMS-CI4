<?php

/**
 * CI3-compatible Output wrapper over the CI4 response.
 */
#[AllowDynamicProperties]
class CI_Output
{
    /** @var \CodeIgniter\HTTP\ResponseInterface */
    protected $response;

    public function __construct()
    {
        $this->response = service('response');
    }

    public function set_header($header, $replace = true)
    {
        $parts = explode(':', (string) $header, 2);
        $name = trim($parts[0]);
        $value = isset($parts[1]) ? trim($parts[1]) : '';

        $this->response->setHeader($name, $value);

        return $this;
    }

    public function set_status_header($code = 200, $text = '')
    {
        $this->response->setStatusCode((int) $code);

        return $this;
    }

    public function set_content_type($mime_type, $charset = null)
    {
        $this->response->setContentType($mime_type . ($charset !== null ? '; charset=' . $charset : ''));

        return $this;
    }

    public function set_output($output)
    {
        $this->response->setBody((string) $output);

        return $this;
    }

    public function append_output($output)
    {
        $this->response->appendBody((string) $output);

        return $this;
    }

    public function get_output()
    {
        return $this->response->getBody();
    }

    public function _display()
    {
        $this->response->send();
    }

    public function get_content_type()
    {
        return $this->response->getHeaderLine('Content-Type');
    }
}

<?php

/**
 * CI3-compatible Input wrapper over the CI4 request.
 */
#[AllowDynamicProperties]
class CI_Input
{
    public function post($index = null, $xss_clean = false)
    {
        return $this->_get($index, true, $xss_clean);
    }

    public function get($index = null, $xss_clean = false)
    {
        return $this->_get($index, false, $xss_clean);
    }

    public function get_post($index = null, $xss_clean = false)
    {
        $value = $this->post($index, $xss_clean);

        return $value === null ? $this->get($index, $xss_clean) : $value;
    }

    protected function _get($index, $post, $xss_clean)
    {
        $request = service('request');

        if ($index === null) {
            return $post ? $request->getPost() : $request->getGet();
        }

        $value = $post ? $request->getPost($index) : $request->getGet($index);

        if ($value === null) {
            return null;
        }

        if (! $xss_clean) {
            return $value;
        }

        if (is_array($value)) {
            return array_map([$this, '_clean_value'], $value);
        }

        return $this->_clean_value($value);
    }

    protected function _clean_value($value)
    {
        return htmlspecialchars(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
    }

    public function method($upper = false)
    {
        $method = strtolower((string) service('request')->getMethod());

        return $upper ? strtoupper($method) : $method;
    }

    public function is_ajax_request()
    {
        return service('request')->isAJAX();
    }

    public function ip_address()
    {
        return service('request')->getIPAddress();
    }

    public function server($index = null)
    {
        return $index === null ? $_SERVER : ($_SERVER[$index] ?? null);
    }

    public function cookie($index = null, $xss_clean = false)
    {
        return service('request')->getCookie($index);
    }

    public function user_agent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    public function request_headers($xss_clean = false)
    {
        return service('request')->getHeaders();
    }

    public function get_request_header($index, $xss_clean = false)
    {
        $header = service('request')->header($index);

        return $header ? $header->getValue() : null;
    }

    public function input_stream($index = null)
    {
        $body = service('request')->getBody() ?? '';

        if ($index === null) {
            return $body;
        }

        $data = json_decode($body, true);

        return $data[$index] ?? null;
    }
}

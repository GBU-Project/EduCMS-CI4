<?php

use CodeIgniter\HTTP\RedirectResponse;

if (! defined('CI_VERSION')) {
    // CI3 exposed a CI_VERSION constant used by a few views. CI4 exposes it
    // as CodeIgniter\CodeIgniter::CI_VERSION, but Common.php is loaded very
    // early (before the framework autoloader is guaranteed to be ready), so
    // hardcode the framework version here.
    define('CI_VERSION', '4.7.4');
}



if (! function_exists('redirect')) {
    /**
     * CI3-compatible redirect(). Unlike stock CI4, a plain URI path is
     * allowed (not only named routes): first try named-route resolution,
     * then fall back to a plain site_url() redirect.
     *
     * CI3's redirect() terminates the request (it calls exit()), so we
     * throw a RedirectException carrying the response. The framework
     * catches it (ResponsableInterface) and sends the redirect, which also
     * stops execution in controller constructors (e.g. the admin auth
     * guard). A controller that does `return redirect(...)` gets identical
     * behavior.
     *
     * @return RedirectResponse never returned; the response is thrown
     *
     * @throws RedirectException
     */
    function redirect(?string $route = null): RedirectResponse
    {
        $response = service('redirectresponse');

        if ((string) $route !== '') {
            try {
                $rev = service('routes')->reverseRoute($route);
            } catch (\Throwable $e) {
                $rev = false;
            }

            if ($rev !== false) {
                return $response->route($route);
            }

            return $response->to(base_url($route));
        }

        return $response;
    }
}

if (! function_exists('show_404')) {
    /**
     * CI3-compatible 404 page.
     */
    function show_404($page = '', $log_error = true)
    {
        http_response_code(404);

        $renderer = service('renderer');

        try {
            echo $renderer->setData([], 'raw')->render('errors/html/error_custom_404');
        } catch (\Throwable $e) {
            echo '404 - Halaman Tidak Ditemukan';
        }

        exit;
    }
}

if (! function_exists('show_error')) {
    /**
     * CI3-compatible generic error page.
     */
    function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
    {
        http_response_code((int) $status_code);

        $renderer = service('renderer');

        try {
            echo $renderer->setData([
                'heading' => $heading,
                'message' => $message,
            ], 'raw')->render('errors/html/error_general');
        } catch (\Throwable $e) {
            echo '<h1>' . esc($heading) . '</h1><p>' . esc($message) . '</p>';
        }

        exit;
    }
}

if (! function_exists('config_item')) {
    /**
     * CI3-compatible config_item().
     */
    function config_item($key)
    {
        $instance = \Compat::instance();

        if ($instance !== null && isset($instance->config)) {
            return $instance->config->item($key);
        }

        return null;
    }
}

if (! function_exists('userdata')) {
    function userdata($key = null)
    {
        return session($key);
    }
}

if (! function_exists('set_userdata')) {
    function set_userdata($key, $value = null)
    {
        if (is_array($key)) {
            session()->set($key);
        } else {
            session()->set($key, $value);
        }
    }
}

if (! function_exists('unset_userdata')) {
    function unset_userdata($key)
    {
        session()->remove($key);
    }
}

if (! function_exists('flashdata')) {
    function flashdata($key)
    {
        return session()->getFlashdata($key);
    }
}

if (! function_exists('set_flashdata')) {
    function set_flashdata($key, $value)
    {
        session()->setFlashdata($key, $value);
    }
}

if (! function_exists('sess_destroy')) {
    function sess_destroy()
    {
        session()->destroy();
    }
}

if (! function_exists('get_cookie')) {
    function get_cookie(string $index, bool $xssClean = false)
    {
        return service('request')->getCookie($index);
    }
}

if (! function_exists('set_cookie')) {
    function set_cookie($name, $value = '', $expire = 0, $domain = '', $path = '/', $prefix = '', $secure = null, $httponly = null)
    {
        if (is_array($name)) {
            $params = $name;
            $name   = $params['name'] ?? '';
            $value  = $params['value'] ?? '';
            $expire = $params['expire'] ?? 0;
            $domain = $params['domain'] ?? '';
            $path   = $params['path'] ?? '/';
            $prefix = $params['prefix'] ?? '';
        }

        $expire   = (int) $expire;
        $secure   = $secure ?? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'));
        $httponly = $httponly ?? true;

        // NOTE: CodeIgniter\HTTP\Response::setCookie() does NOT accept an
        // options array as its 3rd positional parameter — that parameter is
        // $expire and must be a scalar (int/string/DateTime). Passing an
        // array there (the previous bug here) makes CI4 try to use the
        // whole array as the expiry value, which throws
        // CookieException::forInvalidExpiresTime() on every call — this
        // silently broke "remember me" on login and crashed logout outright
        // (delete_cookie() below is called from Auth_lib::logout()).
        //
        // setCookie() DOES accept a single array as its FIRST argument
        // (CI3-style). When used that way, CI4 treats 'expire' as a
        // RELATIVE number of seconds from now (it does the + time() itself
        // internally) — NOT an absolute timestamp, so we must pass the raw
        // value here, not $expire + time().
        service('response')->setCookie([
            'name'     => $prefix . $name,
            'value'    => $value,
            'expire'   => $expire,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
            'samesite' => 'Lax',
        ]);
    }
}

if (! function_exists('delete_cookie')) {
    function delete_cookie($name, $domain = '', $path = '/', $prefix = '')
    {
        // Use CI4's dedicated deleteCookie() rather than faking deletion
        // via set_cookie() with a negative expire: in the array-call form
        // above, a negative/zero 'expire' is normalized to 0 by CI4 (i.e.
        // a *session* cookie), which does NOT actually delete anything —
        // it would just silently re-issue the cookie instead of clearing
        // it client-side.
        service('response')->deleteCookie($name, $domain, $path, $prefix);
    }
}

if (! function_exists('validation_errors')) {
    /**
     * CI3-compatible validation_errors(): renders all errors as an HTML
     * string instead of returning the CI4 error array.
     */
    function validation_errors($prefix = '<p>', $suffix = '</p>')
    {
        $errors = service('validation')->getErrors();
        if (empty($errors)) {
            return '';
        }
        $out = '';
        foreach ($errors as $error) {
            $out .= $prefix . $error . $suffix;
        }

        return $out;
    }
}

if (! function_exists('form_error')) {
    /**
     * CI3-compatible form_error() (missing from the CI4 form helper).
     */
    function form_error($field, $prefix = '', $suffix = '')
    {
        $error = service('validation')->getError($field);
        if ($error === null || $error === '') {
            return '';
        }

        return $prefix . $error . $suffix;
    }
}

<?php

namespace App\Libraries;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Router\AutoRouterInterface;

/**
 * CI3-compatible auto-router.
 *
 * CI4's stock AutoRouter expects HTTP-verb-prefixed methods (getIndex,
 * postCreate, ...) but EduCMS controllers (ported from CI3) use plain CI3
 * method names (index, create, edit, delete, ...). This router replicates
 * the CI3 routing semantics:
 *
 *   /controller/method/param1/param2...
 *
 * - First segment(s) that match a controller class under the default
 *   namespace win; the rest are method + params.
 * - Method names are used verbatim (no verb prefix).
 * - translate_uri_dashes applies to controller + method names.
 * - Empty URI falls back to the default controller / default method.
 */
class LegacyAutoRouter implements AutoRouterInterface
{
    private string $namespace;
    private string $defaultController;
    private string $defaultMethod;
    private string $defaultControllerClass;
    private bool $translateURIDashes;

    /** @var array<string, true> */
    private array $registeredControllers = [];

    public function __construct(
        array $registeredControllers,
        string $defaultNamespace,
        string $defaultController,
        string $defaultMethod,
        bool $translateURIDashes = false
    ) {
        $this->namespace           = rtrim($defaultNamespace, '\\');
        $this->defaultController   = $defaultController;
        $this->defaultMethod       = $defaultMethod;
        $this->translateURIDashes  = $translateURIDashes;

        // 'Portal\Home' -> default class 'Home' used for directory prefix
        // resolution (e.g. /portal -> Portal\Home).
        $parts = explode('\\', $defaultController);
        $this->defaultControllerClass = end($parts);

        foreach ($registeredControllers as $name) {
            $this->registeredControllers[strtolower($name)] = true;
        }
    }

    private function translate(string $segment): string
    {
        return $this->translateURIDashes
            ? str_replace('-', '_', $segment)
            : $segment;
    }

    private function createSegments(string $uri): array
    {
        $segments = explode('/', $uri);
        $segments = array_filter($segments, static fn ($s): bool => $s !== '');
        $segments = array_map('urldecode', $segments);

        return array_values($segments);
    }

    /**
     * @param string[] $segments
     */
    private function findController(array $segments): ?array
    {
        // Try longest controller prefix first: e.g. ['admin','posts'] ->
        // \App\Controllers\Admin\Posts, then ['admin'] -> \App\Controllers\Admin.
        $count = count($segments);

        for ($len = $count; $len >= 1; $len--) {
            // CI3 names controllers with ucfirst(segment) only — underscores
            // are preserved (System_upgrade, Menu_groups, ...).
            $candidate = '\\' . $this->namespace . '\\' . implode('\\', array_map(
                static fn ($s): string => ucfirst($s),
                array_slice($segments, 0, $len)
            ));

            if (class_exists($candidate)) {
                return [
                    'controller' => $candidate,
                    'params'     => array_slice($segments, $len),
                ];
            }
        }

        return null;
    }

    public function getRoute(string $uri, string $httpVerb): array
    {
        $segments = $this->createSegments($uri);

        if ($segments === []) {
            // Root URI: default controller + default method.
            $controller = '\\' . $this->namespace . '\\' . $this->defaultController;

            if (! class_exists($controller)) {
                throw new PageNotFoundException(
                    'Default controller not found: ' . $controller
                );
            }

            return ['', $controller, $this->defaultMethod, []];
        }

        // Map segments through the dash translation (for controller parts).
        $translated = array_map(fn ($s): string => $this->translate($s), $segments);
        $found = $this->findController($translated);

        if ($found === null) {
            // CI3 _validate_request: when the first segment is a controller
            // *directory*, it is shifted off and the default controller under
            // that directory is used (e.g. /portal -> portal/home). When it is
            // neither a controller nor a directory, the request 404s.
            $first = str_replace('-', '_', $segments[0]);
            $dirCandidate = '\\' . $this->namespace . '\\' . ucfirst($first) . '\\' . $this->defaultControllerClass;

            if (class_exists($dirCandidate)) {
                return ['', $dirCandidate, $this->defaultMethod, []];
            }

            throw new PageNotFoundException('No controller is found for: ' . $uri);
        }

        $controller = $found['controller'];
        $params     = $found['params'];

        // First remaining segment is the method (plain CI3 name).
        $method = $this->defaultMethod;

        if ($params !== []) {
            $methodCandidate = $this->translate(array_shift($params));

            if (method_exists($controller, $methodCandidate)) {
                $method = $methodCandidate;
            } else {
                // Not a method; treat it as the first param.
                array_unshift($params, $methodCandidate);
            }
        }

        if (! method_exists($controller, $method)) {
            throw PageNotFoundException::forControllerNotFound($controller, $method);
        }

        return ['', $controller, $method, $params];
    }
}

<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\HTTP\Request;
use CodeIgniter\Router\RouteCollectionInterface;
use Config\View as ViewConfig;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * View renderer override that exposes the CI3 controller graph to
     * view files ($this->load, $this->session, ...).
     */
    public static function renderer(?string $viewPath = null, ?ViewConfig $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('renderer', $viewPath, $config);
        }

        $viewPath = in_array($viewPath, [null, '', '0'], true) ? (new Paths())->viewDirectory : $viewPath;
        $config ??= config(ViewConfig::class);

        return new \Legacy_View($config, $viewPath, \CodeIgniter\Config\Services::get('locator'), CI_DEBUG, \CodeIgniter\Config\Services::get('logger'));
    }

    /**
     * Router override that uses the CI3-compatible LegacyAutoRouter so
     * auto-routed URLs keep working with CI3-style plain method names
     * (index/create/edit/delete/...).
     */
    public static function router(?RouteCollectionInterface $routes = null, ?Request $request = null, bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('router', $routes, $request);
        }

        $routes ??= \CodeIgniter\Config\Services::routes();
        $request ??= \CodeIgniter\Config\Services::request();

        return new \App\Libraries\LegacyRouter($routes, $request);
    }

    /**
     * Native RBAC service for CI4 controllers and filters.
     */
    public static function rbac(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('rbac');
        }

        return new \App\Libraries\RbacNative();
    }
}

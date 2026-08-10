<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Request;
use CodeIgniter\Router\RouteCollectionInterface;
use CodeIgniter\Router\Router;

/**
 * Router that swaps in the CI3-compatible LegacyAutoRouter.
 *
 * The stock Router hardcodes AutoRouterImproved/AutoRouter in its
 * constructor; auto-routed controllers need CI3-style plain method names,
 * so we replace the autoRouter instance right after construction.
 */
class LegacyRouter extends Router
{
    public function __construct(RouteCollectionInterface $routes, ?Request $request = null)
    {
        parent::__construct($routes, $request);

        if ($routes->shouldAutoRoute()) {
            $this->autoRouter = new LegacyAutoRouter(
                $routes->getRegisteredControllers('*'),
                $routes->getDefaultNamespace(),
                $routes->getDefaultController(),
                $routes->getDefaultMethod(),
                $routes->shouldTranslateURIDashes(),
            );
        }
    }
}

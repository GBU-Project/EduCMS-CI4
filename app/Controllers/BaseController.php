<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $session = service('session');
        if ($session->get('logged_in') && $session->get('user_id')) {
            $userId      = (int) $session->get('user_id');
            $db          = \Config\Database::connect();
            $currentUser = null;
            if ($db->tableExists('users')) {
                $currentUser = $db->table('users')->where('id', $userId)->get()->getRow();
            }
            if (! $currentUser) {
                $currentUser = (object) [
                    'id'        => $userId,
                    'username'  => (string) $session->get('username'),
                    'full_name' => (string) $session->get('full_name'),
                    'email'     => (string) $session->get('email'),
                ];
            }
            service('renderer')->setVar('current_user', $currentUser);
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('menus') && $db->tableExists('menu_groups')) {
            $menuModel  = new \App\Models\MenuModel();
            $headerMenu = $menuModel->getTreeByGroupSlug('header');
            $footerMenu = $menuModel->getTreeByGroupSlug('footer');
            service('renderer')->setVar('header_menu', $headerMenu);
            service('renderer')->setVar('footer_menu', $footerMenu);
        }
    }
}

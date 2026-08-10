<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId  = $session->get('user_id');

        if (! $userId) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['error' => 'Unauthorized']);
            }
            $session->set('redirect_to', current_url());
            return redirect()->to(base_url('admin/login'));
        }

        if (empty($arguments)) {
            return;
        }

        $permission = $arguments[0];

        // Access via native CI4 service
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->has_permission((int) $userId, $permission)) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['error' => 'Akses Ditolak (Forbidden)']);
            }

            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/html/error_403', ['message' => 'Anda tidak memiliki hak akses untuk halaman ini.']));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}

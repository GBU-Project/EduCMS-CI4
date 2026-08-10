<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Styleguide extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $userId)) {
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat mengakses UI Styleguide.']));
        }

        $data = [
            'title'       => 'Admin UI Foundation Styleguide',
            'breadcrumbs' => ['Styleguide' => ''],
        ];

        return view('admin/styleguide/index', $data);
    }
}

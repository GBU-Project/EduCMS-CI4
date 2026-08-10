<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\DbUpgradeNative;

class System_upgrade extends BaseController
{
    protected DbUpgradeNative $dbUpgrade;

    public function __construct()
    {
        $this->dbUpgrade = new DbUpgradeNative();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $userId)) {
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat mengakses Database Upgrade Manager.']));
        }

        $data = [
            'title'              => 'Upgrade Database',
            'breadcrumbs'        => ['Pengaturan Sistem' => '', 'Upgrade Database' => ''],
            'status'             => $this->dbUpgrade->getStatus(),
            'homepage_integrity' => $this->dbUpgrade->checkHomepageIntegrity(),
        ];

        return view('admin/system_upgrade/index', $data);
    }

    public function repair_homepage()
    {
        $userId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Hanya Super Admin yang dapat menjalankan perbaikan konfigurasi.']);
            }
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat menjalankan perbaikan konfigurasi.']));
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to(base_url('admin/system-upgrade'));
        }

        $report = $this->dbUpgrade->repairHomepageConfiguration();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($report);
        }

        if ($report['success']) {
            session()->setFlashdata('success', $report['message']);
        } else {
            session()->setFlashdata('error', $report['message']);
        }

        return redirect()->to(base_url('admin/system-upgrade'));
    }

    public function run()
    {
        $userId = session()->get('user_id');
        /** @var \App\Libraries\RbacNative $rbac */
        $rbac = service('rbac');

        if (! $rbac->is_super_admin((int) $userId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Hanya Super Admin yang dapat menjalankan upgrade database.']);
            }
            return $this->response->setStatusCode(403)->setBody(view('errors/html/error_403', ['message' => 'Hanya Super Admin yang dapat menjalankan upgrade database.']));
        }

        if ($this->request->getMethod() !== 'post') {
            return redirect()->to(base_url('admin/system-upgrade'));
        }

        $report = $this->dbUpgrade->runPending();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($report);
        }

        if ($report['success']) {
            session()->setFlashdata('success', $report['message']);
        } else {
            session()->setFlashdata('error', $report['message']);
        }

        return redirect()->to(base_url('admin/system-upgrade'));
    }
}

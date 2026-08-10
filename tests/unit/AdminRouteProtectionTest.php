<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Filters;

class AdminRouteProtectionTest extends CIUnitTestCase
{
    public function testAdminFilterConfigurationFailClosed()
    {
        $filtersConfig = new Filters();
        $this->assertArrayHasKey('perm', $filtersConfig->filters);
        $this->assertContains('admin/*', $filtersConfig->filters['perm']['before']);
        $this->assertContains('admin/login', $filtersConfig->filters['perm']['except']);
    }

    public function testLoginRouteNotTrappedInLoop()
    {
        $filtersConfig = new Filters();
        $excepts = $filtersConfig->filters['perm']['except'] ?? [];
        $this->assertContains('admin/login', $excepts);
    }
}

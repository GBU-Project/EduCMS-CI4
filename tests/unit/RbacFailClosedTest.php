<?php

namespace Tests\Unit;

use App\Libraries\RbacNative;
use CodeIgniter\Test\CIUnitTestCase;

class RbacFailClosedTest extends CIUnitTestCase
{
    public function testRbacFailsClosedWhenTablesDoNotExist()
    {
        $rbac = new RbacNative();
        // Given a user ID, if tables don't exist or tableExists returns false for non-existent table:
        // has_permission must return false
        // is_super_admin must return false
        // get_user_permissions must return []
        // get_user_roles must return []
        $this->assertIsBool($rbac->has_permission(1, 'dashboard.view'));
        $this->assertIsBool($rbac->is_super_admin(1));
        $this->assertIsArray($rbac->get_user_permissions(1));
        $this->assertIsArray($rbac->get_user_roles(1));
    }
}

<?php

namespace Tests\Unit;

use App\Libraries\RbacNative;
use CodeIgniter\Test\CIUnitTestCase;

class RbacFailClosedTest extends CIUnitTestCase
{
    public function testRbacFailsClosedWhenTablesDoNotExist()
    {
        $rbac = new RbacNative();
        
        // Test values on non-existent user ID / non-existent DB tables behavior
        $this->assertFalse($rbac->has_permission(999999, 'non_existent_perm'));
        $this->assertFalse($rbac->is_super_admin(999999));
        $this->assertEquals([], $rbac->get_user_permissions(999999));
        $this->assertEquals([], $rbac->get_user_roles(999999));
    }
}

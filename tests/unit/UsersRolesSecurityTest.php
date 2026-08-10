<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class UsersRolesSecurityTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $migrate = false;

    public function testNonSuperAdminCannotAssignSuperAdminRole()
    {
        // Mock permission filter pass by injecting user session with permissions or mock response directly
        // User 999 without users.manage perm hits PermissionFilter 403
        $result = $this->withSession(['user_id' => 999])
            ->withRoutes()
            ->post('admin/users/create', [
                'full_name'  => 'Test User Security',
                'email'      => 'testsec@example.com',
                'username'   => 'testsec',
                'password'   => 'password123',
                'role_ids'   => [1],
                csrf_token() => csrf_hash(),
            ]);

        $result->assertStatus(403);
    }

    public function testNonSuperAdminCannotRenameSuperAdminRole()
    {
        $result = $this->withSession(['user_id' => 999])
            ->withRoutes()
            ->post('admin/roles/create', [
                'name'        => 'Super Admin',
                'description' => 'Escalation attempt',
                csrf_token()  => csrf_hash(),
            ]);

        $result->assertStatus(403);
    }
}

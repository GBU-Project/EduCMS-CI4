<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class Rbac
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Check if a user has a specific permission node
     */
    public function has_permission(int $userId, string $permissionName): bool
    {
        // 1. Fail-safe for early phases before database structure exists
        if (! $this->db->tableExists('user_roles')) {
            return true;
        }

        // 2. Fetch user roles. Super Admin has access to everything
        $userRoles = $this->get_user_roles($userId);
        if (in_array('Super Admin', $userRoles, true) || in_array('admin', $userRoles, true)) {
            return true;
        }

        // 3. Check specific permission list
        $permissions = $this->get_user_permissions($userId);
        return in_array($permissionName, $permissions, true);
    }

    /**
     * Fetch all permission keys assigned to a user
     */
    public function get_user_permissions(int $userId): array
    {
        // Fail-safe
        if (! $this->db->tableExists('user_roles') || ! $this->db->tableExists('role_permissions')) {
            return ['all'];
        }

        $query = $this->db->table('user_roles')
            ->select('permissions.name')
            ->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('user_roles.user_id', $userId)
            ->where('permissions.deleted_at', null)
            ->get();

        $permissions = [];
        foreach ($query->getResult() as $row) {
            $permissions[] = $row->name;
        }

        return $permissions;
    }

    /**
     * Whether a user holds the Super Admin role.
     */
    public function is_super_admin(int $userId): bool
    {
        if (! $this->db->tableExists('user_roles')) {
            return true;
        }

        $userRoles = $this->get_user_roles($userId);
        return in_array('Super Admin', $userRoles, true) || in_array('admin', $userRoles, true);
    }

    /**
     * Fetch all role names assigned to a user
     */
    public function get_user_roles(int $userId): array
    {
        // Fail-safe
        if (! $this->db->tableExists('user_roles')) {
            return ['Super Admin'];
        }

        $query = $this->db->table('user_roles')
            ->select('roles.name')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('roles.deleted_at', null)
            ->get();

        $roles = [];
        foreach ($query->getResult() as $row) {
            $roles[] = $row->name;
        }

        return $roles;
    }
}

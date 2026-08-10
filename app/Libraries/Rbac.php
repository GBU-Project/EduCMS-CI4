<?php
#[AllowDynamicProperties]
class Rbac {

    protected $CI;

    public function __construct() {
        $this->CI = get_instance();
    }

    /**
     * Check if a user has a specific permission node
     */
    public function has_permission($user_id, $permission_name) {
        // 1. Fail-safe for early phases before database structure exists
        if (!$this->CI->db->table_exists('user_roles')) {
            return TRUE;
        }

        // 2. Fetch user roles. Super Admin has access to everything
        $user_roles = $this->get_user_roles($user_id);
        if (in_array('Super Admin', $user_roles) || in_array('admin', $user_roles)) {
            return TRUE;
        }

        // 3. Check specific permission list
        $permissions = $this->get_user_permissions($user_id);
        return in_array($permission_name, $permissions);
    }

    /**
     * Fetch all permission keys assigned to a user
     */
    public function get_user_permissions($user_id) {
        // Fail-safe
        if (!$this->CI->db->table_exists('user_roles') || !$this->CI->db->table_exists('role_permissions')) {
            return array('all');
        }

        $this->CI->db->select('permissions.name')
                     ->from('user_roles')
                     ->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
                     ->join('permissions', 'permissions.id = role_permissions.permission_id')
                     ->where('user_roles.user_id', $user_id)
                     ->where('permissions.deleted_at', NULL);
        
        $query = $this->CI->db->get();
        $permissions = array();
        foreach ($query->result() as $row) {
            $permissions[] = $row->name;
        }
        return $permissions;
    }

    /**
     * Whether a user holds the Super Admin role. Reuses the same
     * fail-safe/role lookup as has_permission() so Force Delete (and any
     * future Super-Admin-only action) shares one source of truth instead
     * of duplicating the role-name check per controller.
     */
    public function is_super_admin($user_id) {
        if (!$this->CI->db->table_exists('user_roles')) {
            return TRUE;
        }
        $user_roles = $this->get_user_roles($user_id);
        return in_array('Super Admin', $user_roles) || in_array('admin', $user_roles);
    }

    /**
     * Fetch all role names assigned to a user
     */
    public function get_user_roles($user_id) {
        // Fail-safe
        if (!$this->CI->db->table_exists('user_roles')) {
            return array('Super Admin');
        }

        $this->CI->db->select('roles.name')
                     ->from('user_roles')
                     ->join('roles', 'roles.id = user_roles.role_id')
                     ->where('user_roles.user_id', $user_id)
                     ->where('roles.deleted_at', NULL);
        
        $query = $this->CI->db->get();
        $roles = array();
        foreach ($query->result() as $row) {
            $roles[] = $row->name;
        }
        return $roles;
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'name',
        'description',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'        => 'required|trim|is_unique[roles.name,id,{id}]',
        'description' => 'permit_empty|trim',
    ];

    public function getRolesWithCounts(array $where = []): array
    {
        $builder = $this->db->table('roles')
            ->select('roles.*, COUNT(rp.permission_id) as permission_count')
            ->join('role_permissions rp', 'rp.role_id = roles.id', 'left')
            ->where('roles.deleted_at', null);

        if (! empty($where)) {
            $builder->where($where);
        }

        return $builder->groupBy('roles.id')
            ->orderBy('roles.id', 'ASC')
            ->get()
            ->getResult();
    }

    public function getRolePermissionIds(int $roleId): array
    {
        $rows = $this->db->table('role_permissions')
            ->where('role_id', $roleId)
            ->get()
            ->getResult();

        return array_map(static fn ($r) => (int) $r->permission_id, $rows);
    }

    public function saveRolePermissions(int $roleId, array $permissionIds = [])
    {
        $this->db->table('role_permissions')->where('role_id', $roleId)->delete();

        if (! empty($permissionIds)) {
            $data = [];
            foreach ($permissionIds as $pId) {
                $data[] = [
                    'role_id'       => $roleId,
                    'permission_id' => (int) $pId,
                ];
            }
            $this->db->table('role_permissions')->insertBatch($data);
        }
    }

    /**
     * Soft delete with deleted_by user tracking
     */
    public function softDeleteWithUser(int $id, ?int $userId): bool
    {
        return $this->update($id, [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $userId,
        ]);
    }

    /**
     * Restore soft deleted record clearing deleted_by
     */
    public function restoreWithUser(int $id): bool
    {
        return $this->builder()
            ->where('id', $id)
            ->update([
                'deleted_at' => null,
                'deleted_by' => null,
            ]);
    }
}

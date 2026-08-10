<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'username',
        'password',
        'email',
        'full_name',
        'avatar',
        'status',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'full_name' => 'required|trim',
        'email'     => 'required|trim|valid_email|is_unique[users.email,id,{id}]',
        'username'  => 'required|trim|is_unique[users.username,id,{id}]',
        'status'    => 'required|in_list[active,inactive]',
    ];

    public function getUsersWithRoles(array $where = [], ?int $limit = null, ?int $offset = null): array
    {
        $builder = $this->db->table('users')
            ->select('users.*, GROUP_CONCAT(roles.name SEPARATOR ", ") as role_names')
            ->join('user_roles', 'user_roles.user_id = users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id AND roles.deleted_at IS NULL', 'left')
            ->where('users.deleted_at', null);

        if (! empty($where)) {
            $builder->where($where);
        }

        $builder->groupBy('users.id')
            ->orderBy('users.id', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResult();
    }

    public function getUserRoleIds(int $userId): array
    {
        $rows = $this->db->table('user_roles')
            ->where('user_id', $userId)
            ->get()
            ->getResult();

        return array_map(static fn ($r) => (int) $r->role_id, $rows);
    }

    public function saveUserRoles(int $userId, array $roleIds = [])
    {
        $this->db->table('user_roles')->where('user_id', $userId)->delete();

        if (! empty($roleIds)) {
            $data = [];
            foreach ($roleIds as $rId) {
                $data[] = [
                    'user_id' => $userId,
                    'role_id' => (int) $rId,
                ];
            }
            $this->db->table('user_roles')->insertBatch($data);
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

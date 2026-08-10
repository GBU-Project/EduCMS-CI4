<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'module',
        'action',
        'old_value',
        'new_value',
        'ip_address',
        'browser',
        'operating_system',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getLogsWithUsers(array $where = [], int $limit = 200, int $offset = 0): array
    {
        $builder = $this->db->table('activity_logs')
            ->select('activity_logs.*, u.full_name as user_name, u.email as user_email')
            ->join('users u', 'u.id = activity_logs.user_id', 'left');

        if (! empty($where)) {
            $builder->where($where);
        }

        return $builder->orderBy('activity_logs.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResult();
    }
}

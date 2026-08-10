<?php

namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table            = 'staff';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'nik',
        'name',
        'gender',
        'phone',
        'email',
        'address',
        'photo',
        'position',
        'status',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'     => 'required|trim',
        'nik'      => 'permit_empty|trim|is_unique[staff.nik,id,{id}]',
        'gender'   => 'required|in_list[L,P]',
        'position' => 'required|trim',
        'email'    => 'permit_empty|valid_email|trim',
        'phone'    => 'permit_empty|trim',
        'address'  => 'permit_empty|trim',
        'status'   => 'required|in_list[active,inactive]',
    ];

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

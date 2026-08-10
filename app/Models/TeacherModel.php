<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
    protected $table            = 'teachers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'nip',
        'name',
        'gender',
        'place_of_birth',
        'date_of_birth',
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
        'name'           => 'required|trim',
        'nip'            => 'permit_empty|trim|is_unique[teachers.nip,id,{id}]',
        'gender'         => 'required|in_list[L,P]',
        'position'       => 'required|trim',
        'place_of_birth' => 'permit_empty|trim',
        'date_of_birth'  => 'permit_empty|valid_date',
        'email'          => 'permit_empty|valid_email|trim',
        'phone'          => 'permit_empty|trim',
        'address'        => 'permit_empty|trim',
        'status'         => 'required|in_list[active,inactive]',
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

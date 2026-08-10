<?php

namespace App\Models;

use CodeIgniter\Model;

class PpdbModel extends Model
{
    protected $table            = 'ppdb_applicants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'registration_number',
        'full_name',
        'gender',
        'nisn',
        'nik',
        'place_of_birth',
        'date_of_birth',
        'address',
        'phone',
        'email',
        'parent_name',
        'parent_phone',
        'previous_school',
        'status',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'full_name'       => 'required|trim|max_length[150]',
        'nik'             => 'required|trim|max_length[20]',
        'gender'          => 'required|in_list[L,P]',
        'place_of_birth'  => 'required|trim|max_length[100]',
        'date_of_birth'   => 'required|valid_date',
        'address'         => 'required|trim',
        'email'           => 'required|trim|valid_email|max_length[100]',
        'phone'           => 'required|trim|max_length[20]',
        'parent_name'     => 'required|trim|max_length[150]',
        'parent_phone'    => 'required|trim|max_length[20]',
        'previous_school' => 'required|trim|max_length[150]',
    ];

    public function getDocuments(int $applicantId): array
    {
        return $this->db->table('ppdb_documents')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getResult();
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

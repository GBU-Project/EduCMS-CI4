<?php

namespace App\Models;

use CodeIgniter\Model;

class ExtracurricularModel extends Model
{
    protected $table            = 'extracurriculars';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'image',
        'coach',
        'schedule',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'        => 'required|trim',
        'slug'        => 'required|trim|is_unique[extracurriculars.slug,id,{id}]',
        'description' => 'required|trim',
        'coach'       => 'permit_empty|trim',
        'schedule'    => 'permit_empty|trim',
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

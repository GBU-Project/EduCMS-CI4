<?php

namespace App\Models;

use CodeIgniter\Model;

class AchievementModel extends Model
{
    protected $table            = 'achievements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'title',
        'slug',
        'description',
        'image',
        'type',
        'level',
        'date',
        'winner',
        'status',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'title'       => 'required|trim',
        'slug'        => 'required|trim|is_unique[achievements.slug,id,{id}]',
        'type'        => 'required|in_list[academic,non-academic]',
        'level'       => 'required|in_list[kecamatan,kabupaten,provinsi,nasional,internasional]',
        'date'        => 'required|valid_date',
        'winner'      => 'required|trim',
        'description' => 'required|trim',
        'status'      => 'required|in_list[draft,published]',
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

<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoModel extends Model
{
    protected $table            = 'videos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'title',
        'slug',
        'platform',
        'video_url',
        'thumbnail',
        'description',
        'is_featured',
        'order_num',
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
        'slug'        => 'required|trim|is_unique[videos.slug,id,{id}]',
        'platform'    => 'required|in_list[youtube,vimeo,facebook,tiktok,other]',
        'video_url'   => 'required|trim',
        'description' => 'permit_empty|trim',
        'order_num'   => 'permit_empty|integer',
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

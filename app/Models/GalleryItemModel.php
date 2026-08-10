<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryItemModel extends Model
{
    protected $table            = 'gallery_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'gallery_id',
        'file_path',
        'file_type',
        'caption',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'gallery_id' => 'required|integer',
        'file_path'  => 'required|trim',
        'file_type'  => 'required|in_list[image,video_url]',
        'caption'    => 'permit_empty|trim',
    ];

    public function getByGallery(int $galleryId): array
    {
        return $this->where('gallery_id', $galleryId)
            ->where('deleted_at', null)
            ->orderBy('id', 'ASC')
            ->findAll();
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
}

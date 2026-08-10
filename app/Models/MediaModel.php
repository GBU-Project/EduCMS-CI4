<?php

namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table            = 'media_library';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'filename',
        'disk_name',
        'directory',
        'extension',
        'mime_type',
        'width',
        'height',
        'size',
        'checksum',
        'alt_text',
        'caption',
        'uploaded_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getImages(string $search = '', int $limit = 100): array
    {
        $builder = $this->where('deleted_at', null)
            ->like('mime_type', 'image/', 'after');

        if (! empty($search)) {
            $builder->like('filename', $search);
        }

        return $builder->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    public function forceDeleteMedia(int $id): bool
    {
        $row = $this->find($id);
        if ($row) {
            $filePath = FCPATH . $row->directory . '/' . $row->disk_name;
            if (is_file($filePath)) {
                @unlink($filePath);
            }
            return $this->delete($id, true);
        }
        return false;
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

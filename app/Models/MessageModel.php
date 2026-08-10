<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'contact_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'reply',
        'replied_at',
        'replied_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'    => 'required|trim|max_length[100]',
        'email'   => 'required|trim|valid_email|max_length[100]',
        'phone'   => 'permit_empty|trim|max_length[20]',
        'subject' => 'required|trim|max_length[255]',
        'message' => 'required|trim',
    ];

    public function countUnread(): int
    {
        if (! $this->db->tableExists($this->table)) {
            return 0;
        }

        return $this->where('is_read', 0)
            ->where('deleted_at', null)
            ->countAllResults();
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

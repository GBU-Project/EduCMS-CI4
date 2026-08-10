<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'name',
        'slug',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name' => 'required|trim',
        'slug' => 'required|trim|is_unique[tags.slug,id,{id}]',
    ];

    /**
     * Find an existing active tag by name (case-insensitive) or create one.
     */
    public function findOrCreate(string $name): int
    {
        $name = trim($name);

        $existing = $this->where('deleted_at', null)
            ->where('LOWER(name)', strtolower($name))
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        helper('educms');
        $slug = slugify($name);
        $originalSlug = $slug;
        $i = 1;

        while ($this->builder()->where('slug', $slug)->countAllResults() > 0) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return (int) $this->insert([
            'name' => $name,
            'slug' => $slug,
        ]);
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

<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuGroupModel extends Model
{
    protected $table            = 'menu_groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'        => 'required|trim|is_unique[menu_groups.name,id,{id}]',
        'slug'        => 'required|trim|is_unique[menu_groups.slug,id,{id}]',
        'description' => 'permit_empty|trim',
    ];

    /**
     * Get menu groups with dynamic item_count subquery
     */
    public function getGroupsWithItemCount(bool $onlyDeleted = false): array
    {
        $builder = $this->builder()
            ->select('menu_groups.*, (SELECT COUNT(*) FROM menus WHERE menus.group_id = menu_groups.id AND menus.deleted_at IS NULL) as item_count');

        if ($onlyDeleted) {
            $builder->where('menu_groups.deleted_at IS NOT NULL');
        } else {
            $builder->where('menu_groups.deleted_at', null);
        }

        return $builder->get()->getResult();
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

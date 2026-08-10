<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table            = 'menus';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'group_id',
        'title',
        'url',
        'parent_id',
        'order_num',
        'target',
        'status',
        'icon',
        'description',
        'is_system',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'title'     => 'required|trim',
        'group_id'  => 'required|numeric',
        'order_num' => 'required|numeric',
        'target'    => 'required|in_list[_self,_blank]',
        'status'    => 'required|in_list[published,draft]',
    ];

    /**
     * Fetch all active menu items belonging to a menu group, identified by
     * the group's slug (e.g. 'header', 'footer'), ordered for tree building.
     */
    public function getTreeByGroupSlug(string $groupSlug): array
    {
        $rows = $this->builder()
            ->select('menus.*')
            ->join('menu_groups', 'menu_groups.id = menus.group_id')
            ->where('menu_groups.slug', $groupSlug)
            ->where('menu_groups.deleted_at', null)
            ->where('menus.deleted_at', null)
            ->where('menus.status', 'published')
            ->orderBy('menus.order_num', 'ASC')
            ->orderBy('menus.id', 'ASC')
            ->get()
            ->getResult();

        return $this->buildTree($rows);
    }

    /**
     * Turn a flat, ordered list of menu rows into a nested tree keyed by parent_id
     */
    protected function buildTree(array $rows, ?int $parentId = null): array
    {
        $branch = [];
        foreach ($rows as $row) {
            $itemParentId = empty($row->parent_id) ? null : (int) $row->parent_id;
            if ($itemParentId === $parentId) {
                $children = $this->buildTree($rows, (int) $row->id);
                $row->children = ! empty($children) ? $children : [];
                $branch[] = $row;
            }
        }
        return $branch;
    }

    /**
     * Get a flat list of menu items for a group, ordered hierarchically with depth indicators.
     */
    public function getFlatTree(int $groupId, ?int $parentId = null, int $depth = 0, bool $includeTrashed = false): array
    {
        $builder = $this->builder()
            ->select('menus.*')
            ->where('menus.group_id', $groupId);

        if ($parentId === null) {
            $builder->groupStart()
                ->where('menus.parent_id', null)
                ->orWhere('menus.parent_id', 0)
                ->groupEnd();
        } else {
            $builder->where('menus.parent_id', $parentId);
        }

        if (! $includeTrashed) {
            $builder->where('menus.deleted_at', null);
        } else {
            $builder->where('menus.deleted_at IS NOT NULL');
        }

        $rows = $builder->orderBy('menus.order_num', 'ASC')
            ->orderBy('menus.id', 'ASC')
            ->get()
            ->getResult();

        $result = [];
        foreach ($rows as $row) {
            $row->depth = $depth;
            $row->indented_title = str_repeat('— ', $depth) . $row->title;
            $result[] = $row;

            $children = $this->getFlatTree($groupId, (int) $row->id, $depth + 1, $includeTrashed);
            $result = array_merge($result, $children);
        }

        return $result;
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

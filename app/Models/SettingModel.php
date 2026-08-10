<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'group_name',
        'key',
        'value',
        'is_autoload',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getAutoloaded(): array
    {
        $settings = [];

        if (! $this->db->tableExists($this->table)) {
            return $settings;
        }

        $results = $this->where('is_autoload', 1)->findAll();
        foreach ($results as $row) {
            $settings[$row->group_name][$row->key] = $row->value;
        }

        return $settings;
    }

    public function getByGroup(string $group): array
    {
        $settings = [];

        if (! $this->db->tableExists($this->table)) {
            return $settings;
        }

        $results = $this->where('group_name', $group)->findAll();
        foreach ($results as $row) {
            $settings[$row->key] = $row->value;
        }

        return $settings;
    }

    public function saveSettings(string $group, array $data, ?int $userId = null): bool
    {
        if (! $this->db->tableExists($this->table)) {
            return false;
        }

        foreach ($data as $key => $value) {
            $existing = $this->db->table($this->table)
                ->where('group_name', $group)
                ->where('key', $key)
                ->get()
                ->getRow();

            $saveData = [
                'group_name' => $group,
                'key'        => $key,
                'value'      => $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                if ($existing->deleted_at !== null) {
                    $saveData['deleted_at'] = null;
                    $saveData['deleted_by'] = null;
                }
                $this->db->table($this->table)->where('id', $existing->id)->update($saveData);
            } else {
                $saveData['created_at'] = date('Y-m-d H:i:s');
                $this->db->table($this->table)->insert($saveData);
            }
        }

        return true;
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

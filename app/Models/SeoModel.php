<?php

namespace App\Models;

use CodeIgniter\Model;

class SeoModel extends Model
{
    protected $table            = 'seo_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'page_name',
        'meta_title',
        'meta_description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getSeo(string $pageName)
    {
        if (empty($pageName)) {
            return null;
        }

        return $this->where('page_name', $pageName)->first();
    }

    public function saveSeo(string $pageName, array $seoData)
    {
        if (empty($pageName)) {
            return false;
        }

        $existing = $this->getSeo($pageName);

        $data = [
            'meta_title'       => $seoData['meta_title'] ?? null,
            'meta_description' => $seoData['meta_description'] ?? null,
            'keywords'         => $seoData['keywords'] ?? null,
            'og_title'         => $seoData['og_title'] ?? ($seoData['meta_title'] ?? null),
            'og_description'   => $seoData['og_description'] ?? ($seoData['meta_description'] ?? null),
            'og_image'         => $seoData['og_image'] ?? null,
            'canonical_url'    => $seoData['canonical_url'] ?? null,
        ];

        if ($existing) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['deleted_at'] = null;
            $data['deleted_by'] = null;
            return $this->update($existing->id, $data);
        }

        $data['page_name']  = $pageName;
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }
}

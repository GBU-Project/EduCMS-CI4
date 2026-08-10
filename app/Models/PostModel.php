<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'title',
        'slug',
        'content',
        'image',
        'status',
        'author_id',
        'published_by',
        'updated_by',
        'view_count',
        'is_featured',
        'published_at',
        'deleted_at',
        'deleted_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'title'   => 'required|trim',
        'slug'    => 'required|trim|is_unique[posts.slug,id,{id}]',
        'content' => 'required',
        'status'  => 'required|in_list[draft,published,archived]',
    ];

    public function getPostCategories(int $postId): array
    {
        return $this->db->table('post_categories pc')
            ->select('c.*')
            ->join('categories c', 'c.id = pc.category_id')
            ->where('pc.post_id', $postId)
            ->where('c.deleted_at', null)
            ->get()
            ->getResult();
    }

    public function getPostTags(int $postId): array
    {
        return $this->db->table('post_tags pt')
            ->select('t.*')
            ->join('tags t', 't.id = pt.tag_id')
            ->where('pt.post_id', $postId)
            ->where('t.deleted_at', null)
            ->get()
            ->getResult();
    }

    public function saveRelations(int $postId, array $categoryIds = [], array $tagIds = [])
    {
        // 1. Save Categories
        $this->db->table('post_categories')->where('post_id', $postId)->delete();
        if (! empty($categoryIds)) {
            $catData = [];
            foreach ($categoryIds as $catId) {
                $catData[] = [
                    'post_id'     => $postId,
                    'category_id' => $catId,
                ];
            }
            $this->db->table('post_categories')->insertBatch($catData);
        }

        // 2. Save Tags
        $this->db->table('post_tags')->where('post_id', $postId)->delete();
        if (! empty($tagIds)) {
            $tagData = [];
            foreach ($tagIds as $tagId) {
                $tagData[] = [
                    'post_id' => $postId,
                    'tag_id'  => $tagId,
                ];
            }
            $this->db->table('post_tags')->insertBatch($tagData);
        }
    }

    public function getPostsWithRelations(array $where = [], ?int $limit = null, ?int $offset = null): array
    {
        $builder = $this->db->table('posts')
            ->select('posts.*, u.full_name as author_name')
            ->join('users u', 'u.id = posts.author_id', 'left')
            ->where('posts.deleted_at', null);

        if (! empty($where)) {
            $builder->where($where);
        }

        $builder->orderBy('posts.id', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        $posts = $builder->get()->getResult();

        foreach ($posts as $post) {
            $post->categories = $this->getPostCategories((int) $post->id);
            $post->tags       = $this->getPostTags((int) $post->id);
        }

        return $posts;
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

<?php

namespace App\Controllers\Portal;

use App\Controllers\BaseController;
use App\Models\PostModel;

class News extends BaseController
{
    protected PostModel $postModel;
    protected int $perPage = 9;

    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    public function index()
    {
        helper(['educms']);

        $page   = (int) $this->request->getGet('page');
        $page   = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $this->perPage;

        $where = ['posts.status' => 'published'];
        $total = $this->postModel->where($where)->where('deleted_at', null)->countAllResults();
        $posts = $this->postModel->getPostsWithRelations($where, $this->perPage, $offset);

        $seoMeta = $this->loadSeo('posts_index', 'Berita dan artikel terbaru seputar sekolah.');

        $data = [
            'title'        => 'Berita' . (' | ' . site_name()),
            'posts'        => $posts,
            'current_page' => $page,
            'total_pages'  => max(1, (int) ceil($total / $this->perPage)),
            'seo_meta'     => $seoMeta,
        ];

        return view('portal/news_index', $data);
    }

    public function detail($slug)
    {
        helper(['educms']);

        $post = $this->postModel
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $post || $post->status !== 'published') {
            $data = ['title' => 'Berita Tidak Ditemukan'];
            return $this->response->setStatusCode(404)->setBody(view('portal/page_not_found', $data));
        }

        // Increment view counter
        $db = \Config\Database::connect();
        $db->table('posts')->where('id', $post->id)->set('view_count', 'view_count+1', false)->update();

        if (! isset($post->author_name) && ! empty($post->author_id)) {
            $author = $db->table('users')->select('full_name')->where('id', $post->author_id)->get()->getRow();
            $post->author_name = $author->full_name ?? null;
        }

        $post->categories = $this->postModel->getPostCategories((int) $post->id);
        $post->tags       = $this->postModel->getPostTags((int) $post->id);

        // Navigation for Previous/Next post
        $prevPost = $this->postModel
            ->where('status', 'published')
            ->where('id !=', $post->id)
            ->where('created_at <', $post->created_at)
            ->orderBy('created_at', 'DESC')
            ->first();

        $nextPost = $this->postModel
            ->where('status', 'published')
            ->where('id !=', $post->id)
            ->where('created_at >', $post->created_at)
            ->orderBy('created_at', 'ASC')
            ->first();

        $seoMeta = $this->loadSeo('posts_' . $post->id, strip_tags($post->content));

        $data = [
            'title'     => $post->title . (' | ' . site_name()),
            'post'      => $post,
            'prev_post' => $prevPost,
            'next_post' => $nextPost,
            'seo_meta'  => $seoMeta,
        ];

        return view('portal/news_detail', $data);
    }

    protected function loadSeo(string $pageName, string $fallbackDescription = ''): array
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_settings')->where('page_name', $pageName)->get()->getRow();

        return [
            'meta_title'       => $seo->meta_title ?? '',
            'meta_description' => $seo->meta_description ?? $fallbackDescription,
            'keywords'         => $seo->keywords ?? '',
            'og_title'         => $seo->og_title ?? '',
            'og_description'   => $seo->og_description ?? '',
            'og_image'         => $seo->og_image ?? '',
            'canonical_url'    => $seo->canonical_url ?? '',
        ];
    }
}

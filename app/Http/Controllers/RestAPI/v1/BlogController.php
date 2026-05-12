<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function blog(Request $request)
    {

        $languageId = $request->query('languageId');

        $query = Blog::query()
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->select('posts.*', DB::raw('COUNT(comments.id) as comment_count'))
            ->where('posts.status', 1)
            ->groupBy('posts.id');

        if ($languageId) {

            $query->where('lang_id', $languageId);
        }

        $blog_posts = $query->get();

        return response()->json([
            'status' => 200,
            'data' => $blog_posts,
        ]);
    }


    public function getBlogBySlug($title_slug)
    {
        // Single Blog with comment count
        $blog_post = Blog::leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->select('posts.*', DB::raw('COUNT(comments.id) as comment_count'))
            ->where('posts.title_slug', $title_slug)
            ->where('posts.status', 1)
            ->groupBy('posts.id')
            ->first();
    
        // Most viewed blogs (top 10 by hit)
        $most_viewed_blogs = Blog::where('status', 1)
            ->orderBy('hit', 'desc')
            ->limit(10)
            ->get();
    
        // Latest blogs (top 10 by created_at)
        $latest_blogs = Blog::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    
        if ($blog_post) {
            return response()->json([
                'status' => 200,
                'data' => $blog_post,
                'most_viewed_blogs' => $most_viewed_blogs,
                'latest_blogs' => $latest_blogs,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Blog post not found',
                'most_viewed_blogs' => $most_viewed_blogs,
                'latest_blogs' => $latest_blogs,
            ]);
        }
    }
    
    
    public function getBlogByCategory(Request $request)
    {
        $languageId = $request->query('languageId');
        $categoryId = $request->query('categoryId');
    
        $query = Blog::query()
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->select(
                'posts.id',
                'posts.title',
                'posts.title_slug',
                'posts.created_at',
                'posts.hit',
                'posts.image_big',
                'posts.image_small',
                'posts.image_mid',
                'posts.image_slider',
                DB::raw('COUNT(comments.id) as comment_count')
            )
            ->where('posts.status', 1)
            ->groupBy(
                'posts.id',
                'posts.title',
                'posts.title_slug',
                'posts.created_at',
                'posts.hit',
                'posts.image_big',
                'posts.image_small',
                'posts.image_mid',
                'posts.image_slider'
            )
            ->orderByDesc('posts.hit'); 
    
        if ($languageId) {
            $query->where('posts.lang_id', $languageId);
        }
    
        if ($categoryId) {
            $query->where('posts.category_id', $categoryId);
        }
    
        $blog_posts = $query->get();
    
        if ($blog_posts->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No blog posts found',
            ]);
        }
    
        $baseUrl = url('blog');
    
        $blog_posts->each(function ($post) use ($baseUrl) {
            $post->image_big = $baseUrl . '/' . ltrim($post->image_big, '/');
            $post->image_small = $baseUrl . '/' . ltrim($post->image_small, '/');
            $post->image_mid = $baseUrl . '/' . ltrim($post->image_mid, '/');
            $post->image_slider = $baseUrl . '/' . ltrim($post->image_slider, '/');
        });
    
        return response()->json([
            'status' => 200,
            'data' => $blog_posts,
        ]);
    }
    

    public function getBlogCategory(Request $request)
    {

        $languageId = $request->query('languageId');

        if (!$languageId) {
            return response()->json([
                'status' => 400,
                'message' => 'languageId is required',
            ]);
        }

        $categories = DB::connection('mysql2')->table('categories')
            ->select('id', 'lang_id', 'name', 'category_order')
            ->where('lang_id', $languageId)
            ->get();

        return response()->json([
            'status' => 200,
            'categories' => $categories,
        ]);
    }
}

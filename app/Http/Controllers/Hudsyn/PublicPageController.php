<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\Page;
use App\Hudsyn\Blog;
use App\Hudsyn\PressRelease;
use App\Hudsyn\CustomRoute;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class PublicPageController extends Controller
{
    /**
     * Display a published page, or if the requested URL matches a custom route, load that content.
     *
     * @param  string|null  $slug
     * @return \Illuminate\Http\Response
     */
    public function showPage($slug = null)
    {
        // Get the full requested path (without query parameters).
        $requestedPath = '/' . request()->path(); // e.g., "/about-us"
        
        // Check if a custom route exists for this URL.
        $customRoute = CustomRoute::where('route', $requestedPath)->first();
        if ($customRoute) {
            switch ($customRoute->content_type) {
                case 'page':
                    $content = Page::find($customRoute->content_id);
                    $folder = 'pages';
                    $viewName = 'public.page';
                    break;
                case 'blog':
                    $content = Blog::find($customRoute->content_id);
                    $folder = 'blog';
                    $viewName = 'public.blog';
                    break;
                case 'press_release':
                    $content = PressRelease::find($customRoute->content_id);
                    $folder = 'press';
                    $viewName = 'public.press';
                    break;
                default:
                    abort(404, 'Content not found.');
            }
            if (!$content || $content->status !== 'published') {
                abort(404, 'Content not published.');
            }
            $filePath = public_path("static/{$folder}/{$content->slug}.html");
            if (File::exists($filePath)) {
                return Response::file($filePath);
            }
            return view($viewName, [str_replace('public.', '', $viewName) => $content]);
        }

        // If no slug provided, load the homepage.
        if (is_null($slug)) {
            $page = Page::where('is_homepage', true)
                        ->where('status', 'published')
                        ->first();
            if (!$page) {
                abort(404, 'Homepage not found.');
            }
        } else {
            $page = Page::where('slug', $slug)
                        ->where('status', 'published')
                        ->first();
            if (!$page) {
                abort(404, 'Page not found.');
            }
        }

        $filePath = public_path("static/pages/{$page->slug}.html");
        if (File::exists($filePath)) {
            return Response::file($filePath);
        }
        return view('public.page', compact('page'));
    }

    /**
     * Display a published blog post.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function showBlogPost($slug)
    {
        $blog = Blog::where('slug', $slug)
                    ->where('status', 'published')
                    ->first();

        if (!$blog) {
            abort(404, 'Blog post not found.');
        }

        $filePath = public_path("static/blog/{$blog->slug}.html");

        if (File::exists($filePath)) {
            return Response::file($filePath);
        }

        return view('public.blog', compact('blog'));
    }

    /**
     * Display a published press release.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function showPressRelease($slug)
    {
        $press = PressRelease::where('slug', $slug)
                             ->where('status', 'published')
                             ->first();

        if (!$press) {
            abort(404, 'Press release not found.');
        }

        $filePath = public_path("static/press/{$press->slug}.html");

        if (File::exists($filePath)) {
            return Response::file($filePath);
        }

        return view('public.press', compact('press'));
    }
}

<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\Blog;
use App\Hudsyn\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index()
    {
        $blogs = Blog::with('author')->get();
        return view('hudsyn.blog.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        $authors = User::all();
        return view('hudsyn.blog.create', compact('authors'));
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:hud_blog,slug',
            'content'   => 'required|string',
            'status'    => 'required|in:draft,published',
            'author_id' => 'required|exists:hud_users,id',
        ]);

        // Convert the provided slug to a URL-friendly version.
        $slug = Str::slug($request->slug);

        $blog = Blog::create([
            'title'        => $request->title,
            'slug'         => $slug,
            'content'      => $request->content,
            'status'       => $request->status,
            'published_at' => $request->status === 'published' ? Carbon::now() : null,
            'author_id'    => $request->author_id,
        ]);

        // Generate a static file if the blog post is published.
        if ($blog->status === 'published') {
            $this->generateStaticFile($blog);
        }

        return redirect()->route('hudsyn.blog.index')
                         ->with('success', 'Blog post created successfully.');
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $authors = User::all();
        return view('hudsyn.blog.edit', compact('blog', 'authors'));
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title'     => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:hud_blog,slug,' . $blog->id,
            'content'   => 'required|string',
            'status'    => 'required|in:draft,published',
            'author_id' => 'required|exists:hud_users,id',
        ]);

        // Convert the provided slug to a URL-friendly version.
        $slug = Str::slug($request->slug);

        $blog->update([
            'title'        => $request->title,
            'slug'         => $slug,
            'content'      => $request->content,
            'status'       => $request->status,
            'published_at' => $request->status === 'published' ? Carbon::now() : null,
            'author_id'    => $request->author_id,
        ]);

        // Regenerate the static file if the blog post is published.
        if ($blog->status === 'published') {
            $this->generateStaticFile($blog);
        }

        return redirect()->route('hudsyn.blog.index')
                         ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        // Optionally remove the static file.
        $filePath = public_path("static/blog/{$blog->slug}.html");
        if (File::exists($filePath)) {
            unlink($filePath);
        }

        $blog->delete();

        return redirect()->route('hudsyn.blog.index')
                         ->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Generate a static HTML file for a published blog post.
     *
     * @param Blog $blog
     */
    protected function generateStaticFile(Blog $blog)
    {
        // Render the public view for the blog post.
        $html = view('public.blog', compact('blog'))->render();

        // Define the file path based on the slug.
        $filePath = public_path("static/blog/{$blog->slug}.html");

        // Ensure the directory exists.
        if (!File::exists(public_path('static/blog'))) {
            File::makeDirectory(public_path('static/blog'), 0755, true);
        }

        // Write the static file.
        file_put_contents($filePath, $html);
    }
}

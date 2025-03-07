<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index()
    {
        $pages = Page::all();
        return view('hudsyn.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('hudsyn.pages.create');
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'required|string|max:255|unique:hud_pages,slug',
            'content'           => 'required|string',
            'status'            => 'required|in:draft,published',
            'layout_header'     => 'nullable|string|max:255',
            'layout_footer'     => 'nullable|string|max:255',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
        ]);

        // Convert the slug to a URL-friendly version
        $slug = Str::slug($request->slug);

        // If this page is set as homepage, remove homepage flag from other pages.
        $isHomepage = $request->has('is_homepage');
        if ($isHomepage) {
            Page::where('is_homepage', true)->update(['is_homepage' => false]);
        }

        $page = Page::create([
            'title'             => $request->title,
            'slug'              => $slug,  // Use the URL-friendly slug
            'content'           => $request->content,
            'status'            => $request->status,
            'is_homepage'       => $isHomepage,
            'layout_header'     => $request->layout_header,
            'layout_footer'     => $request->layout_footer,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
            'published_at'      => $request->status === 'published' ? now() : null,
        ]);

        if ($page->status === 'published') {
            $this->generateStaticFile($page);
        }

        return redirect()->route('hudsyn.pages.index')
                         ->with('success', 'Page created successfully.');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('hudsyn.pages.edit', compact('page'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'title'             => 'required|string|max:255',
            'slug'              => 'required|string|max:255|unique:hud_pages,slug,' . $page->id,
            'content'           => 'required|string',
            'status'            => 'required|in:draft,published',
            'layout_header'     => 'nullable|string|max:255',
            'layout_footer'     => 'nullable|string|max:255',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
        ]);

        // Generate a URL-friendly slug.
        $slug = Str::slug($request->slug);

        $isHomepage = $request->has('is_homepage');
        if ($isHomepage) {
            Page::where('is_homepage', true)
                ->where('id', '!=', $page->id)
                ->update(['is_homepage' => false]);
        }

        $page->update([
            'title'             => $request->title,
            'slug'              => $slug,  // Use the URL-friendly slug
            'content'           => $request->content,
            'status'            => $request->status,
            'is_homepage'       => $isHomepage,
            'layout_header'     => $request->layout_header,
            'layout_footer'     => $request->layout_footer,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
            'published_at'      => $request->status === 'published' ? now() : null,
        ]);

        if ($page->status === 'published') {
            $this->generateStaticFile($page);
        }

        return redirect()->route('hudsyn.pages.index')
                         ->with('success', 'Page updated successfully.');
    }


    /**
     * Remove the specified page from storage.
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);

        // Optionally remove the static file from the server.
        if ($page->static_file_path && file_exists(public_path($page->static_file_path))) {
            unlink(public_path($page->static_file_path));
        }

        $page->delete();

        return redirect()->route('hudsyn.pages.index')
                         ->with('success', 'Page deleted successfully.');
    }

    /**
     * Generate a static HTML file for the published page.
     *
     * This example renders a public Blade view and writes it to a file.
     */
    protected function generateStaticFile(Page $page)
    {
        // Render the public view with the page data.
        $html = view('public.page', compact('page'))->render();

        // Define a filename based on the slug.
        $fileName = $page->slug . '.html';
        $filePath = 'static/pages/' . $fileName;

        // Ensure the directory exists.
        if (!File::exists(public_path('static/pages'))) {
            File::makeDirectory(public_path('static/pages'), 0755, true);
        }

        // Write the static file.
        file_put_contents(public_path($filePath), $html);

        // Save the static file path.
        $page->static_file_path = $filePath;
        $page->save();
    }
}

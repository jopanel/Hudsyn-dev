<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\PressRelease;
use App\Hudsyn\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PressReleaseController extends Controller
{
    /**
     * Display a listing of press releases.
     */
    public function index()
    {
        $pressReleases = PressRelease::with('author')->get();
        return view('hudsyn.press-releases.index', compact('pressReleases'));
    }

    /**
     * Show the form for creating a new press release.
     */
    public function create()
    {
        $authors = User::all();
        return view('hudsyn.press-releases.create', compact('authors'));
    }

    /**
     * Store a newly created press release.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:hud_press_releases,slug',
            'content'   => 'required|string',
            'status'    => 'required|in:draft,published',
            'author_id' => 'required|exists:hud_users,id',
        ]);

        // Convert the slug into a URL-friendly format.
        $slug = Str::slug($request->slug);

        $pressRelease = PressRelease::create([
            'title'        => $request->title,
            'slug'         => $slug,
            'content'      => $request->content,
            'status'       => $request->status,
            'published_at' => $request->status === 'published' ? Carbon::now() : null,
            'author_id'    => $request->author_id,
        ]);

        // Generate a static file if published.
        if ($pressRelease->status === 'published') {
            $this->generateStaticFile($pressRelease);
        }

        return redirect()->route('hudsyn.press-releases.index')
                         ->with('success', 'Press release created successfully.');
    }

    /**
     * Show the form for editing the specified press release.
     */
    public function edit($id)
    {
        $pressRelease = PressRelease::findOrFail($id);
        $authors = User::all();
        return view('hudsyn.press-releases.edit', compact('pressRelease', 'authors'));
    }

    /**
     * Update the specified press release.
     */
    public function update(Request $request, $id)
    {
        $pressRelease = PressRelease::findOrFail($id);

        $request->validate([
            'title'     => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:hud_press_releases,slug,' . $pressRelease->id,
            'content'   => 'required|string',
            'status'    => 'required|in:draft,published',
            'author_id' => 'required|exists:hud_users,id',
        ]);

        $slug = Str::slug($request->slug);

        $pressRelease->update([
            'title'        => $request->title,
            'slug'         => $slug,
            'content'      => $request->content,
            'status'       => $request->status,
            'published_at' => $request->status === 'published' ? Carbon::now() : null,
            'author_id'    => $request->author_id,
        ]);

        // Regenerate the static file if published.
        if ($pressRelease->status === 'published') {
            $this->generateStaticFile($pressRelease);
        }

        return redirect()->route('hudsyn.press-releases.index')
                         ->with('success', 'Press release updated successfully.');
    }

    /**
     * Remove the specified press release.
     */
    public function destroy($id)
    {
        $pressRelease = PressRelease::findOrFail($id);
        $filePath = public_path("static/press/{$pressRelease->slug}.html");
        if (File::exists($filePath)) {
            unlink($filePath);
        }
        $pressRelease->delete();

        return redirect()->route('hudsyn.press-releases.index')
                         ->with('success', 'Press release deleted successfully.');
    }

    /**
     * Generate a static HTML file for a published press release.
     *
     * @param PressRelease $pressRelease
     */
    protected function generateStaticFile(PressRelease $pressRelease)
    {
        // Render the public view for the press release.
        $html = view('public.press', compact('pressRelease'))->render();

        // Define the file path based on the slug.
        $filePath = public_path("static/press/{$pressRelease->slug}.html");

        // Ensure the directory exists.
        if (!File::exists(public_path('static/press'))) {
            File::makeDirectory(public_path('static/press'), 0755, true);
        }

        // Write the static file.
        file_put_contents($filePath, $html);
    }
}

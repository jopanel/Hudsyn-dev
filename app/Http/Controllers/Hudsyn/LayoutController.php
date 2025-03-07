<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\Layout;

class LayoutController extends Controller
{
    /**
     * Display a listing of layouts.
     */
    public function index()
    {
        $layouts = Layout::all();
        return view('hudsyn.layouts.index', compact('layouts'));
    }

    /**
     * Show the form for creating a new layout.
     */
    public function create()
    {
        return view('hudsyn.layouts.create');
    }

    /**
     * Store a newly created layout.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'header_file'  => 'required|string|max:255',
            'footer_file'  => 'required|string|max:255',
        ]);

        Layout::create($request->only(['name', 'header_file', 'footer_file']));

        return redirect()->route('hudsyn.layouts.index')
                         ->with('success', 'Layout created successfully.');
    }

    /**
     * Show the form for editing the specified layout.
     */
    public function edit($id)
    {
        $layout = Layout::findOrFail($id);
        return view('hudsyn.layouts.edit', compact('layout'));
    }

    /**
     * Update the specified layout.
     */
    public function update(Request $request, $id)
    {
        $layout = Layout::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'header_file'  => 'required|string|max:255',
            'footer_file'  => 'required|string|max:255',
        ]);

        $layout->update($request->only(['name', 'header_file', 'footer_file']));

        return redirect()->route('hudsyn.layouts.index')
                         ->with('success', 'Layout updated successfully.');
    }

    /**
     * Remove the specified layout.
     */
    public function destroy($id)
    {
        $layout = Layout::findOrFail($id);
        $layout->delete();

        return redirect()->route('hudsyn.layouts.index')
                         ->with('success', 'Layout deleted successfully.');
    }
}

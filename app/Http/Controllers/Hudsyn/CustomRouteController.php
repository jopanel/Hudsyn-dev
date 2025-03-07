<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\CustomRoute;

class CustomRouteController extends Controller
{
    /**
     * Display a listing of custom routes.
     */
    public function index()
    {
        $customRoutes = CustomRoute::all();
        return view('hudsyn.custom-routes.index', compact('customRoutes'));
    }

    /**
     * Show the form for creating a new custom route.
     */
    public function create()
    {
        return view('hudsyn.custom-routes.create');
    }

    /**
     * Store a newly created custom route.
     */
    public function store(Request $request)
    {
        $request->validate([
            'route'        => 'required|string|unique:hud_custom_routes,route',
            // Allowed values: page, blog, press_release.
            'content_type' => 'required|in:page,blog,press_release',
            'content_id'   => 'required|integer',
        ]);

        // Optionally ensure the route starts with a slash.
        $routePath = $request->route;
        if (substr($routePath, 0, 1) !== '/') {
            $routePath = '/' . $routePath;
        }

        CustomRoute::create([
            'route'        => $routePath,
            'content_type' => $request->content_type,
            'content_id'   => $request->content_id,
        ]);

        return redirect()->route('hudsyn.custom-routes.index')
                         ->with('success', 'Custom route created successfully.');
    }

    /**
     * Show the form for editing the specified custom route.
     */
    public function edit($id)
    {
        $customRoute = CustomRoute::findOrFail($id);
        return view('hudsyn.custom-routes.edit', compact('customRoute'));
    }

    /**
     * Update the specified custom route.
     */
    public function update(Request $request, $id)
    {
        $customRoute = CustomRoute::findOrFail($id);

        $request->validate([
            'route'        => 'required|string|unique:hud_custom_routes,route,' . $customRoute->id,
            'content_type' => 'required|in:page,blog,press_release',
            'content_id'   => 'required|integer',
        ]);

        $routePath = $request->route;
        if (substr($routePath, 0, 1) !== '/') {
            $routePath = '/' . $routePath;
        }

        $customRoute->update([
            'route'        => $routePath,
            'content_type' => $request->content_type,
            'content_id'   => $request->content_id,
        ]);

        return redirect()->route('hudsyn.custom-routes.index')
                         ->with('success', 'Custom route updated successfully.');
    }

    /**
     * Remove the specified custom route.
     */
    public function destroy($id)
    {
        $customRoute = CustomRoute::findOrFail($id);
        $customRoute->delete();

        return redirect()->route('hudsyn.custom-routes.index')
                         ->with('success', 'Custom route deleted successfully.');
    }
}

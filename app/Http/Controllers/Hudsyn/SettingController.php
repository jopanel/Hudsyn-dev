<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index()
    {
        $settings = Setting::all();
        return view('hudsyn.settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new setting.
     */
    public function create()
    {
        return view('hudsyn.settings.create');
    }

    /**
     * Store a newly created setting.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key'   => 'required|string|max:255|unique:hud_settings,key',
            'value' => 'required|string',
        ]);

        Setting::create($request->only(['key', 'value']));

        return redirect()->route('hudsyn.settings.index')
                         ->with('success', 'Setting added successfully.');
    }

    /**
     * Show the form for editing the specified setting.
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        return view('hudsyn.settings.edit', compact('setting'));
    }

    /**
     * Update the specified setting.
     */
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $request->validate([
            'key'   => 'required|string|max:255|unique:hud_settings,key,' . $setting->id,
            'value' => 'required|string',
        ]);

        $setting->update($request->only(['key', 'value']));

        return redirect()->route('hudsyn.settings.index')
                         ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified setting.
     */
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();

        return redirect()->route('hudsyn.settings.index')
                         ->with('success', 'Setting deleted successfully.');
    }
}

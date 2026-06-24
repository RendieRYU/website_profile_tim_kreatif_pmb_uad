<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token']);
        if ($request->hasFile('team_photo')) {
            $path = $request->file('team_photo')->store('settings', 'public');
            $data['team_photo'] = $path;
        }
        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            $data['site_logo'] = $path;
        }
        if ($request->hasFile('instagram_image')) {
            $path = $request->file('instagram_image')->store('settings', 'public');
            $data['instagram_image'] = $path;
        }
        if ($request->hasFile('tiktok_image')) {
            $path = $request->file('tiktok_image')->store('settings', 'public');
            $data['tiktok_image'] = $path;
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}

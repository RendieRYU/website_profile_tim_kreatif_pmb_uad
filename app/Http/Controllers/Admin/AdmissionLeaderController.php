<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionLeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdmissionLeaderController extends Controller
{
    public function index()
    {
        $leaders = AdmissionLeader::orderBy('order')->get();
        return view('admin.admission_leaders.index', compact('leaders'));
    }

    public function create()
    {
        return view('admin.admission_leaders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $validated['order'] = $validated['order'] ?? 0;

        if ($request->filled('cropped_photo')) {
            $image_parts = explode(";base64,", $request->cropped_photo);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'admission_leaders/' . Str::uuid() . '.jpg';
                Storage::disk('public')->put($fileName, $image_base64);
                $validated['photo'] = $fileName;
            }
        } elseif ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('admission_leaders', 'public');
        }

        AdmissionLeader::create($validated);

        return redirect()->route('admin.admission-leaders.index')->with('success', 'Pimpinan Admisi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $leader = AdmissionLeader::findOrFail($id);
        return view('admin.admission_leaders.edit', compact('leader'));
    }

    public function update(Request $request, $id)
    {
        $leader = AdmissionLeader::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $validated['order'] = $validated['order'] ?? 0;

        if ($request->filled('cropped_photo')) {
            $image_parts = explode(";base64,", $request->cropped_photo);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'admission_leaders/' . Str::uuid() . '.jpg';
                Storage::disk('public')->put($fileName, $image_base64);
                $validated['photo'] = $fileName;
            }
        } elseif ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('admission_leaders', 'public');
        }

        $leader->update($validated);

        return redirect()->route('admin.admission-leaders.index')->with('success', 'Pimpinan Admisi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $leader = AdmissionLeader::findOrFail($id);
        $leader->delete();

        return redirect()->route('admin.admission-leaders.index')->with('success', 'Pimpinan Admisi berhasil dihapus.');
    }
}

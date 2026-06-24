<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;

class DivisionController extends Controller
{
    public function index() { return view('admin.divisions.index', ['divisions' => Division::all()]); }
    public function create() { return view('admin.divisions.create'); }
    public function store(Request $request) {
        $validated = $request->validate(['name' => 'required', 'color_hex' => 'required']);
        Division::create($validated);
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }
    public function edit(Division $division) { return view('admin.divisions.edit', compact('division')); }
    public function update(Request $request, Division $division) {
        $validated = $request->validate(['name' => 'required', 'color_hex' => 'required']);
        $division->update($validated);
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }
    public function destroy(Division $division) {
        $division->delete();
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }
}

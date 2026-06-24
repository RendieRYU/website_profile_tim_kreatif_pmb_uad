<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Period;
use App\Models\Division;

class MemberController extends Controller
{
    public function index() { 
        $members = Member::with(['period', 'division'])->latest()->paginate(10);
        return view('admin.members.index', compact('members')); 
    }
    public function create() { 
        $periods = Period::all();
        $divisions = Division::all();
        return view('admin.members.create', compact('periods', 'divisions')); 
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'full_name' => 'required', 'nickname' => 'nullable', 'role' => 'nullable',
            'period_id' => 'required|exists:periods,id', 'division_id' => 'required|exists:divisions,id',
            'linkedin' => 'nullable|url', 'instagram' => 'nullable|url', 'github' => 'nullable|url',
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('members', 'public');
        }
        Member::create($validated);
        return redirect()->route('admin.members.index')->with('success', 'Anggota berhasil ditambahkan.');
    }
    public function edit(Member $member) { 
        $periods = Period::all();
        $divisions = Division::all();
        return view('admin.members.edit', compact('member', 'periods', 'divisions')); 
    }
    public function update(Request $request, Member $member) {
        $validated = $request->validate([
            'full_name' => 'required', 'nickname' => 'nullable', 'role' => 'nullable',
            'period_id' => 'required|exists:periods,id', 'division_id' => 'required|exists:divisions,id',
            'linkedin' => 'nullable|url', 'instagram' => 'nullable|url', 'github' => 'nullable|url',
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('members', 'public');
        }
        $member->update($validated);
        return redirect()->route('admin.members.index')->with('success', 'Anggota berhasil diperbarui.');
    }
    public function destroy(Member $member) {
        $member->delete();
        return redirect()->route('admin.members.index')->with('success', 'Anggota berhasil dihapus.');
    }
}

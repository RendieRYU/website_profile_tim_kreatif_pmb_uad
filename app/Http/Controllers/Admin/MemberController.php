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
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->route('admin.dashboard')->with('error', 'Belum ada periode yang aktif.');
        }
        
        $query = Member::with(['period', 'division'])->where('period_id', $activePeriod->id);
        
        $sort = request('sort', 'name');
        if ($sort === 'division') {
            $members = $query->leftJoin('divisions', 'members.division_id', '=', 'divisions.id')
                ->orderBy('divisions.name', 'asc')
                ->orderBy('full_name', 'asc')
                ->select('members.*')
                ->get();
        } else {
            $members = $query->orderBy('full_name', 'asc')->get();
        }
        
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
            'linkedin' => 'nullable|url', 'instagram' => 'nullable|url',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        if ($request->filled('cropped_photo')) {
            $image_parts = explode(";base64,", $request->cropped_photo);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'members/' . \Illuminate\Support\Str::uuid() . '.jpg';
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
                $validated['photo'] = $fileName;
            }
        } elseif ($request->hasFile('photo')) {
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
            'linkedin' => 'nullable|url', 'instagram' => 'nullable|url',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        if ($request->filled('cropped_photo')) {
            $image_parts = explode(";base64,", $request->cropped_photo);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'members/' . \Illuminate\Support\Str::uuid() . '.jpg';
                \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $image_base64);
                $validated['photo'] = $fileName;
            }
        } elseif ($request->hasFile('photo')) {
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

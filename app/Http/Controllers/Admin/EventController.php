<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Member;
use App\Models\Category;

class EventController extends Controller
{
    public function index() { 
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->route('admin.dashboard')->with('error', 'Belum ada periode yang aktif. Silakan aktifkan salah satu periode terlebih dahulu.');
        }

        $events = Event::where('period_id', $activePeriod->id)->latest('event_date')->paginate(10);
        return view('admin.events.index', compact('events')); 
    }
    public function create() { 
        $members = Member::with('division')->get();
        $categories = Category::all();
        return view('admin.events.create', compact('members', 'categories')); 
    }
    public function store(Request $request) {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Tidak dapat menambahkan kegiatan: belum ada periode aktif.');
        }

        $validated = $request->validate([
            'title' => 'required', 'description' => 'nullable|string', 'event_date' => 'required|date', 'event_time' => 'nullable|date_format:H:i', 'link' => 'nullable|url',
            'status' => 'required|in:ide,ditunda,selesai'
        ]);
        
        $validated['period_id'] = $activePeriod->id;
        
        $event = Event::create($validated);
        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $memberId) {
                $member = Member::find($memberId);
                if ($member) $syncData[$memberId] = ['division_id' => $member->division_id];
            }
            $event->members()->sync($syncData);
        }
        if ($request->has('categories')) {
            $event->categories()->sync($request->categories);
        }
        return redirect()->route('admin.events.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }
    public function edit(Event $event) { 
        $members = Member::with('division')->get();
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'members', 'categories')); 
    }
    public function update(Request $request, Event $event) {
        $validated = $request->validate([
            'title' => 'required', 'description' => 'nullable|string', 'event_date' => 'required|date', 'event_time' => 'nullable', 'link' => 'nullable|url',
            'status' => 'required|in:ide,ditunda,selesai'
        ]);
        $event->update($validated);
        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $memberId) {
                $member = Member::find($memberId);
                if ($member) $syncData[$memberId] = ['division_id' => $member->division_id];
            }
            $event->members()->sync($syncData);
        } else {
            $event->members()->detach();
        }
        if ($request->has('categories')) {
            $event->categories()->sync($request->categories);
        } else {
            $event->categories()->detach();
        }
        return redirect()->route('admin.events.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }
    public function destroy(Event $event) {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}

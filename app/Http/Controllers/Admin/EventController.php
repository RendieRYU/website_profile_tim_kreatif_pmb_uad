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

        $events = Event::with(['members.division', 'categories'])->where('period_id', $activePeriod->id)->latest('event_date')->paginate(15);
        $allEvents = Event::with(['members.division', 'categories'])->where('period_id', $activePeriod->id)->get();
        $divisions = \App\Models\Division::all()->keyBy('id');

        $allEvents->transform(function($event) use ($divisions) {
            $event->members->transform(function($member) use ($divisions) {
                $pivotDivisionId = $member->pivot->division_id ?? $member->division_id;
                $member->pivot_division = $divisions->get($pivotDivisionId);
                $colors = [
                    'Inti' => '#8b5cf6',
                    'Creative' => '#ec4899',
                    'Programmer' => '#3b82f6',
                    'Media' => '#f59e0b',
                ];
                $divName = $member->pivot_division ? $member->pivot_division->name : '';
                $member->color = $colors[$divName] ?? '#64748b';
                return $member;
            });
            $event->formatted_time = $event->event_time ? $event->event_time->format('H:i') : null;
            return $event;
        });

        return view('admin.events.index', compact('events', 'allEvents', 'activePeriod')); 
    }
    public function create(Request $request) { 
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $members = $activePeriod ? Member::with('division')->where('period_id', $activePeriod->id)->get() : collect();
        $categories = Category::all();
        $selectedDate = $request->query('date', date('Y-m-d'));
        return view('admin.events.create', compact('members', 'categories', 'selectedDate')); 
    }
    public function store(Request $request) {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Tidak dapat menambahkan kegiatan: belum ada periode aktif.');
        }

        $validated = $request->validate([
            'title' => 'required', 'description' => 'nullable|string', 'event_date' => 'required|date', 'event_time' => 'nullable|date_format:H:i', 'link' => 'nullable|url',
            'status' => 'required|in:ide,acc,selesai,ditunda,terjadwal',
            'links' => 'nullable|array',
            'links.*.title' => 'nullable|string',
            'links.*.url' => 'nullable|url'
        ]);
        
        $cleanLinks = [];
        if ($request->has('links') && is_array($request->links)) {
            foreach ($request->links as $l) {
                if (!empty($l['url'])) {
                    $cleanLinks[] = [
                        'title' => !empty($l['title']) ? $l['title'] : 'Tautan',
                        'url' => $l['url']
                    ];
                }
            }
        }
        $validated['links'] = $cleanLinks;
        if (empty($validated['link']) && !empty($cleanLinks)) {
            $validated['link'] = $cleanLinks[0]['url'];
        }

        $validated['period_id'] = $activePeriod->id;
        
        $event = Event::create($validated);
        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $memberId) {
                $member = Member::find($memberId);
                if ($member) {
                    $role = $request->member_roles[$memberId] ?? null;
                    $syncData[$memberId] = [
                        'division_id' => $member->division_id,
                        'role' => $role
                    ];
                }
            }
            $event->members()->sync($syncData);
        }
        if ($request->has('categories')) {
            $event->categories()->sync($request->categories);
        }
        return redirect()->route('admin.events.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }
    public function edit(Event $event) { 
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $members = $activePeriod ? Member::with('division')->where('period_id', $activePeriod->id)->get() : collect();
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'members', 'categories')); 
    }
    public function update(Request $request, Event $event) {
        $validated = $request->validate([
            'title' => 'required', 'description' => 'nullable|string', 'event_date' => 'required|date', 'event_time' => 'nullable', 'link' => 'nullable|url',
            'status' => 'required|in:ide,acc,selesai,ditunda,terjadwal',
            'links' => 'nullable|array',
            'links.*.title' => 'nullable|string',
            'links.*.url' => 'nullable|url'
        ]);

        $cleanLinks = [];
        if ($request->has('links') && is_array($request->links)) {
            foreach ($request->links as $l) {
                if (!empty($l['url'])) {
                    $cleanLinks[] = [
                        'title' => !empty($l['title']) ? $l['title'] : 'Tautan',
                        'url' => $l['url']
                    ];
                }
            }
        }
        $validated['links'] = $cleanLinks;
        if (empty($validated['link']) && !empty($cleanLinks)) {
            $validated['link'] = $cleanLinks[0]['url'];
        }

        $event->update($validated);
        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $memberId) {
                $member = Member::find($memberId);
                if ($member) {
                    $role = $request->member_roles[$memberId] ?? null;
                    $syncData[$memberId] = [
                        'division_id' => $member->division_id,
                        'role' => $role
                    ];
                }
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

    public function export() {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Belum ada periode aktif untuk ekspor.');
        }

        $events = Event::with(['categories', 'members'])->where('period_id', $activePeriod->id)->orderBy('event_date', 'asc')->get();

        $filename = "Kalender_Kegiatan_{$activePeriod->name}.xls";

        return response(view('admin.events.excel', compact('events'))->render(), 200, [
            "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}

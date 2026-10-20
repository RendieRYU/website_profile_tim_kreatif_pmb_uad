<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Member;
use App\Models\Category;
use App\Models\Period;

class ProjectController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->route('admin.dashboard')->with('error', 'Belum ada periode yang aktif.');
        }

        $events = Event::with(['members.division', 'categories'])
            ->where('period_id', $activePeriod->id)
            ->orderBy('event_date', 'asc')
            ->get();

        $divisions = \App\Models\Division::all()->keyBy('id');

        $events->transform(function($event) use ($divisions) {
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

        $members = Member::with('division')->where('period_id', $activePeriod->id)->get();
        $categories = Category::all();

        return view('admin.projects.index', compact('events', 'activePeriod', 'members', 'categories'));
    }

    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'column' => 'required|in:todo,ongoing,complete',
        ]);

        $column = $request->column;
        $newStatus = 'terjadwal';

        if ($column === 'todo') {
            // Keep status as ide/terjadwal/ditunda/ditolak if already set, or default to terjadwal
            $newStatus = in_array($event->status, ['ide', 'ditunda', 'ditolak']) ? $event->status : 'terjadwal';
        } elseif ($column === 'ongoing') {
            $newStatus = 'acc';
        } elseif ($column === 'complete') {
            $newStatus = 'selesai';
        }

        $event->status = $newStatus;
        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Status projek "' . $event->title . '" berhasil diperbarui ke ' . strtoupper($column) . ' (' . strtoupper($newStatus) . ')!',
            'status' => $newStatus
        ]);
    }
}

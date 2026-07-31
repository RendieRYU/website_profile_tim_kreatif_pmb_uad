<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;

class EventApiController extends Controller
{
    public function getMonthEvents($year, $month)
    {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $periodId = $activePeriod ? $activePeriod->id : 0;

        $events = Event::with(['members.division', 'categories'])
            ->where('period_id', $periodId)
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->get();
            
        $divisions = \App\Models\Division::all()->keyBy('id');

        $events->transform(function($event) use ($divisions) {
            $event->members->transform(function($member) use ($divisions) {
                // Attach the pivot division explicitly
                $pivotDivisionId = $member->pivot->division_id ?? $member->division_id;
                $member->pivot_division = $divisions->get($pivotDivisionId);
                // Assign color based on division name or id.
                $colors = [
                    'Inti' => '#8b5cf6', // purple
                    'Creative' => '#ec4899', // pink
                    'Programmer' => '#3b82f6', // blue
                    'Media' => '#f59e0b', // amber
                ];
                $divName = $member->pivot_division ? $member->pivot_division->name : '';
                $member->color = $colors[$divName] ?? '#64748b'; // default slate

                return $member;
            });
            $event->formatted_time = $event->event_time ? $event->event_time->format('H:i') : null;
            return $event;
        });

        return response()->json($events);
    }

    public function updateLink(Request $request, Event $event)
    {
        $request->validate([
            'pin' => 'required',
            'title' => 'nullable|string',
            'url' => 'required|url',
        ]);

        $correctPin = \App\Models\Setting::get('calendar_link_pin', '123456');

        if (trim($request->pin) !== trim($correctPin)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode Konfirmasi (PIN) salah! Silakan hubungi Admin.'
            ], 422);
        }

        $links = is_array($event->links) ? $event->links : [];
        if ($event->link && empty($links)) {
            $links[] = ['title' => 'Tautan Utama', 'url' => $event->link];
        }

        $links[] = [
            'title' => $request->title ?: 'Tautan Tambahan',
            'url' => $request->url
        ];

        $event->links = $links;
        if (empty($event->link)) {
            $event->link = $request->url;
        }
        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Tautan kegiatan berhasil diperbarui!',
            'links' => $event->links
        ]);
    }
}

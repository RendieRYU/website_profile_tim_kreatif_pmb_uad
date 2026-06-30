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
}

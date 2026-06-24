<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;

class EventApiController extends Controller
{
    public function getMonthEvents($year, $month)
    {
        $events = Event::with('members.division')
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->get();
            
        $divisions = \App\Models\Division::all()->keyBy('id');

        $events->transform(function($event) use ($divisions) {
            $event->members->transform(function($member) use ($divisions) {
                // Attach the pivot division explicitly
                $pivotDivisionId = $member->pivot->division_id ?? $member->division_id;
                $member->pivot_division = $divisions->get($pivotDivisionId);
                return $member;
            });
            $event->formatted_time = $event->event_time ? $event->event_time->format('H:i') : null;
            return $event;
        });

        return response()->json($events);
    }
}

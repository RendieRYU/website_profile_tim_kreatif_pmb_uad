<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Period;
use App\Models\Category;

class PublicProjectController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            $activePeriod = Period::latest()->first();
        }

        $events = collect();
        if ($activePeriod) {
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
        }

        return view('pages.projects', compact('events', 'activePeriod'));
    }
}

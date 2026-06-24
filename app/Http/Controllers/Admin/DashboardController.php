<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Member;
use App\Models\Event;
use App\Models\News;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'members' => Member::count(),
            'events' => Event::count(),
            'news' => News::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}

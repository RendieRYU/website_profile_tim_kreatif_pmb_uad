<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\NoteFolder;

class PublicNoteController extends Controller
{
    public function index()
    {
        $folders = NoteFolder::withCount('notes')
            ->orderBy('order', 'asc')
            ->get();

        $notes = Note::where('is_archived', false)
            ->with('folder')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('order', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('pages.notes', compact('folders', 'notes'));
    }
}

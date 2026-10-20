<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\NoteFolder;

class NoteController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $folders = NoteFolder::where('user_id', $userId)
            ->withCount('notes')
            ->orderBy('order', 'asc')
            ->get();

        $notes = Note::where('user_id', $userId)
            ->where('is_archived', false)
            ->with('folder')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('order', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.notes.index', compact('folders', 'notes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'folder_id' => 'nullable|exists:note_folders,id',
            'color' => 'nullable|string',
            'note_date' => 'nullable|date',
            'is_pinned' => 'nullable|boolean'
        ]);

        $validated['user_id'] = auth()->id();
        $validated['color'] = $validated['color'] ?? '#FEF3C7';
        $validated['note_date'] = $validated['note_date'] ?? date('Y-m-d');
        $validated['is_pinned'] = $request->has('is_pinned');

        $note = Note::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Catatan berhasil dibuat!', 'note' => $note->load('folder')]);
        }

        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil ditambahkan!');
    }

    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'folder_id' => 'nullable|exists:note_folders,id',
            'color' => 'nullable|string',
            'note_date' => 'nullable|date',
            'is_pinned' => 'nullable|boolean'
        ]);

        $validated['is_pinned'] = $request->has('is_pinned');
        $note->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Catatan berhasil diperbarui!', 'note' => $note->load('folder')]);
        }

        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil diperbarui!');
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Catatan berhasil dihapus.']);
        }

        return redirect()->route('admin.notes.index')->with('success', 'Catatan berhasil dihapus!');
    }

    public function storeFolder(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string'
        ]);

        $validated['user_id'] = auth()->id();
        $validated['color'] = $validated['color'] ?? '#E0F2FE';

        $folder = NoteFolder::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Folder berhasil dibuat!', 'folder' => $folder]);
        }

        return redirect()->route('admin.notes.index')->with('success', 'Folder baru berhasil dibuat!');
    }

    public function destroyFolder(NoteFolder $folder)
    {
        if ($folder->user_id !== auth()->id()) {
            abort(403);
        }

        $folder->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Folder berhasil dihapus.']);
        }

        return redirect()->route('admin.notes.index')->with('success', 'Folder berhasil dihapus!');
    }

    public function togglePin(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->is_pinned = !$note->is_pinned;
        $note->save();

        return response()->json(['success' => true, 'is_pinned' => $note->is_pinned]);
    }

    public function moveNote(Request $request, Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'folder_id' => 'nullable|exists:note_folders,id'
        ]);

        $note->folder_id = $validated['folder_id'] ?? null;
        $note->save();

        return response()->json(['success' => true, 'message' => 'Catatan berhasil dipindahkan ke folder!']);
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|array',
            'notes.*.id' => 'required|exists:notes,id',
            'notes.*.order' => 'required|integer'
        ]);

        if (isset($validated['notes'])) {
            foreach ($validated['notes'] as $item) {
                Note::where('id', $item['id'])->where('user_id', auth()->id())->update(['order' => $item['order']]);
            }
        }

        return response()->json(['success' => true]);
    }
}

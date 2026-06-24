<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Member;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index() { 
        $news = News::latest('published_date')->paginate(10);
        return view('admin.news.index', compact('news')); 
    }
    public function create() { 
        $members = Member::all();
        return view('admin.news.create', compact('members')); 
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required', 'content' => 'required', 'published_date' => 'required|date',
        ]);
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('news', 'public');
        }
        $news = News::create($validated);
        if ($request->has('members')) {
            $news->members()->sync($request->members);
        }
        return redirect()->route('admin.news.index')->with('success', 'Berita/Portofolio berhasil ditambahkan.');
    }
    public function edit(News $news) { 
        $members = Member::all();
        return view('admin.news.edit', compact('news', 'members')); 
    }
    public function update(Request $request, News $news) {
        $validated = $request->validate([
            'title' => 'required', 'content' => 'required', 'published_date' => 'required|date',
        ]);
        if ($validated['title'] !== $news->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('news', 'public');
        }
        $news->update($validated);
        if ($request->has('members')) {
            $news->members()->sync($request->members);
        } else {
            $news->members()->detach();
        }
        return redirect()->route('admin.news.index')->with('success', 'Berita/Portofolio berhasil diperbarui.');
    }
    public function destroy(News $news) {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Berita/Portofolio berhasil dihapus.');
    }
}

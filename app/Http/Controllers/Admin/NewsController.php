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
        return view('admin.news.create'); 
    }
    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required', 'content' => 'required', 'published_date' => 'required|date',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('news', 'public');
        }
        $news = News::create($validated);
        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil ditambahkan.');
    }
    public function edit(News $news) { 
        return view('admin.news.edit', compact('news')); 
    }
    public function update(Request $request, News $news) {
        $validated = $request->validate([
            'title' => 'required', 'content' => 'required', 'published_date' => 'required|date',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        if ($validated['title'] !== $news->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }
        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('news', 'public');
        }
        $news->update($validated);
        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }
    public function destroy(News $news) {
        $news->delete();
        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }
}

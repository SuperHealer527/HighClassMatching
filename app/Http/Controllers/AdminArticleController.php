<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index() { return view('admin.articles.index', ['articles' => Article::latest()->paginate(20)]); }
    public function create() { return view('admin.articles.form', ['article' => new Article]); }
    public function edit(Article $article) { return view('admin.articles.form', compact('article')); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['author_id'] = auth()->id();
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['published_at'] = $data['status'] === 'published' ? now() : null;
        Article::create($data);
        return redirect()->route('admin.articles.index')->with('status', '記事を作成しました。');
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->validated($request);
        if ($article->title !== $data['title']) $data['slug'] = $this->uniqueSlug($data['title'], $article->id);
        $data['published_at'] = $data['status'] === 'published' ? ($article->published_at ?: now()) : null;
        $article->update($data);
        return redirect()->route('admin.articles.index')->with('status', '記事を更新しました。');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'max:255'], 'category' => ['required', 'in:interview,knowledge,case_study,news'],
            'excerpt' => ['required', 'max:1000'], 'body' => ['required'], 'cover_image_path' => ['nullable', 'max:255'],
            'status' => ['required', 'in:draft,published'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $except = null): string
    {
        $base = Str::slug($title) ?: 'article'; $slug = $base; $number = 2;
        while (Article::where('slug', $slug)->when($except, fn ($q) => $q->where('id', '!=', $except))->exists()) $slug = $base.'-'.$number++;
        return $slug;
    }
}

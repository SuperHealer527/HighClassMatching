<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        return view('articles.index', ['articles' => Article::published()->latest('published_at')->paginate(12)]);
    }

    public function show(Article $article)
    {
        abort_unless($article->status === 'published' && $article->published_at && $article->published_at->lte(now()), 404);
        $relatedArticles = Article::published()->where('id', '!=', $article->id)
            ->where('category', $article->category)->latest('published_at')->take(3)->get();
        if ($relatedArticles->isEmpty()) {
            $relatedArticles = Article::published()->where('id', '!=', $article->id)->latest('published_at')->take(3)->get();
        }
        return view('articles.show', compact('article', 'relatedArticles'));
    }
}

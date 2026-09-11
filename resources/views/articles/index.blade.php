@extends('layouts.user')
@section('content')
<div class="page-heading"><div><div class="eyebrow">JOURNAL</div><h1>指導と地域スポーツの知見</h1></div></div>
<div class="article-grid">@foreach($articles as $article)<article class="article-card"><a href="{{ route('articles.show',$article) }}"><img src="{{ asset($article->cover_image_path ?: 'images/sports-analysis-interview.png') }}" alt=""><div><span class="eyebrow">{{ strtoupper($article->category) }}</span><h2>{{ $article->title }}</h2><p>{{ $article->excerpt }}</p><span class="meta">{{ $article->published_at->format('Y.m.d') }}</span></div></a></article>@endforeach</div>{{ $articles->links() }}
@endsection

@extends('layouts.user')
@section('content')
<article class="article-detail modern-article">
    <header class="article-header"><div class="article-header-meta"><span class="eyebrow">{{ strtoupper($article->category) }}</span><span>{{ $article->published_at->format('Y.m.d') }}</span></div><h1>{{ $article->title }}</h1><p class="article-lead">{{ $article->excerpt }}</p></header>
    <figure class="article-figure"><img class="article-hero" src="{{ asset($article->cover_image_path ?: 'images/sports-analysis-interview.png') }}" alt="{{ $article->title }}"><figcaption>HIGH CLASS MATCHING JOURNAL</figcaption></figure>
    <div class="article-reading"><aside><span>CONTENTS</span><strong>地域スポーツの現場から</strong><p>指導者、学校、地域団体の実践と知見を紹介します。</p></aside><div class="article-body">{!! nl2br(e($article->body)) !!}</div></div>
</article>
<aside class="article-service-ad"><div><div class="eyebrow">FROM KNOWLEDGE TO ACTION</div><h2>記事で得た知見を、<br>現場の指導へ。</h2><p>競技と地域から、課題に合う専門家を探せます。</p></div><div><a class="btn" href="{{ route('coaches.index') }}">指導者を探す</a><a href="{{ route('register') }}">無料会員登録 →</a></div></aside>
@if($relatedArticles->count())<section class="workspace-section related-section"><div class="section-heading"><div><div class="eyebrow">RELATED JOURNAL</div><h2>あわせて読みたい記事</h2></div><a href="{{ route('articles.index') }}">記事一覧へ</a></div><div class="article-grid">@foreach($relatedArticles as $item)<article class="article-card"><a href="{{ route('articles.show',$item) }}"><img src="{{ asset($item->cover_image_path ?: 'images/sports-analysis-interview.png') }}" alt=""><div><span class="eyebrow">{{ strtoupper($item->category) }}</span><h3>{{ $item->title }}</h3><p>{{ $item->excerpt }}</p><span class="meta">{{ $item->published_at->format('Y.m.d') }}</span></div></a></article>@endforeach</div></section>@endif
@include('partials.matching-promo',['title'=>'知ることから、つながることへ。','text'=>'地域スポーツを支える専門家と、指導を必要とする学校・団体の出会いをつくります。'])
@endsection

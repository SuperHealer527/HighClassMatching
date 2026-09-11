@extends('layouts.user')
@section('content')
<div class="page-heading"><div><div class="eyebrow">SPORTS COMMUNITY</div><h1>学校・団体を探す</h1></div><p class="result-count"><strong>{{ number_format($organizations->total()) }}</strong> 団体</p></div>
<div class="result-grid organization-results">
@forelse($organizations as $organization)
@php($image = $organization->image_path ? (str_starts_with($organization->image_path,'images/') ? asset($organization->image_path) : asset('storage/'.$organization->image_path)) : asset($loop->even ? 'images/track-coaching.png' : 'images/school-team-practice.png'))
<article class="result-card organization-card"><a class="result-image landscape" href="{{ route('organizations.show',$organization) }}"><img src="{{ $image }}" alt="{{ $organization->name }}"><span class="entity-image-label">SPORTS COMMUNITY</span></a><div class="result-card-body"><div class="page-actions"><span class="badge status">{{ $organization->main_prefecture }}</span><span class="badge">{{ $organization->sport }}</span></div><h2><a href="{{ route('organizations.show',$organization) }}">{{ $organization->name }}</a></h2><p>{{ Str::limit($organization->introduction ?: '地域に根ざしたスポーツ活動を行っています。', 90) }}</p><p class="meta">{{ $organization->area }} / {{ $organization->target_age }} / {{ number_format($organization->member_count) }}名</p><div class="organization-card-foot"><span>公開案件</span><strong>{{ $organization->jobs_count }}</strong></div><a class="entity-card-link" href="{{ route('organizations.show',$organization) }}">団体情報を見る <span>→</span></a></div></article>
@empty<div class="empty-state">公開中の団体はありません。</div>@endforelse
</div>{{ $organizations->links() }}
<section class="wide-cta"><div><div class="eyebrow">FIND YOUR COACH</div><h2>地域と専門家をつなぐ、新しい部活動へ。</h2></div><a class="btn" href="{{ route('register') }}">団体として無料登録</a></section>
@endsection

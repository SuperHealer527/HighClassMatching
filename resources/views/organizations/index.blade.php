@extends('layouts.user')
@section('content')
<div class="page-heading"><div><div class="eyebrow">SPORTS COMMUNITY</div><h1>チーム・部活を探す</h1></div><p class="result-count"><strong>{{ number_format($organizations->total()) }}</strong> 団体</p></div>
<form class="organization-search-panel" method="get" action="{{ route('organizations.index') }}" aria-label="都道府県と競技からチーム・部活を検索">
    <div class="organization-search-copy">
        <span>SEARCH DIRECTORY</span>
        <strong>都道府県と競技から検索</strong>
    </div>
    <label>
        <span>都道府県</span>
        <select class="field" name="prefecture">
            <option value="">全国から選択</option>
            @foreach($prefectures as $prefecture)
                <option value="{{ $prefecture }}" {{ request('prefecture') === $prefecture ? 'selected' : '' }}>{{ $prefecture }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span>競技</span>
        <select class="field" name="sport">
            <option value="">すべての競技</option>
            @foreach($sports as $sport)
                <option value="{{ $sport }}" {{ request('sport') === $sport ? 'selected' : '' }}>{{ $sport }}</option>
            @endforeach
        </select>
    </label>
    <div class="organization-search-actions">
        <button class="btn dark" type="submit">検索する</button>
        @if(request()->filled('prefecture') || request()->filled('sport'))
            <a href="{{ route('organizations.index') }}">条件をクリア</a>
        @endif
    </div>
</form>
<div class="result-grid organization-results">
@forelse($organizations as $organization)
@php($image = $organization->image_path ? (str_starts_with($organization->image_path,'images/') ? asset($organization->image_path) : asset('storage/'.$organization->image_path)) : asset($loop->even ? 'images/track-coaching.png' : 'images/school-team-practice.png'))
<article class="result-card organization-card"><a class="result-image landscape" href="{{ route('organizations.show',$organization) }}"><img src="{{ $image }}" alt="{{ $organization->name }}"><span class="entity-image-label">SPORTS COMMUNITY</span></a><div class="result-card-body"><div class="page-actions"><span class="badge status">{{ $organization->main_prefecture }}</span><span class="badge">{{ $organization->sport }}</span></div><h2><a href="{{ route('organizations.show',$organization) }}">{{ $organization->name }}</a></h2><p>{{ Str::limit($organization->introduction ?: '地域に根ざしたスポーツ活動を行っています。', 90) }}</p><p class="meta">{{ $organization->area }} / {{ $organization->target_age }} / {{ number_format($organization->member_count) }}名</p><div class="organization-card-foot"><span>公開案件</span><strong>{{ $organization->jobs_count }}</strong></div><a class="entity-card-link" href="{{ route('organizations.show',$organization) }}">チーム・部活情報を見る <span>→</span></a></div></article>
@empty<div class="empty-state">公開中の団体はありません。</div>@endforelse
</div>{{ $organizations->links() }}
<section class="wide-cta"><div><div class="eyebrow">FIND YOUR COACH</div><h2>地域と専門家をつなぐ、新しい部活動へ。</h2></div><a class="btn" href="{{ route('register') }}">チーム・部活として無料登録</a></section>
@endsection

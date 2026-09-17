@extends('layouts.user')
@section('content')
@php
    $photo = $coach->photo_path ? (\Illuminate\Support\Str::startsWith($coach->photo_path, 'images/') ? asset($coach->photo_path) : asset('storage/'.$coach->photo_path)) : asset('images/sample-coach-profile.png');
    $education = $coach->education_history ?: array_filter([$coach->degree]);
    $qualifications = $coach->qualification_items ?: array_slice(preg_split('/[、,\r\n]+/u', (string) $coach->qualifications, -1, PREG_SPLIT_NO_EMPTY), 0, 2);
    $teachingAchievements = $coach->teaching_achievements ?: array_filter(preg_split('/\r\n|\r|\n/u', (string) $coach->achievements, -1, PREG_SPLIT_NO_EMPTY));
    $requestAchievements = $coach->request_achievements ?: array_filter(preg_split('/\r\n|\r|\n/u', (string) $coach->request_history, -1, PREG_SPLIT_NO_EMPTY));
    $recommendations = $coach->recommendations ?: ($coach->recommended_athlete ? [['name' => $coach->recommended_athlete, 'introduction' => '']] : []);
    $mediatedOfferUrl = route('inquiries.create', ['coach' => $coach->id, 'mode' => 'mediated']);
@endphp

<section class="detail-hero profile-detail-hero compact-profile-hero">
    <div class="detail-hero-media"><img src="{{ $photo }}" alt="{{ $coach->name }}"></div>
    <div class="detail-hero-content">
        <div class="eyebrow">COACH PROFILE</div>
        <div class="detail-badges"><span class="badge status">{{ $coach->verification_status === 'verified' ? '本人・資格確認済み' : '確認手続き中' }}</span><span class="badge">{{ $coach->main_prefecture }}</span></div>
        <h1>{{ $coach->name }}</h1>
        <p class="detail-subtitle">{{ $coach->kana }} @if($coach->roman_name)<span>/ {{ $coach->roman_name }}</span>@endif</p>
        <p class="detail-message">{{ $coach->affiliation ?: 'フリーランス指導者' }}</p>
        <div class="hero-metrics"><div><strong>{{ count((array) $coach->fields) }}</strong><span>専門分野</span></div><div><strong>{{ max(1, count((array) $coach->available_prefectures)) }}</strong><span>対応地域</span></div><div><strong>{{ $coach->completeness_score }}%</strong><span>プロフィール充実度</span></div></div>
    </div>
</section>

<div class="detail-actionbar profile-actionbar">
    <div><span>分野</span><strong>{{ implode(' / ', (array) $coach->fields) ?: '未設定' }}</strong></div>
    <div><span>専門競技</span><strong>{{ implode(' / ', (array) $coach->sports) ?: '未設定' }}</strong></div>
    <div class="detail-actions">
        @auth
            @if(auth()->user()->isOrganization() && auth()->user()->organization)
                <form method="post" action="{{ route('favorites.toggle', $coach) }}">@csrf<button class="btn secondary" type="submit">保存する</button></form>
                @if($coach->direct_offer_enabled)<a class="btn" href="{{ route('offers.create', $coach) }}">直接オファー</a>@endif
                <a class="btn mediated-btn" href="{{ $mediatedOfferUrl }}">事務局を通じて相談</a>
            @elseif(auth()->user()->isAdmin())
                <a class="btn mediated-btn" href="{{ $mediatedOfferUrl }}">事務局対応を登録</a>
            @endif
        @else
            <a class="btn" href="{{ route('register') }}">無料登録して相談する</a>
        @endauth
    </div>
</div>

<div class="coach-profile-content">
    <section class="detail-section profile-compact-section">
        <div class="detail-section-title"><span>01</span><div><div class="eyebrow">PROFILE</div><h2>基本情報</h2></div></div>
        <dl class="coach-basic-grid">
            <div><dt>所属</dt><dd>{{ $coach->affiliation ?: '未設定' }}</dd></div>
            <div><dt>分野</dt><dd>{{ implode(' / ', (array) $coach->fields) ?: '未設定' }}</dd></div>
            <div><dt>専門競技</dt><dd>{{ implode(' / ', (array) $coach->sports) ?: '未設定' }}</dd></div>
            <div><dt>学歴</dt><dd>@forelse($education as $item)<span class="profile-list-line">{{ $item }}</span>@empty 未設定 @endforelse</dd></div>
            <div><dt>資格</dt><dd>@forelse($qualifications as $index => $item)<span class="profile-list-line"><b>{{ $index === 0 ? '①' : '②' }}</b>{{ $item }}</span>@empty 未設定 @endforelse</dd></div>
            <div><dt>対応可能地域</dt><dd>{{ implode(' / ', $coach->available_prefectures ?: [$coach->main_prefecture]) }}</dd></div>
            <div><dt>現在の拠点</dt><dd>{{ $coach->main_prefecture }}{{ $coach->area ? ' / '.$coach->area : '' }}</dd></div>
            <div><dt>最終更新日時</dt><dd>{{ optional($coach->profile_updated_at ?: $coach->updated_at)->format('Y.m.d H:i') }}</dd></div>
        </dl>
    </section>

    <section class="detail-section profile-compact-section">
        <div class="detail-section-title"><span>02</span><div><div class="eyebrow">VOICE & MESSAGE</div><h2>評価とメッセージ</h2></div></div>
        <div class="recommendation-grid">
            @forelse($recommendations as $recommendation)
                <article class="recommendation-card"><span>RECOMMENDATION</span><h3>{{ $recommendation['name'] ?: '推薦者' }}</h3><p>{{ $recommendation['introduction'] ?: 'この指導者を推薦します。' }}</p></article>
            @empty
                <div class="profile-empty">推薦コメントは準備中です。</div>
            @endforelse
        </div>
        <div class="coach-message-block"><span>MESSAGE</span><p class="preline">{{ $coach->message ?: '指導に関するご相談をお待ちしています。' }}</p></div>
    </section>

    <section class="detail-section profile-compact-section profile-offer-section">
        <div class="detail-section-title"><span>03</span><div><div class="eyebrow">AVAILABLE REQUESTS</div><h2>オファー可能なご依頼について</h2></div></div>
        <div class="offer-condition"><span>直接オファー</span><strong>{{ $coach->direct_offer_enabled ? '受付中' : '事務局を通じたご相談のみ' }}</strong><p>{{ $coach->desired_fee_range ?: '内容・日程・費用はご相談ください。' }}</p></div>
        <div class="achievement-columns">
            <div><h3>指導実績</h3><ol>@forelse($teachingAchievements as $item)<li>{{ $item }}</li>@empty<li>実績情報は準備中です。</li>@endforelse</ol></div>
            <div><h3>依頼実績</h3><ol>@forelse($requestAchievements as $item)<li>{{ $item }}</li>@empty<li>依頼実績は準備中です。</li>@endforelse</ol></div>
        </div>
    </section>

    @if($coach->keywords)<div class="profile-keywords"><span>KEYWORDS</span><p>{{ $coach->keywords }}</p></div>@endif

    <section class="profile-bottom-offer">
        <div><span class="eyebrow">START A CONVERSATION</span><h2>{{ $coach->name }}さんに相談する</h2><p>依頼内容が固まっていなくても、事務局が条件整理をサポートします。</p></div>
        <div class="profile-bottom-actions">
            @auth
                @if(auth()->user()->isOrganization() && auth()->user()->organization)
                    @if($coach->direct_offer_enabled)<a class="btn" href="{{ route('offers.create', $coach) }}">直接オファーする</a>@endif
                    <a class="btn mediated-btn" href="{{ $mediatedOfferUrl }}">事務局を通じてオファー</a>
                @elseif(auth()->user()->isAdmin())
                    <a class="btn mediated-btn" href="{{ $mediatedOfferUrl }}">事務局対応を登録</a>
                @else
                    <a class="btn" href="{{ route('inquiries.create', ['coach' => $coach->id]) }}">この指導者について相談</a>
                @endif
            @else
                <a class="btn" href="{{ route('register') }}">無料登録してオファー</a><a class="btn mediated-btn" href="{{ route('login') }}">ログインして事務局に相談</a>
            @endauth
        </div>
    </section>
</div>
@endsection

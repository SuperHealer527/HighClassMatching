@extends('layouts.user')
@section('content')
@php
$photo = $coach->photo_path ? (\Illuminate\Support\Str::startsWith($coach->photo_path, 'images/') ? asset($coach->photo_path) : asset('storage/'.$coach->photo_path)) : asset('images/sample-coach-profile.png');
$education = $coach->education_history ?: [];
$otherAffiliations = $coach->other_affiliations ?: [];
$qualifications = $coach->qualification_items ?: array_slice(preg_split('/[、,\r\n]+/u', (string) $coach->qualifications, -1, PREG_SPLIT_NO_EMPTY), 0, 2);
$teachingAchievements = $coach->teaching_achievements ?: array_filter(preg_split('/\r\n|\r|\n/u', (string) $coach->achievements, -1, PREG_SPLIT_NO_EMPTY));
$requestAchievements = $coach->request_achievements ?: array_filter(preg_split('/\r\n|\r|\n/u', (string) $coach->request_history, -1, PREG_SPLIT_NO_EMPTY));
$recommendations = $coach->recommendations ?: ($coach->recommended_athlete ? [['name' => $coach->recommended_athlete, 'introduction' => '']] : []);
$recommendationFallbacks = ['images/school-team-practice-color.png', 'images/sports-coaching-hero-color.png', 'images/sports-analysis-interview.png'];
$mediatedOfferUrl = route('inquiries.create', ['coach' => $coach->id, 'mode' => 'mediated']);
@endphp

<section class="detail-hero profile-detail-hero compact-profile-hero">
    <div class="detail-hero-media"><img src="{{ $photo }}" alt="{{ $coach->name }}"></div>
    <div class="detail-hero-content">
        <div class="eyebrow">COACH PROFILE</div>
        <div class="detail-badges">@forelse(array_slice((array) $coach->fields, 0, 1) as $field)<span class="badge status">{{ $field }}</span>@empty<span class="badge status">専門分野未設定</span>@endforelse<span class="badge">{{ $coach->main_prefecture }}</span>@if($coach->is_student)<span class="student-badge">学生</span>@endif</div>
        <h1>{{ $coach->name }}</h1>
        <p class="detail-subtitle">{{ $coach->kana }} @if($coach->roman_name)<span>/ {{ $coach->roman_name }}</span>@endif</p>
        <p class="detail-message">{{ $coach->affiliation ?: 'フリーランス指導者' }}</p>
        <div class="hero-metrics">
            <div><strong>{{ min(1, count((array) $coach->fields)) }}</strong><span>専門分野</span></div>
            <div><strong>{{ max(1, count((array) $coach->available_prefectures)) }}</strong><span>対応地域</span></div>
            <div><strong>{{ $coach->completeness_score }}%</strong><span>プロフィール<br>充実度</span></div>
        </div>
    </div>
</section>

<div class="coach-profile-content">
    <section class="detail-section profile-compact-section">
        <div class="detail-section-title"><span>01</span>
            <div>
                <div class="eyebrow">PROFILE</div>
                <h2>基本情報</h2>
            </div>
        </div>
        <dl class="coach-basic-grid">
            <div>
                <dt>所属</dt>
                <dd>{{ $coach->affiliation ?: '未設定' }}</dd>
            </div>
            <div>
                <dt>その他の所属</dt>
                <dd>@forelse($otherAffiliations as $item)<span class="profile-list-line">{{ $item }}</span>@empty 未設定 @endforelse</dd>
            </div>
            <div>
                <dt>学歴</dt>
                <dd>@forelse($education as $item)<span class="profile-list-line">{{ $item }}</span>@empty 未設定 @endforelse</dd>
            </div>
            <div>
                <dt>学位</dt>
                <dd>{{ $coach->degree ?: '未設定' }}</dd>
            </div>
            <div>
                <dt>分野</dt>
                <dd>{{ collect((array) $coach->fields)->first() ?: '未設定' }}</dd>
            </div>
            <div>
                <dt>専門競技</dt>
                <dd>{{ implode(' / ', (array) $coach->sports) ?: '未設定' }}</dd>
            </div>
            <div>
                <dt>資格</dt>
                <dd>@forelse($qualifications as $index => $item)<span class="profile-list-line"><b>{{ $index === 0 ? '①' : '②' }}</b>{{ $item }}</span>@empty 未設定 @endforelse</dd>
            </div>
            <div>
                <dt>対応可能地域</dt>
                <dd>{{ implode(' / ', $coach->available_prefectures ?: [$coach->main_prefecture]) }}</dd>
            </div>
            <div>
                <dt>登録区分</dt>
                <dd>{{ $coach->is_student ? '学生' : '一般・社会人' }}</dd>
            </div>
            <div>
                <dt>現在の拠点</dt>
                <dd>{{ $coach->main_prefecture }}{{ $coach->area ? ' / '.$coach->area : '' }}</dd>
            </div>
            <div>
                <dt>最終更新日時</dt>
                <dd>{{ optional($coach->profile_updated_at ?: $coach->updated_at)->format('Y.m.d H:i') }}</dd>
            </div>
        </dl>
    </section>

    <section class="profile-records-panel" aria-label="指導実績と依頼実績">
        <div class="achievement-columns">
            <div>
                <h3>指導実績</h3>
                <ol>@forelse($teachingAchievements as $item)<li>{{ $item }}</li>@empty<li>実績情報は準備中です。</li>@endforelse</ol>
            </div>
            <div>
                <h3>依頼実績</h3>
                <ol>@forelse($requestAchievements as $item)<li>{{ $item }}</li>@empty<li>依頼実績は準備中です。</li>@endforelse</ol>
            </div>
        </div>
        @if($coach->keywords)<div class="profile-keywords"><span>KEYWORDS</span><p>{{ $coach->keywords }}</p></div>@endif
    </section>

    <section class="detail-section profile-compact-section">
        <div class="detail-section-title"><span>02</span>
            <div>
                <div class="eyebrow">VOICE & MESSAGE</div>
                <h2>評価とメッセージ</h2>
            </div>
        </div>
        <div class="recommendation-grid">
            @forelse($recommendations as $recommendation)
            @php
                $recommendationImagePath = $recommendation['image_path'] ?? null;
                $recommendationImage = $recommendationImagePath
                    ? (str_starts_with($recommendationImagePath, 'images/') ? asset($recommendationImagePath) : asset('storage/'.$recommendationImagePath))
                    : asset($recommendationFallbacks[$loop->index % count($recommendationFallbacks)]);
            @endphp
            <article class="recommendation-card"><img class="recommendation-card-image" src="{{ $recommendationImage }}" alt="{{ $recommendation['name'] ?: '推薦者' }}"><div class="recommendation-card-body"><span>RECOMMENDATION</span>
                <h3>{{ $recommendation['name'] ?: '推薦者' }}</h3>
                <p>{{ $recommendation['introduction'] ?: 'この指導者を推薦します。' }}</p>
            </div></article>
            @empty
            <div class="profile-empty">推薦コメントは準備中です。</div>
            @endforelse
        </div>
        <div class="coach-message-block"><span>INTRODUCTION</span>
            <p class="preline">{{ $coach->message ?: '指導に関するご相談をお待ちしています。' }}</p>
        </div>
    </section>

    <section class="detail-section profile-compact-section profile-offer-section">
        <div class="detail-section-title"><span>03</span>
            <div>
                <div class="eyebrow">AVAILABLE REQUESTS</div>
                <h2>オファー可能なご依頼について</h2>
            </div>
        </div>
        <div class="offer-condition"><span>直接オファー</span><strong>{{ $coach->direct_offer_enabled ? '受付中' : '事務局へ相談' }}</strong>
            <p>{{ $coach->direct_offer_enabled ? '送信内容は指導者本人と事務局の両方へ通知されます。' : '事務局へ相談内容が通知され、条件整理と指導者への確認をサポートします。' }}<br>{{ $coach->desired_fee_range ?: '内容・日程・費用はご相談ください。' }}</p>
        </div>
    </section>

    <section class="profile-bottom-offer">
        <div><span class="eyebrow">START A CONVERSATION</span>
            <h2>{{ $coach->name }}さんに相談する</h2>
            <p>依頼内容が固まっていなくても、事務局が条件整理をサポートします。</p>
        </div>
        <div class="profile-bottom-actions">
            @auth
            @if(auth()->user()->isOrganization() && auth()->user()->organization)
            @if($coach->direct_offer_enabled)<a class="btn" href="{{ route('offers.create', $coach) }}">直接オファーする</a>@endif
            <a class="btn mediated-btn" href="{{ $mediatedOfferUrl }}">事務局へ相談</a>
            @elseif(auth()->user()->isAdmin())
            <a class="btn mediated-btn" href="{{ $mediatedOfferUrl }}">事務局相談を登録</a>
            @else
            <a class="btn" href="{{ route('inquiries.create', ['coach' => $coach->id]) }}">事務局へ相談</a>
            @endif
            @else
            @if($coach->direct_offer_enabled)<a class="btn" href="{{ route('register') }}">無料登録してオファー</a>@endif<a class="btn mediated-btn" href="{{ route('login') }}">ログインして事務局へ相談</a>
            @endauth
        </div>
    </section>
</div>
@endsection

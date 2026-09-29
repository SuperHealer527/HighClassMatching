@extends('layouts.user')
@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">COACH DIRECTORY</div>
        <h1>指導者を探す</h1>
    </div>
    <p class="result-count"><strong>{{ number_format($coaches->total()) }}</strong> 名</p>
</div>
<form class="form search-filter" method="get" action="{{ route('coaches.index') }}">
    <div class="grid3"><select class="field" name="prefecture">
            <option value="">都道府県・対応地域</option>@foreach($prefectures as $pref)<option value="{{ $pref }}" {{ request('prefecture') === $pref ? 'selected' : '' }}>{{ $pref }}</option>@endforeach
        </select><select class="field" name="field">
            <option value="">専門項目</option>@foreach($fields as $field)<option value="{{ $field }}" {{ request('field') === $field ? 'selected' : '' }}>{{ $field }}</option>@endforeach
        </select><input class="field" name="sport" value="{{ request('sport') }}" placeholder="競技"></div>
    <div class="grid3"><input class="field" name="area" value="{{ request('area') }}" placeholder="市区町村・エリア"><input class="field" name="keyword" value="{{ request('keyword') }}" placeholder="資格・実績・キーワード"><select class="field" name="sort">
            <option value="">更新順</option>
            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>評価順</option>
            <option value="complete" {{ request('sort') === 'complete' ? 'selected' : '' }}>プロフィール充実順</option>
        </select></div>
    <div class="filter-footer"><div class="coach-filter-checks"><label class="inline-check"><input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }}> 本人・資格確認済みのみ</label><label class="inline-check"><input type="checkbox" name="student" value="1" {{ request('student') ? 'checked' : '' }}> 学生のみ</label></div>
        <div class="page-actions"><select class="field compact-field" name="per_page">
                <option value="10">10件</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20件</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50件</option>
            </select><button class="btn dark" type="submit">条件を絞り込む</button></div>
    </div>
</form>
<div class="result-grid coach-results">
    @forelse($coaches as $coach)
    @php($photo = $coach->photo_path ? (str_starts_with($coach->photo_path,'images/') ? asset($coach->photo_path) : asset('storage/'.$coach->photo_path)) : asset($loop->even ? 'images/coach-female-editorial.png' : 'images/sample-coach-profile.png'))
    <article class="result-card coach-card"><a class="result-image coach-card-image" href="{{ route('coaches.show',$coach) }}"><img src="{{ $photo }}" alt="{{ $coach->name }}"><span class="entity-image-label">COACH PROFILE</span></a>
        <div class="result-card-body">
            <div class="coach-card-tags"><div>@foreach(array_slice((array) $coach->fields, 0, 2) as $field)<span class="badge status">{{ $field }}</span>@endforeach<span class="badge">{{ $coach->main_prefecture }}</span></div>@if($coach->is_student)<span class="student-badge">学生</span>@endif</div>
            <h2><a href="{{ route('coaches.show',$coach) }}">{{ $coach->name }}</a></h2>
            <p class="coach-card-message">{{ Str::limit($coach->message ?: '指導に関するご相談をお待ちしています。', 64) }}</p>
            <p class="meta">{{ implode(' / ',(array)$coach->sports) }}</p><a class="entity-card-link" href="{{ route('coaches.show',$coach) }}">詳細を見る<span>→</span></a>
        </div>
    </article>
    @empty<div class="empty-state">条件に合う指導者がまだ登録されていません。</div>@endforelse
</div>{{ $coaches->links() }}
<section class="wide-cta">
    <div>
        <div class="eyebrow">BECOME A COACH</div>
        <h2>あなたの専門性を、地域の力に。</h2>
        <p>競技指導、トレーニング、メンタル、栄養、分析など、多様な専門家を募集しています。</p>
    </div><a class="btn" href="{{ route('register') }}">指導者として登録</a>
</section>
@endsection

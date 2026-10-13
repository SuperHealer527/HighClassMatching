@extends('layouts.admin')
@section('title','指導者審査')
@section('content')
<section class="admin-panel admin-list-panel">
    <header class="admin-list-heading">
        <div><div class="admin-eyebrow">COACH REVIEW QUEUE</div><h2>登録・公開審査</h2><p>指導者の登録情報、紹介者、確認書類、修正依頼を一か所で確認できます。</p></div>
        <a class="btn btn-outline-light" href="{{ route('admin.export','coaches') }}">CSV出力</a>
    </header>

    <form class="admin-filter-grid" method="get">
        <label><span>キーワード</span><input class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="氏名・都道府県・分野・競技"></label>
        <label><span>公開状態</span><select class="form-select" name="status"><option value="">すべて</option>@foreach(['pending','approved','rejected','suspended'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>@endforeach</select></label>
        <label><span>確認状態</span><select class="form-select" name="verification_status"><option value="">すべて</option>@foreach(['pending','verified','rejected'] as $status)<option value="{{ $status }}" {{ request('verification_status') === $status ? 'selected' : '' }}>{{ $status }}</option>@endforeach</select></label>
        <label><span>登録区分</span><select class="form-select" name="student"><option value="">すべて</option><option value="1" {{ request('student') === '1' ? 'selected' : '' }}>学生</option><option value="0" {{ request('student') === '0' ? 'selected' : '' }}>一般</option></select></label>
        <button class="btn btn-primary" type="submit">絞り込む</button>
    </form>

    <div class="coach-review-list">
        @forelse($coaches as $coach)
            @php
                $photo = $coach->photo_path ? (\Illuminate\Support\Str::startsWith($coach->photo_path, 'images/') ? asset($coach->photo_path) : asset('storage/'.$coach->photo_path)) : asset('images/sample-coach-profile.png');
                $documentCount = (int) filled($coach->identity_document_path) + (int) filled($coach->qualification_document_path);
            @endphp
            <article class="coach-review-row">
                <img src="{{ $photo }}" alt="" class="coach-review-avatar" onerror="this.onerror=null;this.src='{{ asset('images/sample-coach-profile.png') }}'">
                <div class="coach-review-person"><span>#{{ $coach->id }} / {{ $coach->main_prefecture }}</span><h3>{{ $coach->name }}</h3><p>{{ optional($coach->user)->email }}</p></div>
                <div class="coach-review-referrer"><span>紹介者</span><strong>{{ optional($coach->user)->referrer ?: '未登録' }}</strong><small>推薦者 {{ count((array) $coach->recommendations) }}名</small></div>
                <div class="coach-review-checks"><div><span>書類</span><strong>{{ $documentCount }}/2</strong></div><div><span>本人・資格</span><strong>{{ $coach->verification_status }}</strong></div><div><span>修正依頼</span><strong class="{{ $coach->open_revision_requests_count ? 'text-warning' : '' }}">{{ $coach->open_revision_requests_count }}</strong></div></div>
                <div class="coach-review-state"><span class="status-chip is-{{ $coach->status }}">{{ $coach->status }}</span><time>{{ optional($coach->profile_updated_at ?: $coach->updated_at)->format('Y.m.d') }}</time></div>
                <a class="btn btn-primary" href="{{ route('admin.coaches.show', $coach) }}">審査・修正依頼</a>
            </article>
        @empty
            <div class="admin-empty">条件に一致する指導者はいません。</div>
        @endforelse
    </div>
    <div class="admin-pagination">{{ $coaches->links() }}</div>
</section>
@endsection
